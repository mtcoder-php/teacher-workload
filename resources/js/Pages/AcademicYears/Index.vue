<template>
    <Head title="O'quv yillari" />
    
    <AuthenticatedLayout>
        <template #header>O'quv yillari</template>

        <div class="space-y-6">
            <!-- Header Actions -->
            <div class="flex justify-end">
                <Link
                    href="/academic-years/create"
                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Yangi O'quv Yili</span>
                </Link>
            </div>

            <!-- Academic Years Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    v-for="year in academicYears.data"
                    :key="year.id"
                    class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition"
                >
                    <div
                        :class="{
                            'bg-gradient-to-r from-indigo-500 to-indigo-600': year.is_active,
                            'bg-gradient-to-r from-gray-500 to-gray-600': !year.is_active
                        }"
                        class="px-6 py-4 text-white"
                    >
                        <div class="flex items-center justify-between">
                            <h3 class="text-2xl font-bold">{{ year.name }}</h3>
                            <span
                                v-if="year.is_active"
                                class="px-3 py-1 bg-white bg-opacity-20 rounded-full text-xs font-semibold"
                            >
                                Joriy
                            </span>
                        </div>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="space-y-2">
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>{{ formatDate(year.start_date) }} - {{ formatDate(year.end_date) }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <span>{{ year.semesters_count }} ta semestr</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <Link
                                :href="`/academic-years/${year.id}`"
                                class="text-indigo-600 hover:text-indigo-900 text-sm font-medium"
                            >
                                Batafsil
                            </Link>
                            <div class="flex items-center space-x-2">
                                <Link
                                    :href="`/academic-years/${year.id}/edit`"
                                    class="text-gray-600 hover:text-gray-900"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </Link>
                                <button
                                    v-if="!year.is_active"
                                    @click="setCurrentYear(year)"
                                    class="text-green-600 hover:text-green-900"
                                    title="Joriy qilish"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </button>
                                <button
                                    v-if="!year.is_active"
                                    @click="deleteYear(year)"
                                    class="text-red-600 hover:text-red-900"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="academicYears.data.length === 0" class="col-span-full">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="text-lg font-medium text-gray-900 mb-2">O'quv yillari topilmadi</p>
                        <p class="text-sm text-gray-500">Yangi o'quv yili qo'shish uchun yuqoridagi tugmani bosing</p>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="academicYears.data.length > 0" class="flex justify-center">
                <div class="flex space-x-2">
                    <Link
                        v-for="(link, index) in academicYears.links"
                        :key="index"
                        :href="link.url || '#'"
                        :class="[
                            'px-3 py-1 rounded border',
                            link.active 
                                ? 'bg-indigo-600 text-white border-indigo-600' 
                                : link.url 
                                    ? 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                                    : 'bg-gray-100 text-gray-400 border-gray-300 cursor-not-allowed'
                        ]"
                        :preserve-scroll="true"
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">O'quv yilini o'chirish</h3>
                <p class="text-gray-600 mb-6">
                    <strong>{{ yearToDelete?.name }}</strong> o'quv yilini o'chirishni xohlaysizmi?
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
    academicYears: Object,
});

const showDeleteModal = ref(false);
const yearToDelete = ref(null);

const formatDate = (date) => {
    if (!date) return '';
    
    const [year, month, day] = date.split('T')[0].split('-');
    return `${day}.${month}.${year}`;
};

const deleteYear = (year) => {
    yearToDelete.value = year;
    showDeleteModal.value = true;
};

const confirmDelete = () => {
    router.delete(`/academic-years/${yearToDelete.value.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
            yearToDelete.value = null;
        },
    });
};

const setCurrentYear = (year) => {
    router.post(`/academic-years/${year.id}/set-current`, {}, {
        preserveScroll: true,
    });
};
</script>