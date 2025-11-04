<template>
    <AuthenticatedLayout>
        <template #header>
            Ruxsatni Tahrirlash
        </template>

        <div class="max-w-3xl mx-auto">
            <!-- Back Button -->
            <div class="mb-6">
                <Link 
                    :href="route('admin.permissions.index')"
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
                    <h3 class="text-lg font-semibold text-gray-900">Ruxsat Ma'lumotlari</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ permission.display_name}} ma'lumotlarini tahrirlash</p>
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
                            placeholder="Foydalanuvchilarni Ko'rish"
                            required
                        />
                        <p class="mt-1 text-xs text-gray-500">Foydalanuvchilarga ko'rinadigan nom</p>
                        <p v-if="form.errors.display_name" class="mt-1 text-sm text-red-600">
                            {{ form.errors.display_name }}
                        </p>
                    </div>

                    <!-- System Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Tizim Nomi <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.name"
                            type="text"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent font-mono"
                            :class="{ 'border-red-500': form.errors.name }"
                            placeholder="users.view"
                            pattern="[a-z0-9._-]+"
                            required
                        />
                        <p class="mt-1 text-xs text-gray-500">
                            Format: <code class="bg-gray-100 px-1 rounded">guruh.amal</code>
                        </p>
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <!-- Group -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Guruh <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-2">
                            <select
                                v-model="groupType"
                                class="w-32 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            >
                                <option value="existing">Mavjud</option>
                                <option value="new">Yangi</option>
                            </select>
                            
                            <select
                                v-if="groupType === 'existing'"
                                v-model="form.group"
                                class="flex-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.group }"
                                required
                            >
                                <option value="">Guruhni tanlang</option>
                                <option v-for="group in existingGroups" :key="group" :value="group">
                                    {{ formatGroupName(group) }}
                                </option>
                            </select>

                            <input
                                v-else
                                v-model="form.group"
                                type="text"
                                class="flex-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.group }"
                                placeholder="yangi-guruh"
                                pattern="[a-z0-9-]+"
                                required
                            />
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            Ruxsatlarni guruhlash uchun kategoriya
                        </p>
                        <p v-if="form.errors.group" class="mt-1 text-sm text-red-600">
                            {{ form.errors.group }}
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
                            placeholder="Bu ruxsat haqida qisqacha ma'lumot..."
                        ></textarea>
                        <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                            {{ form.errors.description }}
                        </p>
                    </div>

                    <!-- Roles Info -->
                    <div v-if="permission.roles_count > 0" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <Icon :size="20" class="text-yellow-600 mt-0.5 mr-3 flex-shrink-0">
                                <WarningOutline />
                            </Icon>
                            <div class="text-sm text-yellow-800">
                                <p class="font-medium mb-1">Eslatma</p>
                                <p class="text-yellow-700">
                                    Bu ruxsat <strong>{{ permission.roles_count }} ta rolga</strong> biriktirilgan. 
                                    O'zgartirishlar barcha rollarga ta'sir qiladi.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                        <Link
                            :href="route('admin.permissions.index')"
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
import { ref } from 'vue';
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
    permission: Object,
    existingGroups: Array,
});

// Route helper
const route = (name, params = {}) => {
    const routes = {
        'admin.permissions.index': '/admin/permissions',
        'admin.permissions.update': (id) => `/admin/permissions/${id}`,
    };
    
    if (typeof routes[name] === 'function') {
        return routes[name](params);
    }
    
    return routes[name] || '/admin/permissions';
};

const groupType = ref(props.existingGroups.includes(props.permission.group) ? 'existing' : 'new');

const form = useForm({
    display_name: props.permission.display_name,
    name: props.permission.name,
    group: props.permission.group,
    description: props.permission.description || '',
});

const formatGroupName = (group) => {
    const names = {
        users: 'Foydalanuvchilar',
        faculties: 'Fakultetlar',
        departments: 'Kafedralar',
        teachers: 'O\'qituvchilar',
        subjects: 'Fanlar',
        groups: 'Guruhlar',
        workloads: 'Yuklamalar',
        reports: 'Hisobotlar',
        settings: 'Sozlamalar',
    };
    return names[group] || group.charAt(0).toUpperCase() + group.slice(1);
};

const submit = () => {
    form.put(route('admin.permissions.update', props.permission.id), {
        preserveScroll: true,
    });
};
</script>