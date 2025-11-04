<template>
    <AuthenticatedLayout>
        <template #header>
            Rol Ruxsatlarini Boshqarish
        </template>

        <div class="max-w-6xl mx-auto">
            <!-- Back Button -->
            <div class="mb-6">
                <Link 
                    :href="route('admin.roles.index')"
                    class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900"
                >
                    <Icon :size="20" class="mr-1">
                        <ArrowBackOutline />
                    </Icon>
                    Rollar ro'yxatiga qaytish
                </Link>
            </div>

            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <Icon :size="24" class="text-indigo-600">
                                <ShieldCheckmarkOutline />
                            </Icon>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900">{{ role.display_name }}</h3>
                            <p class="text-sm text-gray-500">
                                <span class="font-mono bg-gray-100 px-2 py-0.5 rounded">{{ role.name }}</span>
                                uchun ruxsatlar
                            </p>
                        </div>
                    </div>

                    <!-- Selected Count -->
                    <div class="text-right">
                        <p class="text-2xl font-bold text-indigo-600">{{ selectedCount }}</p>
                        <p class="text-sm text-gray-500">tanlangan ruxsat</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                <div class="flex flex-wrap gap-2">
                    <button
                        @click="selectAll"
                        class="px-4 py-2 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors text-sm font-medium"
                    >
                        <Icon :size="18" class="inline mr-1">
                            <CheckmarkDoneOutline />
                        </Icon>
                        Hammasini Tanlash
                    </button>
                    <button
                        @click="deselectAll"
                        class="px-4 py-2 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition-colors text-sm font-medium"
                    >
                        <Icon :size="18" class="inline mr-1">
                            <CloseCircleOutline />
                        </Icon>
                        Hammasini Bekor Qilish
                    </button>
                </div>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit">
                <!-- Permissions by Group -->
                <div class="space-y-6">
                    <div
                        v-for="(permissions, group) in allPermissions"
                        :key="group"
                        class="bg-white rounded-lg shadow-sm overflow-hidden"
                    >
                        <!-- Group Header -->
                        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 border-b border-indigo-100">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <Icon :size="24" :class="getGroupIconColor(group)">
                                        <component :is="getGroupIcon(group)" />
                                    </Icon>
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900 capitalize">
                                            {{ formatGroupName(group) }}
                                        </h4>
                                        <p class="text-sm text-gray-600">
                                            {{ permissions.length }} ta ruxsat
                                        </p>
                                    </div>
                                </div>

                                <!-- Group Actions -->
                                <div class="flex gap-2">
                                    <button
                                        type="button"
                                        @click="toggleGroup(group, true)"
                                        class="px-3 py-1 bg-white text-green-600 rounded-lg hover:bg-green-50 transition-colors text-sm"
                                    >
                                        Hammasini tanlash
                                    </button>
                                    <button
                                        type="button"
                                        @click="toggleGroup(group, false)"
                                        class="px-3 py-1 bg-white text-red-600 rounded-lg hover:bg-red-50 transition-colors text-sm"
                                    >
                                        Bekor qilish
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Permissions Grid -->
                        <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <label
                                v-for="permission in permissions"
                                :key="permission.id"
                                class="flex items-start p-4 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer transition-colors border-2"
                                :class="form.permissions.includes(permission.id) 
                                    ? 'border-indigo-500 bg-indigo-50' 
                                    : 'border-transparent'"
                            >
                                <input
                                    type="checkbox"
                                    :value="permission.id"
                                    v-model="form.permissions"
                                    class="mt-1 w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                />
                                <div class="ml-3 flex-1">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ permission.display_name }}
                                    </p>
                                    <p class="text-xs text-gray-500 font-mono mt-1">
                                        {{ permission.name }}
                                    </p>
                                    <p v-if="permission.description" class="text-xs text-gray-600 mt-1">
                                        {{ permission.description }}
                                    </p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="Object.keys(allPermissions).length === 0" class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <Icon :size="64" class="mx-auto text-gray-300 mb-4">
                        <KeyOutline />
                    </Icon>
                    <p class="text-gray-500">Ruxsatlar topilmadi</p>
                </div>

                <!-- Save Actions -->
                <div class="sticky bottom-0 bg-white border-t border-gray-200 shadow-lg rounded-t-lg mt-6">
                    <div class="p-6 flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            <p class="font-medium">
                                <span class="text-indigo-600 text-lg">{{ selectedCount }}</span> ta ruxsat tanlandi
                            </p>
                            <p class="text-xs text-gray-500 mt-1">O'zgarishlar saqlanadi</p>
                        </div>

                        <div class="flex items-center gap-3">
                            <Link
                                :href="route('admin.roles.index')"
                                class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium"
                            >
                                Bekor qilish
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center font-medium"
                            >
                                <Icon v-if="form.processing" :size="20" class="mr-2 animate-spin">
                                    <RefreshOutline />
                                </Icon>
                                <Icon v-else :size="20" class="mr-2">
                                    <SaveOutline />
                                </Icon>
                                {{ form.processing ? 'Saqlanmoqda...' : 'Ruxsatlarni Saqlash' }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
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
    ShieldCheckmarkOutline,
    CheckmarkDoneOutline,
    CloseCircleOutline,
    KeyOutline,
    PeopleOutline,
    SchoolOutline,
    BookOutline,
    DocumentTextOutline,
    SettingsOutline,
} from '@vicons/ionicons5';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    role: Object,
    allPermissions: Object,
    rolePermissions: Array,
});

// Route helper
const route = (name, params = {}) => {
    const routes = {
        'admin.roles.index': '/admin/roles',
        'admin.roles.permissions.update': (id) => `/admin/roles/${id}/permissions`,
    };
    
    if (typeof routes[name] === 'function') {
        return routes[name](params);
    }
    
    return routes[name] || '/admin/roles';
};

const form = useForm({
    permissions: [...props.rolePermissions],
});

const selectedCount = computed(() => form.permissions.length);

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

const getGroupIcon = (group) => {
    const icons = {
        users: PeopleOutline,
        faculties: SchoolOutline,
        departments: SchoolOutline,
        teachers: PeopleOutline,
        subjects: BookOutline,
        groups: PeopleOutline,
        workloads: DocumentTextOutline,
        reports: DocumentTextOutline,
        settings: SettingsOutline,
    };
    return icons[group] || KeyOutline;
};

const getGroupIconColor = (group) => {
    const colors = {
        users: 'text-purple-600',
        faculties: 'text-blue-600',
        departments: 'text-indigo-600',
        teachers: 'text-green-600',
        subjects: 'text-yellow-600',
        groups: 'text-pink-600',
        workloads: 'text-orange-600',
        reports: 'text-teal-600',
        settings: 'text-gray-600',
    };
    return colors[group] || 'text-gray-600';
};

const selectAll = () => {
    const allPermissionIds = [];
    Object.values(props.allPermissions).forEach(permissions => {
        permissions.forEach(permission => {
            allPermissionIds.push(permission.id);
        });
    });
    form.permissions = allPermissionIds;
};

const deselectAll = () => {
    form.permissions = [];
};

const toggleGroup = (group, select) => {
    const groupPermissions = props.allPermissions[group];
    
    if (select) {
        // Add all permissions from this group
        groupPermissions.forEach(permission => {
            if (!form.permissions.includes(permission.id)) {
                form.permissions.push(permission.id);
            }
        });
    } else {
        // Remove all permissions from this group
        const groupPermissionIds = groupPermissions.map(p => p.id);
        form.permissions = form.permissions.filter(id => !groupPermissionIds.includes(id));
    }
};

const submit = () => {
    form.post(route('admin.roles.permissions.update', props.role.id), {
        preserveScroll: true,
    });
};
</script>