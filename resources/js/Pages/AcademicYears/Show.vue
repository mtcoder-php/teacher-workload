<template>
    <Head :title="academicYear.name" />
    
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-3">
                <span>{{ academicYear.name }}</span>
                <Link
                    :href="`/academic-years/${academicYear.id}/edit`"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm"
                >
                    Tahrirlash
                </Link>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Status Card -->
            <div
                :class="{
                    'bg-gradient-to-r from-indigo-500 to-indigo-600': academicYear.is_active,
                    'bg-gradient-to-r from-gray-500 to-gray-600': !academicYear.is_active
                }"
                class="rounded-xl shadow-sm overflow-hidden"
            >
                <div class="px-6 py-8 text-white">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-3xl font-bold">{{ academicYear.name }}</h2>
                        <span
                            v-if="academicYear.is_active"
                            class="px-4 py-2 bg-white bg-opacity-20 rounded-full text-sm font-semibold"
                        >
                            Joriy O'quv Yili
                        </span>
                    </div>
                    <p class="text-white text-opacity-80">
                        {{ formatDate(academicYear.start_date) }} - {{ formatDate(academicYear.end_date) }}
                    </p>
                </div>
            </div>

            <!-- Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow-sm border border-blue-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-600 mb-1">Jami semestrlar</p>
                            <p class="text-3xl font-bold text-blue-900">{{ stats.total_semesters }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-200 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl shadow-sm border border-green-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-600 mb-1">Jami yuklamalar</p>
                            <p class="text-3xl font-bold text-green-900">{{ stats.total_workloads }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-200 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Semesters List -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-800">Semestrlar</h2>
                    <Link
                        href="/semesters/create"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm"
                    >
                        Yangi Semestr
                    </Link>
                </div>

                <div v-if="academicYear.semesters && academicYear.semesters.length > 0" class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div
                            v-for="semester in academicYear.semesters"
                            :key="semester.id"
                            class="p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition"
                        >
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="font-medium text-gray-900">{{ semester.name }}</h3>
                                <span
                                    v-if="semester.is_active"
                                    class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800"
                                >
                                    Joriy
                                </span>
                            </div>
                            <div class="space-y-2 text-sm text-gray-600">
                                <p>{{ formatDate(semester.start_date) }} - {{ formatDate(semester.end_date) }}</p>
                                <p>{{ semester.workloads_count || 0 }} ta yuklama</p>
                            </div>
                            <div class="mt-3 flex items-center justify-end">
                                <Link
                                    :href="`/semesters/${semester.id}`"
                                    class="text-indigo-600 hover:text-indigo-900 text-sm font-medium"
                                >
                                    Batafsil →
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-lg font-medium text-gray-900 mb-2">Hozircha semestrlar yo'q</p>
                    <p class="text-sm text-gray-500 mb-4">Bu o'quv yili uchun semestrlar yaratilmagan</p>
                    <Link
                        href="/semesters/create"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"
                    >
                        Semestr qo'shish
                    </Link>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-between items-center">
                <Link
                    href="/academic-years"
                    class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Orqaga</span>
                </Link>

                <div class="flex items-center gap-3">
                    <button
                        v-if="!academicYear.is_active"
                        @click="setCurrentYear"
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition"
                    >
                        Joriy qilish
                    </button>
                    <button
                        v-if="!academicYear.is_active && (!academicYear.semesters || academicYear.semesters.length === 0)"
                        @click="showDeleteModal = true"
                        class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
                    >
                        O'chirish
                    </button>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">O'quv yilini o'chirish</h3>
                <p class="text-gray-600 mb-6">
                    <strong>{{ academicYear.name }}</strong> o'quv yilini o'chirishni xohlaysizmi? Bu amalni qaytarib bo'lmaydi.
                </p>
                <div class="flex justify-end space-x-3">
                    <button
                        @click="showDeleteModal = false"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                    >
                        Bekor qilish
                    </button>
                    <button
                        @click="confirmDelete"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                    >
                        O'chirish
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    academicYear: Object,
    stats: Object,
});

const showDeleteModal = ref(false);

const formatDate = (date) => {
    if (!date) return '';
    
    const [year, month, day] = date.split('T')[0].split('-');
    return `${day}.${month}.${year}`;
};

const setCurrentYear = () => {
    router.post(`/academic-years/${props.academicYear.id}/set-current`, {}, {
        preserveScroll: true,
    });
};

const confirmDelete = () => {
    router.delete(`/academic-years/${props.academicYear.id}`, {
        onSuccess: () => {
            router.visit('/academic-years');
        },
    });
};
</script>