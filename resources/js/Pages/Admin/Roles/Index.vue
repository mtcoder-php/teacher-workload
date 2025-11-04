<template>
    <Head title="Rollar Boshqaruvi" />
    <AuthenticatedLayout>
       
        <div class="max-w-7xl mx-auto">
            <!-- Header with Actions -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Tizim Rollari</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Foydalanuvchi rollarini boshqarish
                        </p>
                    </div>

                    <Link 
                        :href="route('admin.roles.create')"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
                    >
                        <Icon :size="20" class="mr-2">
                            <AddOutline />
                        </Icon>
                        Yangi Rol
                    </Link>
                </div>
            </div>

            <!-- Search -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                <form @submit.prevent="applyFilters" class="flex gap-4">
                    <input
                        v-model="form.search"
                        type="text"
                        placeholder="Rol nomini qidiring..."
                        class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    />
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
                </form>
            </div>

            <!-- Roles Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    v-for="role in roles.data"
                    :key="role.id"
                    class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow"
                >
                    <!-- Role Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <Icon :size="24" :class="getRoleIconColor(role.name)">
                                    <ShieldCheckmarkOutline />
                                </Icon>
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ role.display_name }}
                                </h3>
                            </div>
                            <p class="text-xs font-mono text-gray-500 bg-gray-50 px-2 py-1 rounded inline-block">
                                {{ role.name }}
                            </p>
                        </div>
                        
                        <!-- Protected Badge -->
                        <span
                            v-if="isProtectedRole(role.name)"
                            class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded"
                            title="Tizim roli"
                        >
                            Himoyalangan
                        </span>
                    </div>

                    <!-- Description -->
                    <p class="text-sm text-gray-600 mb-4 min-h-[40px]">
                        {{ role.description || 'Tavsif yo\'q' }}
                    </p>

                    <!-- Stats -->
                    <div class="grid grid-cols-2 gap-4 mb-4 p-3 bg-gray-50 rounded-lg">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-indigo-600">{{ role.users_count }}</p>
                            <p class="text-xs text-gray-500">Foydalanuvchilar</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-green-600">{{ role.permissions_count }}</p>
                            <p class="text-xs text-gray-500">Ruxsatlar</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2 pt-4 border-t border-gray-200">
                        <!-- Permissions -->
                        <Link
                            :href="route('admin.roles.permissions', role.id)"
                            class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors text-sm font-medium"
                        >
                            <Icon :size="18" class="mr-1">
                                <KeyOutline />
                            </Icon>
                            Ruxsatlar
                        </Link>

                        <!-- Edit -->
                        <Link
                            :href="route('admin.roles.edit', role.id)"
                            class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                            title="Tahrirlash"
                        >
                            <Icon :size="20">
                                <CreateOutline />
                            </Icon>
                        </Link>

                        <!-- Delete -->
                        <button
                            @click="deleteRole(role)"
                            :disabled="isProtectedRole(role.name) || role.users_count > 0"
                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            title="O'chirish"
                        >
                            <Icon :size="20">
                                <TrashOutline />
                            </Icon>
                        </button>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="roles.data.length === 0" class="col-span-full">
                    <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                        <Icon :size="64" class="mx-auto text-gray-300 mb-4">
                            <ShieldCheckmarkOutline />
                        </Icon>
                        <p class="text-gray-500">Rollar topilmadi</p>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="roles.data.length > 0" class="mt-6">
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-sm text-gray-600">
                            {{ roles.from }}-{{ roles.to }} / {{ roles.total }} ta natija
                        </p>
                        
                        <div class="flex gap-2">
                            <template v-for="(link, index) in roles.links" :key="index">
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
import { Link, router, Head } from '@inertiajs/vue3';
import { Icon } from '@vicons/utils';
import {
    AddOutline,
    ShieldCheckmarkOutline,
    CreateOutline,
    TrashOutline,
    KeyOutline,
} from '@vicons/ionicons5';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    roles: Object,
    filters: Object,
});

// Route helper
const route = (name, params = {}) => {
    const routes = {
        'admin.roles.index': '/admin/roles',
        'admin.roles.create': '/admin/roles/create',
        'admin.roles.edit': (id) => `/admin/roles/${id}/edit`,
        'admin.roles.permissions': (id) => `/admin/roles/${id}/permissions`,
        'admin.roles.destroy': (id) => `/admin/roles/${id}`,
    };
    
    if (typeof routes[name] === 'function') {
        return routes[name](params);
    }
    
    return routes[name] || '/admin/roles';
};

const form = reactive({
    search: props.filters.search || '',
});

const protectedRoles = ['admin', 'oqituvchi', 'dekan', 'kafedra-mudiri'];

const isProtectedRole = (roleName) => {
    return protectedRoles.includes(roleName);
};

const getRoleIconColor = (roleName) => {
    const colors = {
        admin: 'text-purple-600',
        dekan: 'text-blue-600',
        'kafedra-mudiri': 'text-indigo-600',
        oqituvchi: 'text-green-600',
        nazoratchi: 'text-yellow-600',
    };
    return colors[roleName] || 'text-gray-600';
};

const applyFilters = () => {
    router.get(route('admin.roles.index'), form, {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilters = () => {
    form.search = '';
    applyFilters();
};

const deleteRole = (role) => {
    if (isProtectedRole(role.name)) {
        alert('Tizim rollarini o\'chirish mumkin emas!');
        return;
    }

    if (role.users_count > 0) {
        alert('Bu rolga biriktirilgan foydalanuvchilar mavjud!');
        return;
    }

    if (confirm(`${role.display_name} rolini o'chirishni tasdiqlaysizmi?`)) {
        router.delete(route('admin.roles.destroy', role.id), {
            preserveScroll: true,
        });
    }
};
</script>