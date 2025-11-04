<template>
    <AuthenticatedLayout>
        <template #header>
            Yangi Foydalanuvchi Qo'shish
        </template>

        <div class="max-w-3xl mx-auto">
            <!-- Back Button -->
            <div class="mb-6">
                <Link 
                    :href="route('admin.users.index')"
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
                    <h3 class="text-lg font-semibold text-gray-900">Foydalanuvchi Ma'lumotlari</h3>
                    <p class="text-sm text-gray-500 mt-1">Yangi foydalanuvchi yaratish uchun forma</p>
                </div>

                <form @submit.prevent="submit" class="p-6 space-y-6">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Ism <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.name"
                            type="text"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.name }"
                            placeholder="Ism va familiya"
                            required
                        />
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.email"
                            type="email"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.email }"
                            placeholder="example@email.com"
                            required
                        />
                        <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Telefon
                        </label>
                        <input
                            v-model="form.phone"
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="+998 90 123 45 67"
                        />
                        <p v-if="form.errors.phone" class="mt-1 text-sm text-red-600">{{ form.errors.phone }}</p>
                    </div>

                    <!-- Role -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Rol <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.role_id"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.role_id }"
                            required
                        >
                            <option value="">Rolni tanlang</option>
                            <option v-for="role in roles" :key="role.id" :value="role.id">
                                {{ role.display_name }}
                            </option>
                        </select>
                        <p v-if="form.errors.role_id" class="mt-1 text-sm text-red-600">{{ form.errors.role_id }}</p>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Parol <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent pr-10"
                                :class="{ 'border-red-500': form.errors.password }"
                                placeholder="Kamida 8 ta belgi"
                                required
                            />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                            >
                                <Icon :size="20">
                                    <component :is="showPassword ? EyeOffOutline : EyeOutline" />
                                </Icon>
                            </button>
                        </div>
                        <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</p>
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Parolni Tasdiqlash <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input
                                v-model="form.password_confirmation"
                                :type="showPasswordConfirm ? 'text' : 'password'"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent pr-10"
                                placeholder="Parolni qayta kiriting"
                                required
                            />
                            <button
                                type="button"
                                @click="showPasswordConfirm = !showPasswordConfirm"
                                class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                            >
                                <Icon :size="20">
                                    <component :is="showPasswordConfirm ? EyeOffOutline : EyeOutline" />
                                </Icon>
                            </button>
                        </div>
                    </div>

                    <!-- Is Active -->
                    <div class="flex items-center">
                        <input
                            v-model="form.is_active"
                            type="checkbox"
                            id="is_active"
                            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                        />
                        <label for="is_active" class="ml-2 block text-sm text-gray-700">
                            Faol foydalanuvchi
                        </label>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                        <Link
                            :href="route('admin.users.index')"
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
    EyeOutline,
    EyeOffOutline,
} from '@vicons/ionicons5';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    roles: Array,
});

// Route helper function
const route = (name, params = {}) => {
    const routes = {
        'admin.users.index': '/admin/users',
        'admin.users.store': '/admin/users',
    };
    return routes[name] || '/admin/users';
};

const showPassword = ref(false);
const showPasswordConfirm = ref(false);

const form = useForm({
    name: '',
    email: '',
    phone: '',
    role_id: '',
    password: '',
    password_confirmation: '',
    is_active: true,
});

const submit = () => {
    form.post(route('admin.users.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>