<template>
    <AuthenticatedLayout>
        <template #header>
            Ruxsatlar Boshqaruvi
        </template>

        <div class="max-w-7xl mx-auto">
            <!-- Header with Actions -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Tizim Ruxsatlari</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Foydalanuvchi ruxsatlarini boshqarish
                        </p>
                    </div>

                    <Link 
                        :href="route('admin.permissions.create')"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
                    >
                        <Icon :size="20" class="mr-2">
                            <AddOutline />
                        </Icon>
                        Yangi Ruxsat
                    </Link>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                <form @submit.prevent="applyFilters" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Search -->
                    <div class="md:col-span-2">
                        <input
                            v-model="form.search"
                            type="text"
                            placeholder="Ruxsat nomini qidiring..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        />
                    </div>

                    <!-- Group Filter -->
                    <div class="flex gap-2">
                        <select
                            v-model="form.group"
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        >
                            <option value="">Barcha guruhlar</option>
                            <option v-for="group in groups" :key="group" :value="group">
                                {{ formatGroupName(group) }}
                            </option>
                        </select>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
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

            <!-- Permissions Table -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ruxsat
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Guruh
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tavsif
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Rollar
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Amallar
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="permission in permissions.data" :key="permission.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ permission.display_name }}
                                        </p>
                                        <p class="text-xs font-mono text-gray-500 mt-1">
                                            {{ permission.name }}
                                        </p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full"
                                        :class="getGroupBadgeClass(permission.group)">
                                        {{ formatGroupName(permission.group) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-600 line-clamp-2">
                                        {{ permission.description || '-' }}
                                    </p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs font-medium rounded-full">
                                        {{ permission.roles_count }} ta rol
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Edit -->
                                        <Link
                                            :href="route('admin.permissions.edit', permission.id)"
                                            class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                            title="Tahrirlash"
                                        >
                                            <Icon :size="18">
                                                <CreateOutline />
                                            </Icon>
                                        </Link>

                                        <!-- Delete -->
                                        <button
                                            @click="deletePermission(permission)"
                                            :disabled="permission.roles_count > 0"
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
                            <tr v-if="permissions.data.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <Icon :size="64" class="mx-auto text-gray-300 mb-4">
                                        <KeyOutline />
                                    </Icon>
                                    <p class="text-gray-500">Ruxsatlar topilmadi</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="permissions.data.length > 0" class="px-6 py-4 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-sm text-gray-600">
                            {{ permissions.from }}-{{ permissions.to }} / {{ permissions.total }} ta natija
                        </p>
                        
                        <div class="flex gap-2">
                            <template v-for="(link, index) in permissions.links" :key="index">
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
import { reactive } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { Icon } from '@vicons/utils';
import {
    AddOutline,
    CreateOutline,
    TrashOutline,
    KeyOutline,
} from '@vicons/ionicons5';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    permissions: Object,
    groups: Array,
    filters: Object,
});

// Route helper
const route = (name, params = {}) => {
    const routes = {
        'admin.permissions.index': '/admin/permissions',
        'admin.permissions.create': '/admin/permissions/create',
        'admin.permissions.edit': (id) => `/admin/permissions/${id}/edit`,
        'admin.permissions.destroy': (id) => `/admin/permissions/${id}`,
    };
    
    if (typeof routes[name] === 'function') {
        return routes[name](params);
    }
    
    return routes[name] || '/admin/permissions';
};

const form = reactive({
    search: props.filters.search || '',
    group: props.filters.group || '',
});

const formatGroupName = (group) => {
    if (!group) return '—'; // yoki "" yoki "Noma'lum"
    
    const names = {
        users: 'Foydalanuvchilar',
        faculties: 'Fakultetlar',
        departments: 'Kafedralar',
        teachers: 'O‘qituvchilar',
        subjects: 'Fanlar',
        groups: 'Guruhlar',
        workloads: 'Yuklamalar',
        reports: 'Hisobotlar',
        settings: 'Sozlamalar',
    };

    const formatted = names[group];
    return formatted || group.charAt(0).toUpperCase() + group.slice(1);
};


const getGroupBadgeClass = (group) => {
    const classes = {
        users: 'bg-purple-100 text-purple-800',
        faculties: 'bg-blue-100 text-blue-800',
        departments: 'bg-indigo-100 text-indigo-800',
        teachers: 'bg-green-100 text-green-800',
        subjects: 'bg-yellow-100 text-yellow-800',
        groups: 'bg-pink-100 text-pink-800',
        workloads: 'bg-orange-100 text-orange-800',
        reports: 'bg-teal-100 text-teal-800',
        settings: 'bg-gray-100 text-gray-800',
    };
    return classes[group] || 'bg-gray-100 text-gray-800';
};

const applyFilters = () => {
    router.get(route('admin.permissions.index'), form, {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilters = () => {
    form.search = '';
    form.group = '';
    applyFilters();
};

const deletePermission = (permission) => {
    if (permission.roles_count > 0) {
        alert('Bu ruxsat rollarga biriktirilgan!');
        return;
    }

    if (confirm(`${permission.display_name} ruxsatini o'chirishni tasdiqlaysizmi?`)) {
        router.delete(route('admin.permissions.destroy', permission.id), {
            preserveScroll: true,
        });
    }
};
</script>