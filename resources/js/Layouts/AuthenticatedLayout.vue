<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Sidebar -->
        <div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-xl transition-transform duration-300 ease-in-out lg:translate-x-0 flex flex-col">
            <!-- Logo - Icon clickable, text static -->
            <div class="flex items-center justify-center h-16 px-4 bg-indigo-600 flex-shrink-0">
                <Link href="/dashboard" class="hover:scale-110 transition-transform">
                    <Icon :size="28" class="text-white mr-2">
                        <SchoolOutline />
                    </Icon>
                </Link>
                <h1 class="text-xl font-bold text-white">Yuklama Tizimi</h1>
            </div>

            <!-- Navigation - Scrollable -->
            <nav class="flex-1 mt-6 px-4 space-y-2 overflow-y-auto pb-6">
                <!-- Dashboard -->
                <NavLink href="/dashboard" :active="$page.url === '/dashboard'">
                    <Icon :size="20">
                        <GridOutline />
                    </Icon>
                    <span>Dashboard</span>
                </NavLink>

                <!-- Fakultetlar - Bino icon -->
                <NavLink href="/faculties" :active="$page.url.startsWith('/faculties')">
                    <Icon :size="20">
                        <LayersOutline />
                    </Icon>
                    <span>Fakultetlar</span>
                </NavLink>

                <!-- Kafedralar - Kutubxona icon -->
                <NavLink href="/departments" :active="$page.url.startsWith('/departments')">
                    <Icon :size="20">
                        <CopyOutline />
                    </Icon>
                    <span>Kafedralar</span>
                </NavLink>

                <!-- Yo'nalishlar - Kompas icon -->
                <NavLink href="/directions" :active="$page.url.startsWith('/directions')">
                    <Icon :size="20">
                        <CompassOutline />
                    </Icon>
                    <span>Yo'nalishlar</span>
                </NavLink>


                   <!-- Guruhlar - Guruh odamlar icon -->
                <NavLink href="/groups" :active="$page.url.startsWith('/groups')">
                    <Icon :size="20">
                        <PeopleCircleOutline />
                    </Icon>
                    <span>Guruhlar</span>
                </NavLink>

                <!-- O'qituvchilar - Odamlar icon -->
                <NavLink href="/teachers" :active="$page.url.startsWith('/teachers')">
                    <Icon :size="20">
                        <PeopleOutline />
                    </Icon>
                    <span>O'qituvchilar</span>
                </NavLink>

                <!-- Fanlar - Kitob icon -->
                <NavLink href="/subjects" :active="$page.url.startsWith('/subjects')">
                    <Icon :size="20">
                        <BookOutline />
                    </Icon>
                    <span>Fanlar</span>
                </NavLink>


                <!-- O'quv Yillari - Soat/vaqt icon -->
                <NavLink href="/academic-years" :active="$page.url.startsWith('/academic-years')">
                    <Icon :size="20">
                        <CalendarOutline />
                    </Icon>
                    <span>O'quv Yillari</span>
                </NavLink>


                <!-- Yuklamalar - Clipboard hujjat icon -->
                <NavLink href="/workloads" :active="$page.url.startsWith('/workloads')">
                    <Icon :size="20">
                        <DocumentsOutline />
                    </Icon>
                    <span>Yuklamalar</span>
                </NavLink>

                <!-- Hisobotlar - Statistika/grafik icon -->
                <NavLink href="/reports" :active="$page.url.startsWith('/reports')">
                    <Icon :size="20">
                        <BarChartOutline />
                    </Icon>
                    <span>Hisobotlar</span>
                </NavLink>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="lg:pl-64">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm h-16">
                <div class="flex items-center justify-between px-4 py-3">
                    <!-- Mobile Menu Button -->
                    <button @click.stop="sidebarOpen = !sidebarOpen" type="button"
                        class="lg:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100 active:bg-gray-200">
                        <Icon :size="24">
                            <MenuOutline />
                        </Icon>
                    </button>

                    <!-- Page Title -->
                    <h2 class="text-xl font-semibold text-gray-800">
                        <slot name="header">Dashboard</slot>
                    </h2>

                    <div class="flex items-center gap-2">
                        <!-- Notifications Bell -->
                        <div class="relative">
                            <button @click.stop="toggleNotifications" type="button" ref="notificationsButton"
                                class="relative p-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors">
                                <Icon :size="24">
                                    <NotificationsOutline />
                                </Icon>

                                <!-- Unread Badge -->
                                <span v-if="unreadCount > 0"
                                    class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center">
                                    {{ unreadCount > 9 ? '9+' : unreadCount }}
                                </span>
                            </button>

                            <!-- Notifications Dropdown -->
                            <div v-show="notificationsOpen" ref="notificationsDropdown"
                                class="fixed md:absolute right-2 md:right-0 left-2 md:left-auto top-16 md:top-auto md:mt-2 w-auto md:w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50 max-h-[calc(100vh-5rem)] md:max-h-96 overflow-hidden flex flex-col">
                                <!-- Header -->
                                <div
                                    class="px-4 py-3 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white z-10">
                                    <h3 class="font-semibold text-gray-900 text-base md:text-sm">Bildirishnomalar</h3>
                                    <div class="flex items-center gap-2">
                                        <span v-if="unreadCount > 0"
                                            class="px-2 py-1 bg-red-100 text-red-700 text-xs font-medium rounded-full">
                                            {{ unreadCount }} yangi
                                        </span>
                                        <!-- Close button for mobile -->
                                        <button @click="notificationsOpen = false"
                                            class="md:hidden p-1 hover:bg-gray-100 rounded-full transition-colors">
                                            <Icon :size="20" class="text-gray-500">
                                                <CloseOutline />
                                            </Icon>
                                        </button>
                                    </div>
                                </div>

                                <!-- Notifications List -->
                                <div class="overflow-y-auto flex-1">
                                    <Link v-for="notification in recentNotifications" :key="notification.id"
                                        href="/notifications" :class="[
                                            'block px-4 py-3 hover:bg-gray-50 active:bg-gray-100 border-b border-gray-100 transition-colors',
                                            !notification.is_read && 'bg-indigo-50'
                                        ]" @click="notificationsOpen = false">
                                        <div class="flex items-start gap-3">
                                            <div :class="[
                                                'w-10 h-10 md:w-8 md:h-8 rounded-full flex items-center justify-center flex-shrink-0',
                                                getNotificationColor(notification.type)
                                            ]">
                                                <Icon :size="18" class="md:w-4 md:h-4 text-white">
                                                    <component :is="getNotificationIcon(notification.type)" />
                                                </Icon>
                                            </div>

                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-start justify-between gap-2">
                                                    <p
                                                        class="text-sm md:text-sm font-medium text-gray-900 line-clamp-2 md:line-clamp-1">
                                                        {{ notification.title }}
                                                    </p>
                                                    <span v-if="!notification.is_read"
                                                        class="w-2 h-2 bg-indigo-600 rounded-full flex-shrink-0 mt-1.5" />
                                                </div>
                                                <p class="text-xs md:text-xs text-gray-600 line-clamp-2 mt-1">
                                                    {{ notification.message }}
                                                </p>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ formatRelativeTime(notification.created_at) }}
                                                </p>
                                            </div>
                                        </div>
                                    </Link>

                                    <!-- Empty State -->
                                    <div v-if="!recentNotifications || recentNotifications.length === 0"
                                        class="px-4 py-12 md:py-8 text-center">
                                        <Icon :size="64" class="md:w-12 md:h-12 mx-auto text-gray-300 mb-3 md:mb-2">
                                            <NotificationsOffOutline />
                                        </Icon>
                                        <p class="text-base md:text-sm text-gray-500">
                                            Bildirishnomalar yo'q
                                        </p>
                                    </div>
                                </div>

                                <!-- Footer -->
                                <div class="px-4 py-3 border-t border-gray-200 sticky bottom-0 bg-white">
                                    <Link href="/notifications"
                                        class="block text-center text-sm font-medium text-indigo-600 hover:text-indigo-700 py-2 md:py-0"
                                        @click="notificationsOpen = false">
                                        Barchasini ko'rish
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- User Menu -->
                        <div class="relative">
                            <button @click.stop="toggleUserMenu" type="button" ref="userMenuButton"
                                class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100">
                                <!-- Avatar - Rasm yoki Harf -->
                                <div
                                    class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center text-white font-semibold overflow-hidden">
                                    <img v-if="$page.props.auth?.user?.avatar_url"
                                        :src="$page.props.auth.user.avatar_url" :alt="$page.props.auth?.user?.name"
                                        class="w-full h-full object-cover" />
                                    <span v-else>
                                        {{ $page.props.auth?.user?.name?.charAt(0).toUpperCase() || 'U' }}
                                    </span>
                                </div>

                                <div class="hidden md:block text-left">
                                    <p class="text-sm font-medium text-gray-700">{{ $page.props.auth?.user?.name ||
                                        'User'
                                    }}</p>
                                    <p class="text-xs text-gray-500">{{ $page.props.auth?.user?.role?.display_name ||
                                        'Guest' }}</p>
                                </div>
                                <Icon :size="16" class="text-gray-400">
                                    <ChevronDownOutline />
                                </Icon>
                            </button>

                            <!-- Dropdown Menu -->
                            <div v-show="userMenuOpen" ref="userMenuDropdown"
                                class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg py-1 z-50 border border-gray-200">
                                <!-- Profile -->
                                <Link href="/profile"
                                    class="flex items-center space-x-2 w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    @click="userMenuOpen = false">
                                    <Icon :size="16">
                                        <PersonOutline />
                                    </Icon>
                                    <span>Profil</span>
                                </Link>

                                <!-- Admin Quick Links (faqat adminlar uchun) -->
                                <div v-if="$page.props.auth?.user?.role?.name === 'admin'">
                                    <hr class="my-1">
                                    <div class="px-3 py-1 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Administrator
                                    </div>

                                    <!-- Admin Settings -->
                                    <Link href="/admin/settings"
                                        class="flex items-center space-x-2 w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                        @click="userMenuOpen = false">
                                        <Icon :size="16">
                                            <SettingsOutline />
                                        </Icon>
                                        <span>Sozlamalar</span>
                                    </Link>

                                    <!-- Users Management -->
                                    <Link href="/admin/users"
                                        class="flex items-center space-x-2 w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                        @click="userMenuOpen = false">
                                        <Icon :size="16">
                                            <PeopleOutline />
                                        </Icon>
                                        <span>Foydalanuvchilar</span>
                                    </Link>

                                    <!-- Permissions -->
                                    <Link href="/admin/permissions"
                                        class="flex items-center space-x-2 w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                        @click="userMenuOpen = false">
                                        <Icon :size="16">
                                            <ShieldOutline />
                                        </Icon>
                                        <span>Ruxsatlar</span>
                                    </Link>

                                    <!-- Roles -->
                                    <Link href="/admin/roles"
                                        class="flex items-center space-x-2 w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                        @click="userMenuOpen = false">
                                        <Icon :size="16">
                                            <KeyOutline />
                                        </Icon>
                                        <span>Rollar</span>
                                    </Link>
                                </div>

                                <hr class="my-1">

                                <!-- Logout -->
                                <Link href="/logout" method="post" as="button"
                                    class="flex items-center space-x-2 w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                                    @click="userMenuOpen = false">
                                    <Icon :size="16">
                                        <LogOutOutline />
                                    </Icon>
                                    <span>Chiqish</span>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-6">
                <!-- Flash Messages -->
                <div v-if="$page.props.flash?.success"
                    class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-center space-x-3">
                    <Icon :size="20" class="text-green-600 flex-shrink-0">
                        <CheckmarkCircleOutline />
                    </Icon>
                    <span>{{ $page.props.flash.success }}</span>
                </div>
                <div v-if="$page.props.flash?.error"
                    class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg flex items-center space-x-3">
                    <Icon :size="20" class="text-red-600 flex-shrink-0">
                        <CloseCircleOutline />
                    </Icon>
                    <span>{{ $page.props.flash.error }}</span>
                </div>
                <div v-if="$page.props.flash?.warning"
                    class="mb-4 p-4 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-lg flex items-center space-x-3">
                    <Icon :size="20" class="text-yellow-600 flex-shrink-0">
                        <WarningOutline />
                    </Icon>
                    <span>{{ $page.props.flash.warning }}</span>
                </div>

                <slot />
            </main>
        </div>

        <!-- Mobile Sidebar Overlay -->
        <div v-show="sidebarOpen" @click="sidebarOpen = false"
            class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"></div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { Icon } from '@vicons/utils';
import {
    GridOutline,
    LayersOutline,
    CopyOutline,
    CompassOutline,
    PeopleOutline,
    BookOutline,
    PeopleCircleOutline,
    CalendarOutline,
    TimeOutline,
    CloseOutline,
    DocumentsOutline,
    BarChartOutline,
    MenuOutline,
    ChevronDownOutline,
    PersonOutline,
    SettingsOutline,
    LogOutOutline,
    CheckmarkCircleOutline,
    CloseCircleOutline,
    WarningOutline,
    SchoolOutline,
    NotificationsOutline,
    NotificationsOffOutline,
    InformationCircleOutline,
    AlertCircleOutline,
    ShieldOutline,
    KeyOutline,
    DocumentTextOutline,
    ServerOutline,
    ClipboardOutline,
} from '@vicons/ionicons5';
import NavLink from '@/Components/NavLink.vue';

const sidebarOpen = ref(false);
const userMenuOpen = ref(false);
const notificationsOpen = ref(false);

// Refs
const notificationsButton = ref(null);
const notificationsDropdown = ref(null);
const userMenuButton = ref(null);
const userMenuDropdown = ref(null);

// Get notifications from shared props
const page = usePage();
const recentNotifications = computed(() => page.props.recentNotifications || []);
const unreadCount = computed(() => page.props.unreadNotificationsCount || 0);

// Toggle functions
const toggleNotifications = () => {
    notificationsOpen.value = !notificationsOpen.value;
    if (notificationsOpen.value) userMenuOpen.value = false;
};

const toggleUserMenu = () => {
    userMenuOpen.value = !userMenuOpen.value;
    if (userMenuOpen.value) notificationsOpen.value = false;
};

// Click outside handler
const handleClickOutside = (event) => {
    // Notifications
    if (notificationsOpen.value &&
        notificationsButton.value &&
        !notificationsButton.value.contains(event.target) &&
        notificationsDropdown.value &&
        !notificationsDropdown.value.contains(event.target)) {
        notificationsOpen.value = false;
    }

    // User menu
    if (userMenuOpen.value &&
        userMenuButton.value &&
        !userMenuButton.value.contains(event.target) &&
        userMenuDropdown.value &&
        !userMenuDropdown.value.contains(event.target)) {
        userMenuOpen.value = false;
    }
};

// Keyboard handler
const handleKeydown = (event) => {
    if (event.key === 'Escape') {
        notificationsOpen.value = false;
        userMenuOpen.value = false;
        if (window.innerWidth < 1024) sidebarOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
    document.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
    document.removeEventListener('keydown', handleKeydown);
});

// Get notification icon
const getNotificationIcon = (type) => {
    const icons = {
        info: InformationCircleOutline,
        success: CheckmarkCircleOutline,
        warning: WarningOutline,
        error: AlertCircleOutline,
        workload: ClipboardOutline,
        user: PersonOutline,
        default: InformationCircleOutline,
    };
    return icons[type] || icons.default;
};

// Get notification color
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

// Format relative time
const formatRelativeTime = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diff = now - date;
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(diff / 3600000);
    const days = Math.floor(diff / 86400000);

    if (minutes < 1) return 'Hozirgina';
    if (minutes < 60) return `${minutes}d oldin`;
    if (hours < 24) return `${hours}s oldin`;
    if (days < 7) return `${days} kun oldin`;

    return date.toLocaleDateString('uz-UZ', { month: 'short', day: 'numeric' });
};
</script>

<style scoped>
/* Custom scrollbar */
nav::-webkit-scrollbar {
    width: 6px;
}

nav::-webkit-scrollbar-track {
    background: transparent;
}

nav::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.1);
    border-radius: 3px;
}

nav::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 0, 0, 0.2);
}
</style>
