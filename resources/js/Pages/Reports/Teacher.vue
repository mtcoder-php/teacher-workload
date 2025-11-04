<template>

    <Head :title="`${teacher.full_name} - Hisobot`" />

    <AuthenticatedLayout>
        <template #header>O'qituvchi hisoboti</template>

        <div class="space-y-6">
            <!-- Teacher Info Card -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-8 text-white">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-3xl font-bold">{{ teacher.full_name }}</h2>
                            <p class="text-blue-100 mt-1">{{ teacher.position }}</p>
                            <p class="text-blue-100">{{ teacher.department?.name }}</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button v-if="canExport" @click="exportExcel"
                                class="px-4 py-2 bg-white bg-opacity-20 rounded-lg hover:bg-opacity-30 transition">
                                Excel
                            </button>
                            <button v-if="canExport" @click="exportPdf"
                                class="px-4 py-2 bg-white bg-opacity-20 rounded-lg hover:bg-opacity-30 transition">
                                PDF
                            </button>
                        </div>
                    </div>

                    <!-- Semester Filter -->
                    <select v-model="currentSemester" @change="changeSemester"
                        class="px-4 py-2 pr-10 text-gray-900 rounded-lg focus:ring-2 focus:ring-white appearance-none bg-white">
                        <option :value="null">Joriy semestr</option>
                        <option v-for="semester in semesters" :key="semester.id" :value="semester.id">
                            {{ semester.name }}
                        </option>
                    </select>
                </div>
            </div>

            <!-- Statistics Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow-sm border border-blue-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-600 mb-1">Jami soatlar</p>
                            <p class="text-3xl font-bold text-blue-900">{{ stats.total_hours }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-200 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl shadow-sm border border-green-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-600 mb-1">Fanlar soni</p>
                            <p class="text-3xl font-bold text-green-900">{{ stats.total_subjects }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-200 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl shadow-sm border border-purple-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-purple-600 mb-1">Guruhlar soni</p>
                            <p class="text-3xl font-bold text-purple-900">{{ stats.total_groups }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-200 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl shadow-sm border border-indigo-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-indigo-600 mb-1">Ma'ruza soatlari</p>
                            <p class="text-3xl font-bold text-indigo-900">{{ stats.lecture_hours }}</p>
                        </div>
                        <div class="w-12 h-12 bg-indigo-200 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Workloads Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-xl font-semibold text-gray-800">Yuklama tafsilotlari</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    #
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Fan
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Guruh
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ma'ruza</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Seminar</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Amaliy</th>

                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Imtihon</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jami
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="(workload, index) in workloads" :key="workload.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ workload.subject.name }}</div>
                                    <div class="text-xs text-gray-500">{{ workload.subject.code }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ workload.group.name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ workload.lecture_hours }}
                                 </td>
                                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ workload.seminar_hours }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{
                                    workload.practical_hours }}
                                </td>
                              
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ workload.exam_hours }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-semibold text-indigo-600">{{ workload.total_hours }}</span>
                                </td>
                            </tr>

                            <!-- Total Row -->
                            <tr class="bg-indigo-50 font-semibold">
                                <td colspan="3" class="px-6 py-4 text-right text-gray-900">JAMI:</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-900">{{ stats.lecture_hours }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-900">{{ stats.seminar_hours }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-900">{{ stats.practical_hours }}</td>                                
                                <td class="px-6 py-4 whitespace-nowrap text-gray-900">{{ stats.exam_hours }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-indigo-600 text-lg">{{ stats.total_hours }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-between items-center">
                <Link href="/reports"
                    class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Orqaga</span>
                </Link>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    teacher: Object,
    workloads: Array,
    stats: Object,
    semesters: Array,
    selectedSemester: [String, Number],
    canExport: Boolean,
});

const currentSemester = ref(props.selectedSemester);

const changeSemester = () => {
    const params = currentSemester.value ? `?semester_id=${currentSemester.value}` : '';
    router.visit(`/reports/teacher/${props.teacher.id}${params}`);
};

const exportExcel = () => {
    const params = new URLSearchParams({
        type: 'teacher',
        teacher_id: props.teacher.id,
        semester_id: currentSemester.value || '',
    });

    window.location.href = `/reports/export/excel?${params.toString()}`;
};

const exportPdf = () => {
    const params = new URLSearchParams({
        type: 'teacher',
        teacher_id: props.teacher.id,
        semester_id: currentSemester.value || '',
    });

    window.location.href = `/reports/export/pdf?${params.toString()}`;
};


</script>