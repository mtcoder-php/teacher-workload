<template>

    <Head :title="teacher.user?.name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-3">
                <span>{{ teacher.user?.name }}</span>
                <Link :href="`/teachers/${teacher.id}/edit`"
                      class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">
                    Tahrirlash
                </Link>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Profile Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-8 bg-gradient-to-r from-indigo-50 to-purple-50">
                    <div class="flex items-center space-x-6">
                        <div class="flex-shrink-0">
                            <div
                                class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white text-3xl font-bold shadow-lg">
                                {{ teacher.user?.name?.charAt(0) || 'O' }}
                            </div>
                        </div>
                        <div class="flex-1">
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ teacher.user?.name }}</h2>
                            <div class="flex flex-wrap gap-3">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white text-gray-700 shadow-sm">
                                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    {{ teacher.user?.email }}
                                </span>
                                <span v-if="teacher.user?.phone"
                                      class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white text-gray-700 shadow-sm">
                                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    {{ teacher.user?.phone }}
                                </span>
                                <span :class="{
                                    'bg-green-100 text-green-800': teacher.employment_type === 'main_job',
                                    'bg-blue-100 text-blue-800': teacher.employment_type === 'internal_part_time',
                                    'bg-purple-100 text-purple-800': teacher.employment_type === 'internal_additional',
                                    'bg-yellow-100 text-yellow-800': teacher.employment_type === 'external_part_time',
                                    'bg-orange-100 text-orange-800': teacher.employment_type === 'hourly'
                                }" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold">
                                    {{ getEmploymentTypeName(teacher.employment_type) }}
                                </span>

                                <span :class="teacher.is_active
                                    ? 'bg-green-100 text-green-800'
                                    : 'bg-red-100 text-red-800'"
                                      class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold">
                                    <span :class="teacher.is_active ? 'bg-green-400' : 'bg-red-400'"
                                          class="w-2 h-2 rounded-full mr-2"></span>
                                    {{ teacher.is_active ? 'Faol' : 'Nofaol' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow-sm border border-blue-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-600 mb-1">Jami yuklamalar</p>
                            <p class="text-3xl font-bold text-blue-900">{{ stats.total_workloads }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-200 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl shadow-sm border border-green-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-600 mb-1">Joriy yuklamalar</p>
                            <p class="text-3xl font-bold text-green-900">{{ stats.current_workloads }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-200 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl shadow-sm border border-purple-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-purple-600 mb-1">Jami soatlar</p>
                            <p class="text-3xl font-bold text-purple-900">{{ stats.total_hours }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-200 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Info -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Professional Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-xl font-semibold text-gray-800">Kasbiy ma'lumotlar</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Kafedra</label>
                            <p class="text-gray-900 font-medium">{{ teacher.department?.name || '—' }}</p>
                            <p class="text-sm text-gray-500">{{ teacher.department?.faculty?.name || '—' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Lavozim</label>
                            <p class="text-gray-900">{{ teacher.position || '—' }}</p>
                        </div>

                        <div v-if="teacher.academic_degree">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Ilmiy daraja</label>
                            <p class="text-gray-900">{{ teacher.academic_degree }}</p>
                        </div>

                        <div v-if="teacher.academic_title">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Ilmiy unvon</label>
                            <p class="text-gray-900">{{ teacher.academic_title }}</p>
                        </div>

                        <div v-if="teacher.hire_date">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Ishga qabul qilish
                                sanasi</label>
                            <p class="text-gray-900">{{ formatDate(teacher.hire_date) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-xl font-semibold text-gray-800">Shaxsiy ma'lumotlar</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div v-if="teacher.birth_date">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Tug'ilgan sana</label>
                            <p class="text-gray-900">{{ formatDate(teacher.birth_date) }}</p>
                        </div>

                        <div v-if="teacher.passport_serial">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Pasport seriyasi</label>
                            <p class="text-gray-900">{{ teacher.passport_serial }}</p>
                        </div>

                        <div v-if="teacher.inn">
                            <label class="block text-sm font-medium text-gray-500 mb-1">INN</label>
                            <p class="text-gray-900">{{ teacher.inn }}</p>
                        </div>

                        <div v-if="teacher.address">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Manzil</label>
                            <p class="text-gray-900">{{ teacher.address }}</p>
                        </div>

                        <div v-if="!teacher.birth_date && !teacher.passport_serial && !teacher.inn && !teacher.address"
                             class="text-center py-8 text-gray-500">
                            <p class="text-sm">Qo'shimcha ma'lumotlar kiritilmagan</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Workloads List -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-800">Yuklamalar</h2>
                    <span class="text-sm text-gray-500">{{ teacher.workloads?.length || 0 }} ta</span>
                </div>
                <div class="p-6">
                    <div v-if="teacher.workloads && teacher.workloads.length > 0" class="space-y-4">
                        <div v-for="workload in teacher.workloads" :key="workload.id"
                             class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h3 class="font-medium text-gray-900">{{ workload.subject?.name }}</h3>
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                        {{ workload.group?.name }}
                                    </span>
                                </div>
                                <div class="flex items-center space-x-4 text-sm text-gray-600">
                                    <span>{{ workload.total_hours }} soat</span>
                                    <span>•</span>
                                    <span>{{ workload.semester?.name }}</span>
                                </div>
                            </div>
                            <Link :href="`/workloads/${workload.id}`"
                                  class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                Batafsil →
                            </Link>
                        </div>
                    </div>
                    <div v-else class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">Hozircha yuklamalar yo'q</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-between items-center">
                <Link href="/teachers"
                      class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Orqaga</span>
                </Link>

                <button @click="showDeleteModal = true"
                        class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    O'chirish
                </button>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">O'qituvchini o'chirish</h3>
                <p class="text-gray-600 mb-6">
                    <strong>{{ teacher.user?.name }}</strong> o'qituvchisini o'chirishni xohlaysizmi? Bu amal user
                    akkauntini
                    ham o'chiradi va qaytarib bo'lmaydi.
                </p>
                <div class="flex justify-end space-x-3">
                    <button @click="showDeleteModal = false"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Bekor qilish
                    </button>
                    <button @click="confirmDelete" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        O'chirish
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3';
import { useToast } from '@/Composables/useToast'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const toast = useToast()

const props = defineProps({
    teacher: Object,
    stats: Object,
});

const showDeleteModal = ref(false);

const getEmploymentTypeName = (type) => {
    const types = {
        'main_job': 'Asosiy ish joyi',
        'internal_part_time': 'O\'rindoshlik (ichki-asosiy)',
        'internal_additional': 'O\'rindoshlik (ichki-qo\'shimcha)',
        'external_part_time': 'O\'rindoshlik (tashqi)',
        'hourly': 'Soatbay'
    };
    return types[type] || 'Noma\'lum';
};

const formatDate = (date) => {
    if (!date) return '';

    const [year, month, day] = date.split('T')[0].split('-');
    return `${day}.${month}.${year}`;
};

const confirmDelete = () => {
    router.delete(`/teachers/${props.teacher.id}`, {
        onSuccess: () => {
            toast.success("O'qituvchi muvaffaqiyatli o'chirildi!");
            router.visit('/teachers');
        },
    });
};
</script>
