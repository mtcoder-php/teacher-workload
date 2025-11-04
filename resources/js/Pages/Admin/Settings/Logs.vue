<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('admin.settings.index')"
                        class="text-gray-600 hover:text-gray-900"
                    >
                        <Icon :size="24">
                            <ArrowBackOutline />
                        </Icon>
                    </Link>
                    <h2 class="text-2xl font-semibold text-gray-800">Tizim Loglari</h2>
                </div>
                <div class="flex items-center gap-2">
                    <a
                        :href="route('admin.logs.download')"
                        class="px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center"
                    >
                        <Icon :size="20" class="mr-2">
                            <DownloadOutline />
                        </Icon>
                        Yuklab olish
                    </a>
                    <button
                        @click="clearLogs"
                        :disabled="clearForm.processing"
                        class="px-3 py-1.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 flex items-center"
                    >
                        <Icon :size="20" class="mr-2">
                            <TrashOutline />
                        </Icon>
                        {{ clearForm.processing ? 'Tozalanmoqda...' : 'Loglarni Tozalash' }}
                    </button>
                </div>
            </div>
        </template>

        <div class="max-w-7xl mx-auto">
            <!-- Log Info -->
            <div v-if="fileSize" class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Fayl hajmi</p>
                        <p class="text-xl font-bold text-gray-900">{{ formatBytes(fileSize) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Oxirgi o'zgarish</p>
                        <p class="text-xl font-bold text-gray-900">{{ formatDate(lastModified) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Ko'rsatilgan qatorlar</p>
                        <p class="text-xl font-bold text-gray-900">{{ logs.length }} / 100</p>
                    </div>
                </div>
            </div>

            <!-- Search -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                <div class="relative">
                    <Icon :size="20" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                        <SearchOutline />
                    </Icon>
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Loglarda qidirish..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    />
                </div>
            </div>

            <!-- Filter Buttons -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                <div class="flex flex-wrap gap-2">
                    <button
                        @click="activeFilter = 'all'"
                        :class="[
                            'px-4 py-2 rounded-lg font-medium transition-colors',
                            activeFilter === 'all'
                                ? 'bg-indigo-600 text-white'
                                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                        ]"
                    >
                        Barchasi ({{ logs.length }})
                    </button>
                    <button
                        @click="activeFilter = 'error'"
                        :class="[
                            'px-4 py-2 rounded-lg font-medium transition-colors',
                            activeFilter === 'error'
                                ? 'bg-red-600 text-white'
                                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                        ]"
                    >
                        Xatolar ({{ errorCount }})
                    </button>
                    <button
                        @click="activeFilter = 'warning'"
                        :class="[
                            'px-4 py-2 rounded-lg font-medium transition-colors',
                            activeFilter === 'warning'
                                ? 'bg-yellow-600 text-white'
                                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                        ]"
                    >
                        Ogohlantirishlar ({{ warningCount }})
                    </button>
                    <button
                        @click="activeFilter = 'info'"
                        :class="[
                            'px-4 py-2 rounded-lg font-medium transition-colors',
                            activeFilter === 'info'
                                ? 'bg-blue-600 text-white'
                                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                        ]"
                    >
                        Ma'lumot ({{ infoCount }})
                    </button>
                </div>
            </div>

            <!-- Logs -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div v-if="message" class="p-12 text-center">
                    <Icon :size="64" class="mx-auto text-gray-300 mb-4">
                        <DocumentTextOutline />
                    </Icon>
                    <p class="text-gray-500">{{ message }}</p>
                </div>

                <div v-else-if="filteredLogs.length === 0" class="p-12 text-center">
                    <Icon :size="64" class="mx-auto text-gray-300 mb-4">
                        <SearchOutline />
                    </Icon>
                    <p class="text-gray-500">Qidiruv bo'yicha hech narsa topilmadi</p>
                </div>

                <div v-else class="divide-y divide-gray-200">
                    <div
                        v-for="(log, index) in paginatedLogs"
                        :key="index"
                        class="p-4 hover:bg-gray-50 transition-colors"
                        :class="getLogClass(log)"
                    >
                        <div class="flex items-start">
                            <Icon :size="20" class="mr-3 mt-1 flex-shrink-0" :class="getLogIconClass(log)">
                                <component :is="getLogIcon(log)" />
                            </Icon>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-mono text-gray-900 break-all">{{ log }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="filteredLogs.length > perPage" class="border-t border-gray-200 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Ko'rsatilmoqda <span class="font-medium">{{ (currentPage - 1) * perPage + 1 }}</span> dan
                            <span class="font-medium">{{ Math.min(currentPage * perPage, filteredLogs.length) }}</span>
                            gacha, jami <span class="font-medium">{{ filteredLogs.length }}</span> ta
                        </div>
                        <div class="flex gap-2">
                            <button
                                @click="currentPage--"
                                :disabled="currentPage === 1"
                                class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                Oldingi
                            </button>
                            <button
                                @click="currentPage++"
                                :disabled="currentPage * perPage >= filteredLogs.length"
                                class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                Keyingi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm, Link, router } from '@inertiajs/vue3';
import { Icon } from '@vicons/utils';
import {
    ArrowBackOutline,
    TrashOutline,
    DocumentTextOutline,
    SearchOutline,
    DownloadOutline,
    AlertCircleOutline,
    WarningOutline,
    InformationCircleOutline,
    CheckmarkCircleOutline,
} from '@vicons/ionicons5';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    logs: {
        type: Array,
        default: () => []
    },
    message: String,
    fileSize: Number,
    lastModified: Number,
});

const searchQuery = ref('');
const currentPage = ref(1);
const perPage = 20;
const activeFilter = ref('all');

const clearForm = useForm({});

const route = (name) => {
    const routes = {
        'admin.settings.index': '/admin/settings',
        'admin.logs.download': '/admin/logs/download',
    };
    return routes[name] || '/admin/logs';
};

const errorCount = computed(() => {
    return props.logs.filter(log => 
        log.includes('ERROR') || log.includes('Exception')
    ).length;
});

const warningCount = computed(() => {
    return props.logs.filter(log => log.includes('WARNING')).length;
});

const infoCount = computed(() => {
    return props.logs.filter(log => log.includes('INFO')).length;
});

const filteredLogs = computed(() => {
    let filtered = props.logs;

    // Filter by type
    if (activeFilter.value === 'error') {
        filtered = filtered.filter(log => log.includes('ERROR') || log.includes('Exception'));
    } else if (activeFilter.value === 'warning') {
        filtered = filtered.filter(log => log.includes('WARNING'));
    } else if (activeFilter.value === 'info') {
        filtered = filtered.filter(log => log.includes('INFO'));
    }

    // Filter by search query
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(log => log.toLowerCase().includes(query));
    }

    return filtered;
});

const paginatedLogs = computed(() => {
    const start = (currentPage.value - 1) * perPage;
    const end = start + perPage;
    return filteredLogs.value.slice(start, end);
});

const formatBytes = (bytes) => {
    if (!bytes) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
};

const formatDate = (timestamp) => {
    if (!timestamp) return '';
    const date = new Date(timestamp * 1000);
    return date.toLocaleString('uz-UZ', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const getLogClass = (log) => {
    if (log.includes('ERROR') || log.includes('Exception')) {
        return 'bg-red-50 border-l-4 border-red-500';
    } else if (log.includes('WARNING')) {
        return 'bg-yellow-50 border-l-4 border-yellow-500';
    } else if (log.includes('INFO')) {
        return 'bg-blue-50 border-l-4 border-blue-500';
    }
    return 'border-l-4 border-transparent';
};

const getLogIconClass = (log) => {
    if (log.includes('ERROR') || log.includes('Exception')) {
        return 'text-red-600';
    } else if (log.includes('WARNING')) {
        return 'text-yellow-600';
    } else if (log.includes('INFO')) {
        return 'text-blue-600';
    }
    return 'text-gray-600';
};

const getLogIcon = (log) => {
    if (log.includes('ERROR') || log.includes('Exception')) {
        return AlertCircleOutline;
    } else if (log.includes('WARNING')) {
        return WarningOutline;
    } else if (log.includes('INFO')) {
        return InformationCircleOutline;
    }
    return CheckmarkCircleOutline;
};

const clearLogs = () => {
    if (confirm('Barcha loglarni o\'chirmoqchimisiz? (Backup yaratiladi)')) {
        clearForm.delete('/admin/logs', {
            preserveScroll: true,
            onSuccess: () => {
                router.reload();
            },
        });
    }
};
</script>