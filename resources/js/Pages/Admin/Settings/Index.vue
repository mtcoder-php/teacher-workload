<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-semibold text-gray-800">Tizim Sozlamalari</h2>
                <div class="flex items-center gap-2">
                    <Icon :size="28" class="text-indigo-600">
                        <SettingsOutline />
                    </Icon>
                </div>
            </div>
        </template>

        <div class="max-w-7xl mx-auto space-y-6">
            <!-- System Info Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- PHP Version -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">PHP Version</p>
                            <p class="text-2xl font-bold text-gray-900">{{ systemInfo.php_version }}</p>
                        </div>
                        <Icon :size="40" class="text-blue-500">
                            <CodeSlashOutline />
                        </Icon>
                    </div>
                </div>

                <!-- Laravel Version -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Laravel</p>
                            <p class="text-2xl font-bold text-gray-900">{{ systemInfo.laravel_version }}</p>
                        </div>
                        <Icon :size="40" class="text-red-500">
                            <RocketOutline />
                        </Icon>
                    </div>
                </div>

                <!-- Environment -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Muhit</p>
                            <p class="text-2xl font-bold text-gray-900 capitalize">{{ systemInfo.environment }}</p>
                        </div>
                        <Icon :size="40" class="text-green-500">
                            <CloudOutline />
                        </Icon>
                    </div>
                </div>

                <!-- Disk Usage -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Disk</p>
                            <p class="text-2xl font-bold text-gray-900">{{ diskInfo.used_percentage }}%</p>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ formatBytes(diskInfo.used) }} / {{ formatBytes(diskInfo.total) }}
                            </p>
                        </div>
                        <Icon :size="40" class="text-purple-500">
                            <ServerOutline />
                        </Icon>
                    </div>
                    <!-- Progress Bar -->
                    <div class="mt-4 w-full bg-gray-200 rounded-full h-2">
                        <div 
                            class="h-2 rounded-full transition-all duration-300"
                            :class="diskInfo.used_percentage > 80 ? 'bg-red-600' : diskInfo.used_percentage > 60 ? 'bg-yellow-500' : 'bg-green-500'"
                            :style="{ width: diskInfo.used_percentage + '%' }"
                        ></div>
                    </div>
                </div>
            </div>

            <!-- Cache Status -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <Icon :size="24" class="mr-2 text-indigo-600">
                        <FlashOutline />
                    </Icon>
                    Cache Holati
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <span class="text-gray-700">Config Cache</span>
                        <span 
                            class="px-3 py-1 rounded-full text-sm font-medium"
                            :class="cacheInfo.config_cached ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-600'"
                        >
                            {{ cacheInfo.config_cached ? 'Aktiv' : 'Yo\'q' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <span class="text-gray-700">Route Cache</span>
                        <span 
                            class="px-3 py-1 rounded-full text-sm font-medium"
                            :class="cacheInfo.routes_cached ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-600'"
                        >
                            {{ cacheInfo.routes_cached ? 'Aktiv' : 'Yo\'q' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <span class="text-gray-700">Events Cache</span>
                        <span 
                            class="px-3 py-1 rounded-full text-sm font-medium"
                            :class="cacheInfo.events_cached ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-600'"
                        >
                            {{ cacheInfo.events_cached ? 'Aktiv' : 'Yo\'q' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- System Actions -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <Icon :size="24" class="mr-2 text-indigo-600">
                        <BuildOutline />
                    </Icon>
                    Tizim Amallari
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                    <button
                        @click="executeAction('cache_clear')"
                        :disabled="actionForm.processing"
                        class="flex flex-col items-center justify-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <Icon :size="32" class="text-blue-600 mb-2">
                            <TrashOutline />
                        </Icon>
                        <span class="text-sm font-medium text-gray-700 text-center">Cache Tozalash</span>
                    </button>

                    <button
                        @click="executeAction('config_cache')"
                        :disabled="actionForm.processing"
                        class="flex flex-col items-center justify-center p-4 bg-green-50 hover:bg-green-100 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <Icon :size="32" class="text-green-600 mb-2">
                            <DocumentTextOutline />
                        </Icon>
                        <span class="text-sm font-medium text-gray-700 text-center">Config Cache</span>
                    </button>

                    <button
                        @click="executeAction('route_cache')"
                        :disabled="actionForm.processing"
                        class="flex flex-col items-center justify-center p-4 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <Icon :size="32" class="text-purple-600 mb-2">
                            <GitBranchOutline />
                        </Icon>
                        <span class="text-sm font-medium text-gray-700 text-center">Route Cache</span>
                    </button>

                    <button
                        @click="executeAction('view_cache')"
                        :disabled="actionForm.processing"
                        class="flex flex-col items-center justify-center p-4 bg-yellow-50 hover:bg-yellow-100 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <Icon :size="32" class="text-yellow-600 mb-2">
                            <EyeOutline />
                        </Icon>
                        <span class="text-sm font-medium text-gray-700 text-center">View Cache</span>
                    </button>

                    <button
                        @click="executeAction('optimize')"
                        :disabled="actionForm.processing"
                        class="flex flex-col items-center justify-center p-4 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <Icon :size="32" class="text-indigo-600 mb-2">
                            <SpeedometerOutline />
                        </Icon>
                        <span class="text-sm font-medium text-gray-700 text-center">Optimize</span>
                    </button>

                    <button
                        @click="executeAction('clear_all')"
                        :disabled="actionForm.processing"
                        class="flex flex-col items-center justify-center p-4 bg-red-50 hover:bg-red-100 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <Icon :size="32" class="text-red-600 mb-2">
                            <RefreshOutline />
                        </Icon>
                        <span class="text-sm font-medium text-gray-700 text-center">Hammasini Tozalash</span>
                    </button>
                </div>
            </div>

            <!-- Settings Tabs -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px overflow-x-auto">
                        <button
                            v-for="(settingsGroup, groupKey) in settings"
                            :key="groupKey"
                            @click="activeTab = groupKey"
                            :class="[
                                'px-6 py-4 text-sm font-medium whitespace-nowrap border-b-2 transition-colors',
                                activeTab === groupKey
                                    ? 'border-indigo-600 text-indigo-600'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                            ]"
                        >
                            {{ formatGroupName(groupKey) }}
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="p-6">
                    <div v-for="(settingsGroup, groupKey) in settings" :key="groupKey">
                        <div v-show="activeTab === groupKey" class="space-y-6">
                            <div
                                v-for="setting in settingsGroup"
                                :key="setting.id"
                                class="flex items-start justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
                            >
                                <div class="flex-1 mr-4">
                                    <label class="block text-sm font-medium text-gray-900 mb-1">
                                        {{ setting.label }}
                                    </label>
                                    <p v-if="setting.description" class="text-xs text-gray-500 mb-3">
                                        {{ setting.description }}
                                    </p>

                                    <!-- Text Input -->
                                    <input
                                        v-if="setting.type === 'text'"
                                        v-model="settingValues[setting.id]"
                                        type="text"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    />

                                    <!-- Number Input -->
                                    <input
                                        v-else-if="setting.type === 'number'"
                                        v-model="settingValues[setting.id]"
                                        type="number"
                                        step="0.1"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    />

                                    <!-- Boolean Toggle -->
                                    <label
                                        v-else-if="setting.type === 'boolean'"
                                        class="relative inline-flex items-center cursor-pointer"
                                    >
                                        <input
                                            type="checkbox"
                                            v-model="settingValues[setting.id]"
                                            :true-value="1"
                                            :false-value="0"
                                            class="sr-only peer"
                                        />
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                        <span class="ml-3 text-sm font-medium text-gray-700">
                                            {{ settingValues[setting.id] == 1 ? 'Yoqilgan' : 'O\'chirilgan' }}
                                        </span>
                                    </label>

                                    <!-- JSON Textarea -->
                                    <textarea
                                        v-else-if="setting.type === 'json'"
                                        v-model="settingValues[setting.id]"
                                        rows="4"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-mono text-sm"
                                    ></textarea>
                                </div>

                                <button
                                    @click="saveSetting(setting)"
                                    :disabled="isSaving(setting.id)"
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
                                >
                                    <Icon :size="18" class="mr-1" v-if="isSaving(setting.id)">
                                        <RefreshOutline class="animate-spin" />
                                    </Icon>
                                    <Icon :size="18" class="mr-1" v-else>
                                        <SaveOutline />
                                    </Icon>
                                    {{ isSaving(setting.id) ? 'Saqlanmoqda...' : 'Saqlash' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Actions Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Logo Upload Section -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <Icon :size="24" class="mr-2 text-indigo-600">
                            <ImageOutline />
                        </Icon>
                        Tizim Logosi
                    </h3>
                    <p class="text-sm text-gray-600 mb-4">
                        Tizim logosini yuklash yoki o'zgartirish
                    </p>
                    
                    <!-- Current Logo Preview -->
                    <div v-if="currentLogo" class="mb-4 p-4 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500 mb-2">Joriy logo:</p>
                        <img :src="currentLogo" alt="Logo" class="h-16 object-contain" />
                    </div>

                    <!-- File Input -->
                    <input
                        ref="logoInput"
                        type="file"
                        accept="image/jpeg,image/png,image/jpg,image/svg+xml"
                        @change="handleLogoUpload"
                        class="hidden"
                    />
                    
                    <button
                        @click="$refs.logoInput.click()"
                        :disabled="logoForm.processing"
                        class="w-full px-4 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
                    >
                        <Icon :size="20" class="mr-2">
                            <CloudUploadOutline />
                        </Icon>
                        {{ logoForm.processing ? 'Yuklanmoqda...' : 'Logo Yuklash' }}
                    </button>
                    <p class="text-xs text-gray-500 mt-2">
                        Ruxsat etilgan: JPG, PNG, SVG. Max: 2MB
                    </p>
                </div>

                <!-- Backup Section -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <Icon :size="24" class="mr-2 text-indigo-600">
                            <SaveOutline />
                        </Icon>
                        Database Backup
                    </h3>
                    <p class="text-sm text-gray-600 mb-4">
                        Ma'lumotlar bazasining zaxira nusxasini yaratish
                    </p>
                    <div class="space-y-2">
                        <button
                            @click="createBackup"
                            :disabled="backupForm.processing"
                            class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
                        >
                            <Icon :size="20" class="mr-2">
                                <CloudDownloadOutline />
                            </Icon>
                            {{ backupForm.processing ? 'Yaratilmoqda...' : 'Backup Yaratish' }}
                        </button>
                        
                        <Link
                            :href="route('admin.backups.index')"
                            class="w-full px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors flex items-center justify-center"
                        >
                            <Icon :size="20" class="mr-2">
                                <FolderOpenOutline />
                            </Icon>
                            Backuplar Ro'yxati
                        </Link>
                    </div>
                </div>

                <!-- Logs Section -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <Icon :size="24" class="mr-2 text-indigo-600">
                            <DocumentTextOutline />
                        </Icon>
                        Tizim Loglari
                    </h3>
                    <p class="text-sm text-gray-600 mb-4">
                        Tizim loglarini ko'rish va boshqarish
                    </p>
                    <div class="flex gap-2">
                        <Link
                            :href="route('admin.logs.index')"
                            class="flex-1 px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center"
                        >
                            <Icon :size="20" class="mr-2">
                                <EyeOutline />
                            </Icon>
                            Ko'rish
                        </Link>
                        <button
                            @click="clearLogs"
                            :disabled="logsForm.processing"
                            class="px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
                        >
                            <Icon :size="20">
                                <TrashOutline />
                            </Icon>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Import/Export Section -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <Icon :size="24" class="mr-2 text-indigo-600">
                        <SwapHorizontalOutline />
                    </Icon>
                    Sozlamalarni Import/Export
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Export -->
                    <div>
                        <p class="text-sm text-gray-600 mb-3">
                            Barcha sozlamalarni JSON faylga eksport qilish
                        </p>
                        <a
                            :href="route('admin.settings.export')"
                            class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center"
                        >
                            <Icon :size="20" class="mr-2">
                                <DownloadOutline />
                            </Icon>
                            Eksport qilish
                        </a>
                    </div>

                    <!-- Import -->
                    <div>
                        <p class="text-sm text-gray-600 mb-3">
                            Sozlamalarni JSON fayldan import qilish
                        </p>
                        <input
                            ref="importInput"
                            type="file"
                            accept="application/json"
                            @change="handleImport"
                            class="hidden"
                        />
                        <button
                            @click="$refs.importInput.click()"
                            :disabled="importForm.processing"
                            class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
                        >
                            <Icon :size="20" class="mr-2">
                                <CloudUploadOutline />
                            </Icon>
                            {{ importForm.processing ? 'Import...' : 'Import qilish' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { useForm, Link, router } from '@inertiajs/vue3';
import { Icon } from '@vicons/utils';
import {
    SettingsOutline,
    CodeSlashOutline,
    RocketOutline,
    CloudOutline,
    ServerOutline,
    FlashOutline,
    BuildOutline,
    TrashOutline,
    DocumentTextOutline,
    GitBranchOutline,
    EyeOutline,
    SpeedometerOutline,
    RefreshOutline,
    SaveOutline,
    CloudDownloadOutline,
    ImageOutline,
    CloudUploadOutline,
    FolderOpenOutline,
    SwapHorizontalOutline,
    DownloadOutline,
} from '@vicons/ionicons5';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    settings: Object,
    systemInfo: Object,
    diskInfo: Object,
    cacheInfo: Object,
});

const activeTab = ref(Object.keys(props.settings)[0] || 'general');

// Initialize setting values
const settingValues = reactive({});
Object.values(props.settings).forEach(group => {
    group.forEach(setting => {
        settingValues[setting.id] = setting.value;
    });
});

// Current logo
const currentLogo = computed(() => {
    const generalSettings = props.settings.general || [];
    const logoSetting = generalSettings.find(s => s.key === 'app_logo');
    return logoSetting ? logoSetting.value : null;
});

// Track which settings are being saved
const savingSettings = ref({});

const actionForm = useForm({ action: '' });
const backupForm = useForm({});
const logsForm = useForm({});
const logoForm = useForm({ logo: null });
const importForm = useForm({ file: null });

const route = (name) => {
    const routes = {
        'admin.logs.index': '/admin/logs',
        'admin.backups.index': '/admin/backups',
        'admin.system.action': '/admin/system/action',
        'admin.backups.create': '/admin/backups',
        'admin.settings.upload-logo': '/admin/settings/upload-logo',
        'admin.settings.export': '/admin/settings/export',
        'admin.settings.import': '/admin/settings/import',
    };
    return routes[name] || '/admin/settings';
};

const formatGroupName = (group) => {
    const names = {
        general: 'Umumiy',
        academic: 'O\'quv',
        workload: 'Yuklama',
        security: 'Xavfsizlik',
        email: 'Email',
        system: 'Tizim',
    };
    return names[group] || group.charAt(0).toUpperCase() + group.slice(1);
};

const formatBytes = (bytes) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
};

const executeAction = (action) => {
    const confirmMessages = {
        'cache_clear': 'Cache tozalansinmi?',
        'config_cache': 'Config cache yaratilsinmi?',
        'route_cache': 'Route cache yaratilsinmi?',
        'view_cache': 'View cache yaratilsinmi?',
        'optimize': 'Tizim optimallashtirisinmi?',
        'clear_all': 'Barcha cache\'lar tozalansinmi?',
    };

    if (confirm(confirmMessages[action] || 'Bu amalni bajarishni xohlaysizmi?')) {
        actionForm.action = action;
        actionForm.post(route('admin.system.action'), {
            preserveScroll: true,
            onSuccess: () => {
                actionForm.reset();
                router.reload({ only: ['cacheInfo'] });
            },
        });
    }
};

const isSaving = (settingId) => {
    return savingSettings.value[settingId] || false;
};

const saveSetting = (setting) => {
    savingSettings.value[setting.id] = true;
    
    router.put(`/admin/settings/${setting.id}`, {
        value: settingValues[setting.id],
    }, {
        preserveScroll: true,
        onFinish: () => {
            savingSettings.value[setting.id] = false;
        },
    });
};

const createBackup = () => {
    if (confirm('Database backup yaratilsinmi?')) {
        backupForm.post(route('admin.backups.create'), {
            preserveScroll: true,
        });
    }
};

const handleLogoUpload = (event) => {
    const file = event.target.files[0];
    if (!file) return;

    if (file.size > 2 * 1024 * 1024) {
        alert('Fayl hajmi 2MB dan oshmasligi kerak');
        return;
    }

    const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/svg+xml'];
    if (!allowedTypes.includes(file.type)) {
        alert('Faqat JPG, PNG, SVG formatlar ruxsat etilgan');
        return;
    }

    logoForm.logo = file;
    logoForm.post(route('admin.settings.upload-logo'), {
        preserveScroll: true,
        onSuccess: () => {
            logoForm.reset();
            event.target.value = '';
            router.reload({ only: ['settings'] });
        },
        onError: () => {
            logoForm.reset();
            event.target.value = '';
        },
    });
};

const handleImport = (event) => {
    const file = event.target.files[0];
    if (!file) return;

    if (!confirm('Hozirgi sozlamalar almashtiriladi. Davom ettirilsinmi?')) {
        event.target.value = '';
        return;
    }

    importForm.file = file;
    importForm.post(route('admin.settings.import'), {
        preserveScroll: true,
        onSuccess: () => {
            importForm.reset();
            event.target.value = '';
            router.reload();
        },
        onError: () => {
            importForm.reset();
            event.target.value = '';
        },
    });
};

const clearLogs = () => {
    if (confirm('Barcha loglarni o\'chirmoqchimisiz? (Backup yaratiladi)')) {
        logsForm.delete('/admin/logs', {
            preserveScroll: true,
        });
    }
};
</script>