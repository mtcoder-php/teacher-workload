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
                    <h2 class="text-2xl font-semibold text-gray-800">Database Backuplar</h2>
                </div>
                <div class="flex items-center gap-2">
                    <button
                        @click="createBackup"
                        :disabled="createForm.processing"
                        class="px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50 flex items-center"
                    >
                        <Icon :size="20" class="mr-2">
                            <AddOutline />
                        </Icon>
                        {{ createForm.processing ? 'Yaratilmoqda...' : 'Yangi Backup' }}
                    </button>
                </div>
            </div>
        </template>

        <div class="max-w-7xl mx-auto">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Jami Backuplar</p>
                            <p class="text-3xl font-bold text-gray-900">{{ backups.length }}</p>
                        </div>
                        <Icon :size="40" class="text-blue-500">
                            <FolderOpenOutline />
                        </Icon>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Jami Hajm</p>
                            <p class="text-3xl font-bold text-gray-900">{{ totalSize }}</p>
                        </div>
                        <Icon :size="40" class="text-purple-500">
                            <ServerOutline />
                        </Icon>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Eng Oxirgi Backup</p>
                            <p class="text-lg font-bold text-gray-900">{{ latestBackup }}</p>
                        </div>
                        <Icon :size="40" class="text-green-500">
                            <TimeOutline />
                        </Icon>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <button
                            @click="cleanupBackups"
                            :disabled="cleanupForm.processing"
                            class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors disabled:opacity-50 flex items-center"
                        >
                            <Icon :size="20" class="mr-2">
                                <BrushOutline />
                            </Icon>
                            {{ cleanupForm.processing ? 'Tozalanmoqda...' : 'Eski Backuplarni Tozalash' }}
                        </button>
                    </div>
                    <p class="text-sm text-gray-500">30 kundan eski backuplar o'chiriladi</p>
                </div>
            </div>

            <!-- Backups List -->
            <div v-if="backups.length === 0" class="bg-white rounded-lg shadow-sm p-12 text-center">
                <Icon :size="64" class="mx-auto text-gray-300 mb-4">
                    <FolderOpenOutline />
                </Icon>
                <p class="text-gray-500 text-lg mb-4">Backuplar topilmadi</p>
                <button
                    @click="createBackup"
                    :disabled="createForm.processing"
                    class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50"
                >
                    Birinchi Backup Yaratish
                </button>
            </div>

            <div v-else class="bg-white rounded-lg shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fayl Nomi
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Hajmi
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Yaratilgan Sana
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Amallar
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="backup in backups" :key="backup.name" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <Icon :size="20" class="text-blue-500 mr-3">
                                        <DocumentOutline />
                                    </Icon>
                                    <span class="text-sm font-medium text-gray-900 font-mono">{{ backup.name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900">{{ backup.size_formatted }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-500">{{ backup.date_formatted }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Restore -->
                                    <button
                                        @click="restoreBackup(backup)"
                                        :disabled="restoreForm.processing"
                                        class="px-3 py-1.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors disabled:opacity-50 flex items-center"
                                        title="Tiklash"
                                    >
                                        <Icon :size="18" class="mr-1">
                                            <RefreshOutline />
                                        </Icon>
                                        Tiklash
                                    </button>

                                    <!-- Download -->
                                    <a
                                        :href="getDownloadUrl(backup.name)"
                                        class="px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center"
                                        title="Yuklab olish"
                                    >
                                        <Icon :size="18" class="mr-1">
                                            <DownloadOutline />
                                        </Icon>
                                        Yuklash
                                    </a>

                                    <!-- Delete -->
                                    <button
                                        @click="deleteBackup(backup)"
                                        :disabled="deleteForm.processing"
                                        class="px-3 py-1.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 flex items-center"
                                        title="O'chirish"
                                    >
                                        <Icon :size="18">
                                            <TrashOutline />
                                        </Icon>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Restore Confirmation Modal -->
        <div v-if="showRestoreModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                <div class="flex items-center mb-4">
                    <Icon :size="40" class="text-yellow-500 mr-3">
                        <WarningOutline />
                    </Icon>
                    <h3 class="text-xl font-semibold text-gray-900">Ogohlik!</h3>
                </div>
                <p class="text-gray-600 mb-6">
                    Backupni tiklash hozirgi barcha ma'lumotlarni o'chiradi va backup bilan almashtiradi. 
                    Bu amalni bekor qilib bo'lmaydi!
                </p>
                <div class="flex items-center mb-6">
                    <input
                        v-model="restoreConfirm"
                        type="checkbox"
                        id="confirm"
                        class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                    />
                    <label for="confirm" class="ml-2 text-sm text-gray-900">
                        Men ogohlantiruvni o'qidim va roziman
                    </label>
                </div>
                <div class="flex gap-3">
                    <button
                        @click="showRestoreModal = false"
                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors"
                    >
                        Bekor qilish
                    </button>
                    <button
                        @click="confirmRestore"
                        :disabled="!restoreConfirm || restoreForm.processing"
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {{ restoreForm.processing ? 'Tiklanmoqda...' : 'Tiklash' }}
                    </button>
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
    AddOutline,
    FolderOpenOutline,
    ServerOutline,
    TimeOutline,
    BrushOutline,
    DocumentOutline,
    RefreshOutline,
    DownloadOutline,
    TrashOutline,
    WarningOutline,
} from '@vicons/ionicons5';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    backups: Array,
});

const createForm = useForm({});
const deleteForm = useForm({});
const cleanupForm = useForm({});
const restoreForm = useForm({
    confirm: false,
});

const showRestoreModal = ref(false);
const restoreConfirm = ref(false);
const selectedBackup = ref(null);

const route = (name) => {
    const routes = {
        'admin.settings.index': '/admin/settings',
        'admin.backups.create': '/admin/backups',
        'admin.backups.cleanup': '/admin/backups/cleanup',
    };
    return routes[name] || '/admin/backups';
};

const totalSize = computed(() => {
    const total = props.backups.reduce((sum, backup) => sum + backup.size, 0);
    return formatBytes(total);
});

const latestBackup = computed(() => {
    if (props.backups.length === 0) return 'Mavjud emas';
    return props.backups[0].date_formatted;
});

const formatBytes = (bytes) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
};

const getDownloadUrl = (filename) => {
    return `/admin/backups/${filename}/download`;
};

const createBackup = () => {
    if (confirm('Yangi backup yaratilsinmi?')) {
        createForm.post(route('admin.backups.create'), {
            preserveScroll: true,
            onSuccess: () => {
                router.reload();
            },
        });
    }
};

const deleteBackup = (backup) => {
    if (confirm(`"${backup.name}" o'chirilsinmi?`)) {
        deleteForm.delete(`/admin/backups/${backup.name}`, {
            preserveScroll: true,
            onSuccess: () => {
                router.reload();
            },
        });
    }
};

const cleanupBackups = () => {
    if (confirm('30 kundan eski backuplar o\'chirilsinmi?')) {
        cleanupForm.post(route('admin.backups.cleanup'), {
            preserveScroll: true,
            onSuccess: () => {
                router.reload();
            },
        });
    }
};

const restoreBackup = (backup) => {
    selectedBackup.value = backup;
    restoreConfirm.value = false;
    showRestoreModal.value = true;
};

const confirmRestore = () => {
    if (!restoreConfirm.value) return;

    restoreForm.confirm = true;
    restoreForm.post(`/admin/backups/${selectedBackup.value.name}/restore`, {
        onSuccess: () => {
            showRestoreModal.value = false;
            restoreConfirm.value = false;
        },
        onError: () => {
            showRestoreModal.value = false;
            restoreConfirm.value = false;
        },
    });
};
</script>