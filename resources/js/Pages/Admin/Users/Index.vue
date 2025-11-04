<template>
    <AuthenticatedLayout>
        <template #header>
            Foydalanuvchilar Boshqaruvi
        </template>

        <div class="max-w-7xl mx-auto">
            <!-- Header with Actions -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Foydalanuvchilar</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Tizim foydalanuvchilarini boshqarish
                        </p>
                    </div>

                    <Link 
                        :href="route('admin.users.create')"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
                    >
                        <Icon :size="20" class="mr-2">
                            <AddOutline />
                        </Icon>
                        Yangi Foydalanuvchi
                    </Link>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                <form @submit.prevent="applyFilters" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Qidiruv</label>
                        <input
                            v-model="form.search"
                            type="text"
                            placeholder="Ism, email, telefon..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        />
                    </div>

                    <!-- Role Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
                        <select
                            v-model="form.role_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        >
                            <option value="">Barcha rollar</option>
                            <option v-for="role in roles" :key="role.id" :value="role.id">
                                {{ role.display_name }}
                            </option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select
                            v-model="form.is_active"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        >
                            <option value="">Barchasi</option>
                            <option value="true">Faol</option>
                            <option value="false">Nofaol</option>
                        </select>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-end gap-2">
                        <button
                            type="submit"
                            class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
                        >
                            Qidirish
                        </button>
                        <button
                            type="button"
                            @click="resetFilters"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors"
                        >
                            Tozalash
                        </button>
                    </div>
                </form>
            </div>

            <!-- Users Table -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Foydalanuvchi
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Rol
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Telefon
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Qo'shilgan
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Amallar
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-semibold">
                                            {{ user.name.charAt(0).toUpperCase() }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                                            <div class="text-sm text-gray-500">{{ user.email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full"
                                        :class="getRoleBadgeClass(user.role?.name)">
                                        {{ user.role?.display_name || 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ user.phone || '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full"
                                        :class="user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                        {{ user.is_active ? 'Faol' : 'Nofaol' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ formatDate(user.created_at) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Toggle Status -->
                                        <button
                                            @click="toggleStatus(user)"
                                            :disabled="user.id === $page.props.auth.user.id"
                                            class="p-2 rounded-lg transition-colors"
                                            :class="user.is_active 
                                                ? 'text-yellow-600 hover:bg-yellow-50' 
                                                : 'text-green-600 hover:bg-green-50'"
                                            :title="user.is_active ? 'Bloklash' : 'Faollashtirish'"
                                        >
                                            <Icon :size="18">
                                                <component :is="user.is_active ? LockClosedOutline : LockOpenOutline" />
                                            </Icon>
                                        </button>

                                        <!-- Edit -->
                                        <Link
                                            :href="route('admin.users.edit', user.id)"
                                            class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                            title="Tahrirlash"
                                        >
                                            <Icon :size="18">
                                                <CreateOutline />
                                            </Icon>
                                        </Link>

                                        <!-- Delete -->
                                        <button
                                            @click="deleteUser(user)"
                                            :disabled="user.id === $page.props.auth.user.id"
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                            title="O'chirish"
                                        >
                                            <Icon :size="18">
                                                <TrashOutline />
                                            </Icon>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Empty State -->
                            <tr v-if="users.data.length === 0">
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <Icon :size="64" class="mx-auto text-gray-300 mb-4">
                                        <PeopleOutline />
                                    </Icon>
                                    <p class="text-gray-500">Foydalanuvchilar topilmadi</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="users.data.length > 0" class="px-6 py-4 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-sm text-gray-600">
                            {{ users.from }}-{{ users.to }} / {{ users.total }} ta natija
                        </p>
                        
                        <div class="flex gap-2">
                            <template v-for="(link, index) in users.links" :key="index">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    :class="[
                                        'px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                                        link.active 
                                            ? 'bg-indigo-600 text-white' 
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                    ]"
                                    v-html="link.label"
                                />
                                <span
                                    v-else
                                    :class="[
                                        'px-3 py-2 rounded-lg text-sm font-medium',
                                        'bg-gray-50 text-gray-400 cursor-not-allowed'
                                    ]"
                                    v-html="link.label"
                                />
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { Icon } from '@vicons/utils';
import {
    AddOutline,
    PeopleOutline,
    CreateOutline,
    TrashOutline,
    LockClosedOutline,
    LockOpenOutline,
} from '@vicons/ionicons5';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    users: Object,
    roles: Array,
    filters: Object,
});

// Route helper function
const route = (name, params = {}) => {
    const routes = {
        'admin.users.index': '/admin/users',
        'admin.users.create': '/admin/users/create',
        'admin.users.edit': (id) => `/admin/users/${id}/edit`,
        'admin.users.toggle-status': (id) => `/admin/users/${id}/toggle-status`,
        'admin.users.destroy': (id) => `/admin/users/${id}`,
    };
    
    if (typeof routes[name] === 'function') {
        return routes[name](params);
    }
    
    return routes[name] || '/admin/users';
};

const form = reactive({
    search: props.filters.search || '',
    role_id: props.filters.role_id || '',
    is_active: props.filters.is_active || '',
});

const applyFilters = () => {
    router.get(route('admin.users.index'), form, {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilters = () => {
    form.search = '';
    form.role_id = '';
    form.is_active = '';
    applyFilters();
};

const getRoleBadgeClass = (roleName) => {
    const classes = {
        admin: 'bg-purple-100 text-purple-800',
        dekan: 'bg-blue-100 text-blue-800',
        'kafedra-mudiri': 'bg-indigo-100 text-indigo-800',
        oqituvchi: 'bg-green-100 text-green-800',
        nazoratchi: 'bg-yellow-100 text-yellow-800',
    };
    return classes[roleName] || 'bg-gray-100 text-gray-800';
};

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('uz-UZ', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const toggleStatus = (user) => {
    if (user.id === props.$page?.props?.auth?.user?.id) {
        return;
    }

    if (confirm(`${user.name}ni ${user.is_active ? 'bloklash' : 'faollashtirish'}ni tasdiqlaysizmi?`)) {
        router.post(route('admin.users.toggle-status', user.id), {}, {
            preserveScroll: true,
        });
    }
};

const deleteUser = (user) => {
    if (user.id === props.$page?.props?.auth?.user?.id) {
        return;
    }

    if (confirm(`${user.name}ni o'chirishni tasdiqlaysizmi? Bu amalni qaytarib bo'lmaydi!`)) {
        router.delete(route('admin.users.destroy', user.id), {
            preserveScroll: true,
        });
    }
};
</script>