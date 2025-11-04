<template>
    <AuthenticatedLayout>
        <template #header>
            Rolni Tahrirlash
        </template>

        <div class="max-w-3xl mx-auto">
            <!-- Back Button -->
            <div class="mb-6">
                <Link 
                    :href="route('admin.roles.index')"
                    class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900"
                >
                    <Icon :size="20" class="mr-1">
                        <ArrowBackOutline />
                    </Icon>
                    Orqaga qaytish
                </Link>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Rol Ma'lumotlari</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ role.display_name }} ma'lumotlarini tahrirlash</p>
                </div>

                <form @submit.prevent="submit" class="p-6 space-y-6">
                    <!-- Display Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Ko'rinadigan Nom <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.display_name"
                            type="text"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.display_name }"
                            placeholder="Administrator"
                            required
                        />
                        <p class="mt-1 text-xs text-gray-500">Foydalanuvchilarga ko'rinadigan nom</p>
                        <p v-if="form.errors.display_name" class="mt-1 text-sm text-red-600">
                            {{ form.errors.display_name }}
                        </p>
                    </div>

                    <!-- System Name (Read-only for protected roles) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Tizim Nomi <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.name"
                            type="text"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent font-mono"
                            :class="{ 
                                'border-red-500': form.errors.name,
                                'bg-gray-50 cursor-not-allowed': isProtectedRole
                            }"
                            :readonly="isProtectedRole"
                            placeholder="administrator"
                            pattern="[a-z0-9-]+"
                            required
                        />
                        <p class="mt-1 text-xs text-gray-500">
                            <span v-if="isProtectedRole" class="text-orange-600 font-medium">
                                ⚠️ Tizim roli - nom o'zgartirilmaydi
                            </span>
                            <span v-else>
                                Faqat kichik harflar, raqamlar va tire (-) ishlatiladi
                            </span>
                        </p>
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Tavsif
                        </label>
                        <textarea
                            v-model="form.description"
                            rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="Bu rol haqida qisqacha ma'lumot..."
                        ></textarea>
                        <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                            {{ form.errors.description }}
                        </p>
                    </div>

                    <!-- Protected Role Warning -->
                    <div v-if="isProtectedRole" class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <Icon :size="20" class="text-orange-600 mt-0.5 mr-3 flex-shrink-0">
                                <WarningOutline />
                            </Icon>
                            <div class="text-sm text-orange-800">
                                <p class="font-medium mb-1">Tizim Roli</p>
                                <p class="text-orange-700">
                                    Bu tizim roli bo'lgani uchun tizim nomi o'zgartirilmaydi. 
                                    Faqat ko'rinadigan nom va tavsifni tahrirlashingiz mumkin.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                        <Link
                            :href="route('admin.roles.index')"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors"
                        >
                            Bekor qilish
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
                        >
                            <Icon v-if="form.processing" :size="20" class="mr-2 animate-spin">
                                <RefreshOutline />
                            </Icon>
                            <Icon v-else :size="20" class="mr-2">
                                <SaveOutline />
                            </Icon>
                            {{ form.processing ? 'Saqlanmoqda...' : 'Saqlash' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import { Icon } from '@vicons/utils';
import {
    ArrowBackOutline,
    SaveOutline,
    RefreshOutline,
    WarningOutline,
} from '@vicons/ionicons5';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    role: Object,
});

// Route helper
const route = (name, params = {}) => {
    const routes = {
        'admin.roles.index': '/admin/roles',
        'admin.roles.update': (id) => `/admin/roles/${id}`,
    };
    
    if (typeof routes[name] === 'function') {
        return routes[name](params);
    }
    
    return routes[name] || '/admin/roles';
};

const protectedRoles = ['admin', 'oqituvchi', 'dekan', 'kafedra-mudiri'];
const isProtectedRole = computed(() => protectedRoles.includes(props.role.name));

const form = useForm({
    display_name: props.role.display_name,
    name: props.role.name,
    description: props.role.description || '',
});

const submit = () => {
    form.put(route('admin.roles.update', props.role.id), {
        preserveScroll: true,
    });
};
</script>