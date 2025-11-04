<template>
    <AuthenticatedLayout>
        <template #header>
            Bildirishnomalar
        </template>

        <div class="max-w-4xl mx-auto">
            <!-- Header with Stats -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Bildirishnomalar</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Sizning barcha bildirishnomalaringiz
                        </p>
                    </div>

                    <!-- Stats -->
                    <div class="flex gap-4">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-indigo-600">{{ stats.total }}</p>
                            <p class="text-xs text-gray-500">Jami</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-green-600">{{ stats.read }}</p>
                            <p class="text-xs text-gray-500">O'qilgan</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-orange-600">{{ stats.unread }}</p>
                            <p class="text-xs text-gray-500">O'qilmagan</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Actions -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                <div class="flex flex-col sm:flex-row gap-3 justify-between">
                    <!-- Filter Buttons -->
                    <div class="flex gap-2">
                        <Link 
                            :href="route('notifications.index')"
                            :class="[
                                'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                                !currentFilter 
                                    ? 'bg-indigo-600 text-white' 
                                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                            ]"
                        >
                            <div class="flex items-center space-x-2">
                                <Icon :size="16">
                                    <ListOutline />
                                </Icon>
                                <span>Barchasi</span>
                            </div>
                        </Link>
                        
                        <Link 
                            :href="`${route('notifications.index')}?filter=unread`"
                            :class="[
                                'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                                currentFilter === 'unread' 
                                    ? 'bg-orange-600 text-white' 
                                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                            ]"
                        >
                            <div class="flex items-center space-x-2">
                                <Icon :size="16">
                                    <MailUnreadOutline />
                                </Icon>
                                <span>O'qilmagan</span>
                            </div>
                        </Link>
                        
                        <Link 
                            :href="`${route('notifications.index')}?filter=read`"
                            :class="[
                                'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                                currentFilter === 'read' 
                                    ? 'bg-green-600 text-white' 
                                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                            ]"
                        >
                            <div class="flex items-center space-x-2">
                                <Icon :size="16">
                                    <MailOpenOutline />
                                </Icon>
                                <span>O'qilgan</span>
                            </div>
                        </Link>
                    </div>

                    <!-- Mark All as Read Button -->
                    <Link 
                        v-if="stats.unread > 0"
                        :href="route('notifications.mark-all-as-read')" 
                        method="post"
                        as="button"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors flex items-center space-x-2"
                    >
                        <Icon :size="16">
                            <CheckmarkDoneOutline />
                        </Icon>
                        <span>Hammasini o'qilgan deb belgilash</span>
                    </Link>
                </div>
            </div>

            <!-- Notifications List -->
            <div class="space-y-3">
                <div 
                    v-for="notification in notifications.data" 
                    :key="notification.id"
                    :class="[
                        'bg-white rounded-lg shadow-sm p-5 transition-all hover:shadow-md',
                        !notification.is_read && 'border-l-4 border-l-indigo-600 bg-indigo-50'
                    ]"
                >
                    <div class="flex items-start gap-4">
                        <!-- Icon -->
                        <div :class="[
                            'w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0',
                            getNotificationColor(notification.type)
                        ]">
                            <Icon :size="20" class="text-white">
                                <component :is="getNotificationIcon(notification.type)" />
                            </Icon>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h4 class="text-sm font-semibold text-gray-900">
                                            {{ notification.title }}
                                        </h4>
                                        <span 
                                            v-if="!notification.is_read"
                                            class="px-2 py-0.5 bg-indigo-100 text-indigo-700 text-xs font-medium rounded-full"
                                        >
                                            Yangi
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-2">
                                        {{ notification.message }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ formatDate(notification.created_at) }}
                                    </p>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center gap-2">
                                    <Link
                                        v-if="!notification.is_read"
                                        :href="route('notifications.mark-as-read', notification.id)"
                                        method="post"
                                        as="button"
                                        class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                        title="O'qilgan deb belgilash"
                                    >
                                        <Icon :size="18">
                                            <CheckmarkOutline />
                                        </Icon>
                                    </Link>

                                    <Link
                                        :href="route('notifications.destroy', notification.id)"
                                        method="delete"
                                        as="button"
                                        @click="confirmDelete"
                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                        title="O'chirish"
                                    >
                                        <Icon :size="18">
                                            <TrashOutline />
                                        </Icon>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div 
                    v-if="notifications.data.length === 0"
                    class="bg-white rounded-lg shadow-sm p-12 text-center"
                >
                    <Icon :size="64" class="mx-auto text-gray-300 mb-4">
                        <NotificationsOffOutline />
                    </Icon>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                        Bildirishnomalar yo'q
                    </h3>
                    <p class="text-sm text-gray-500">
                        Hozircha sizga bildirishnomalar yo'q
                    </p>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="notifications.data.length > 0" class="mt-6">
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-sm text-gray-600">
                            {{ notifications.from }}-{{ notifications.to }} / {{ notifications.total }} ta natija
                        </p>
                        
                        <div class="flex gap-2">
                            <template v-for="(link, index) in notifications.links" :key="index">
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
import { computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { Icon } from '@vicons/utils';
import {
    ListOutline,
    MailUnreadOutline,
    MailOpenOutline,
    CheckmarkDoneOutline,
    CheckmarkOutline,
    TrashOutline,
    NotificationsOffOutline,
    InformationCircleOutline,
    CheckmarkCircleOutline,
    WarningOutline,
    AlertCircleOutline,
    SchoolOutline,
    PersonOutline,
    DocumentTextOutline,
} from '@vicons/ionicons5';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    notifications: Object,
    stats: Object,
    filter: String,
});

const currentFilter = computed(() => props.filter || null);

// Route helper function
const route = (name, params = {}) => {
    const routes = {
        'notifications.index': '/notifications',
        'notifications.mark-as-read': (id) => `/notifications/${id}/mark-as-read`,
        'notifications.mark-all-as-read': '/notifications/mark-all-as-read',
        'notifications.destroy': (id) => `/notifications/${id}`,
    };
    
    if (typeof routes[name] === 'function') {
        return routes[name](params);
    }
    
    return routes[name] || '/notifications';
};

// Get notification icon based on type
const getNotificationIcon = (type) => {
    const icons = {
        info: InformationCircleOutline,
        success: CheckmarkCircleOutline,
        warning: WarningOutline,
        error: AlertCircleOutline,
        workload: SchoolOutline,
        user: PersonOutline,
        default: DocumentTextOutline,
    };
    return icons[type] || icons.default;
};

// Get notification color based on type
const getNotificationColor = (type) => {
    const colors = {
        info: 'bg-blue-600',
        success: 'bg-green-600',
        warning: 'bg-yellow-600',
        error: 'bg-red-600',
        workload: 'bg-indigo-600',
        user: 'bg-purple-600',
        default: 'bg-gray-600',
    };
    return colors[type] || colors.default;
};

// Format date
const formatDate = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diff = now - date;
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(diff / 3600000);
    const days = Math.floor(diff / 86400000);

    if (minutes < 1) return 'Hozirgina';
    if (minutes < 60) return `${minutes} daqiqa oldin`;
    if (hours < 24) return `${hours} soat oldin`;
    if (days < 7) return `${days} kun oldin`;
    
    return date.toLocaleDateString('uz-UZ', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

// Confirm delete
const confirmDelete = (e) => {
    if (!confirm('Bildirishnomani o\'chirmoqchimisiz?')) {
        e.preventDefault();
    }
};
</script>