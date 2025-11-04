<template>
    <Head :title="`${department.name} - Hisobot`" />
    
    <AuthenticatedLayout>
        <template #header>Kafedra hisoboti</template>

        <div class="space-y-6">
            <!-- Department Info Card -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-8 text-white">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-3xl font-bold">{{ department.name }}</h2>
                            <p class="text-green-100 mt-1">{{ department.faculty?.name }}</p>
                            <p class="text-green-100">Mudiri: {{ department.head || 'Belgilanmagan' }}</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button
                                v-if="canExport"
                                @click="exportExcel"
                                class="px-4 py-2 bg-white bg-opacity-20 rounded-lg hover:bg-opacity-30 transition"
                            >
                                Excel
                            </button>
                            <button
                                v-if="canExport"
                                @click="exportPdf"
                                class="px-4 py-2 bg-white bg-opacity-20 rounded-lg hover:bg-opacity-30 transition"
                            >
                                PDF
                            </button>
                        </div>
                    </div>
                    
                    <select
                        v-model="currentSemester"
                        @change="changeSemester"
                        class="px-4 py-2 text-gray-900 rounded-lg focus:ring-2 focus:ring-white pr-10 appearance-none bg-white"
                    >
                        <option :value="null">Joriy semestr</option>
                        <option v-for="semester in semesters" :key="semester.id" :value="semester.id">
                            {{ semester.name }}
                        </option>
                    </select>
                </div>
            </div>

            <!-- Statistics Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow-sm border border-blue-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-600 mb-1">O'qituvchilar</p>
                            <p class="text-3xl font-bold text-blue-900">{{ totalStats.teachers_count }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-200 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl shadow-sm border border-green-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-600 mb-1">Jami soatlar</p>
                            <p class="text-3xl font-bold text-green-900">{{ totalStats.total_hours }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-200 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl shadow-sm border border-purple-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-purple-600 mb-1">O'rtacha yuklama</p>
                            <p class="text-3xl font-bold text-purple-900">{{ totalStats.average_hours }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-200 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Teachers Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-xl font-semibold text-gray-800">O'qituvchilar bo'yicha</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">O'qituvchi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lavozim</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Yuklamalar</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ma'ruza</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seminar</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amaliy</th>                                
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jami soat</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amallar</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="(stat, index) in teacherStats" :key="stat.teacher.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ stat.teacher.full_name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ stat.teacher.position || '—' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ stat.workloads_count }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ stat.lecture_hours }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ stat.seminar_hours }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ stat.practical_hours }}</td>                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-semibold text-green-600">{{ stat.total_hours }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <Link
                                        :href="`/reports/teacher/${stat.teacher.id}?semester_id=${currentSemester || ''}`"
                                        class="text-indigo-600 hover:text-indigo-900"
                                    >
                                        Batafsil
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-between items-center">
                <Link
                    href="/reports"
                    class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
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
    department: Object,
    teacherStats: Array,
    totalStats: Object,
    semesters: Array,
    selectedSemester: [String, Number],
    canExport: Boolean,
});

const currentSemester = ref(props.selectedSemester);

const changeSemester = () => {
    const params = currentSemester.value ? `?semester_id=${currentSemester.value}` : '';
    router.visit(`/reports/department/${props.department.id}${params}`);
};

const exportExcel = () => {
    const params = new URLSearchParams({
        type: 'department',
        department_id: props.department.id,
        semester_id: currentSemester.value || '',
    });
    
    window.location.href = `/reports/export/excel?${params.toString()}`;
};

const exportPdf = () => {
    const params = new URLSearchParams({
        type: 'department',
        department_id: props.department.id,
        semester_id: currentSemester.value || '',
    });
    
    window.location.href = `/reports/export/pdf?${params.toString()}`;
};
</script>