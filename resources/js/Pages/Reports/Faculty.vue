<template>
    <Head :title="`${faculty.name} - Hisobot`" />
    
    <AuthenticatedLayout>
        <template #header>Fakultet hisoboti</template>

        <div class="space-y-6">
            <!-- Faculty Info Card -->
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-8 text-white">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-3xl font-bold">{{ faculty.name }}</h2>
                            <p class="text-purple-100 mt-1">Kod: {{ faculty.code || 'Belgilanmagan' }}</p>
                            <p class="text-purple-100">Dekan: {{ faculty.dean || 'Belgilanmagan' }}</p>
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
                    
                    <!-- Semester Filter -->
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
                            <p class="text-sm font-medium text-blue-600 mb-1">Kafedralar</p>
                            <p class="text-3xl font-bold text-blue-900">{{ totalStats.departments_count }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-200 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl shadow-sm border border-green-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-600 mb-1">O'qituvchilar</p>
                            <p class="text-3xl font-bold text-green-900">{{ totalStats.teachers_count }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-200 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl shadow-sm border border-purple-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-purple-600 mb-1">Jami soatlar</p>
                            <p class="text-3xl font-bold text-purple-900">{{ totalStats.total_hours }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-200 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Departments Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-xl font-semibold text-gray-800">Kafedralar bo'yicha</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kafedra</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mudiri</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">O'qituvchilar</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jami soat</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">O'rtacha yuklama</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amallar</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="(stat, index) in departmentStats" :key="stat.department.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ stat.department.name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ stat.department.head || '—' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ stat.teachers_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-semibold text-purple-600">{{ stat.total_hours }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ stat.average_hours }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <Link
                                        :href="`/reports/department/${stat.department.id}?semester_id=${currentSemester || ''}`"
                                        class="text-indigo-600 hover:text-indigo-900"
                                    >
                                        Batafsil
                                    </Link>
                                </td>
                            </tr>
                            
                            <!-- Summary Row -->
                            <tr v-if="departmentStats.length > 0" class="bg-purple-50 font-semibold">
                                <td colspan="3" class="px-6 py-4 text-right text-gray-900">JAMI:</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-200 text-blue-900">
                                        {{ totalStats.teachers_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-purple-600 text-lg">{{ totalStats.total_hours }}</span>
                                </td>
                                <td colspan="2"></td>
                            </tr>
                            
                            <!-- Empty State -->
                            <tr v-if="departmentStats.length === 0">
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <p class="mt-2 text-sm font-medium">Ma'lumot topilmadi</p>
                                    <p class="mt-1 text-xs">Tanlangan semestrda hech qanday yuklama mavjud emas</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Additional Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Average per Department -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">O'rtacha ko'rsatkichlar</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-600">Kafedra uchun o'qituvchilar:</span>
                            <span class="text-sm font-semibold text-gray-900">
                                {{ totalStats.departments_count > 0 
                                    ? (totalStats.teachers_count / totalStats.departments_count).toFixed(1) 
                                    : 0 }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-600">Kafedra uchun soatlar:</span>
                            <span class="text-sm font-semibold text-gray-900">
                                {{ totalStats.departments_count > 0 
                                    ? (totalStats.total_hours / totalStats.departments_count).toFixed(1) 
                                    : 0 }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-600">O'qituvchi uchun soatlar:</span>
                            <span class="text-sm font-semibold text-gray-900">
                                {{ totalStats.teachers_count > 0 
                                    ? (totalStats.total_hours / totalStats.teachers_count).toFixed(1) 
                                    : 0 }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Top Departments -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Eng yuqori yuklamali kafedralar</h3>
                    <div class="space-y-3">
                        <div 
                            v-for="(stat, index) in topDepartments" 
                            :key="stat.department.id"
                            class="flex items-center justify-between p-3 bg-gradient-to-r from-purple-50 to-transparent rounded-lg border border-purple-100"
                        >
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-purple-500 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                    {{ index + 1 }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ stat.department.name }}</p>
                                    <p class="text-xs text-gray-500">{{ stat.teachers_count }} o'qituvchi</p>
                                </div>
                            </div>
                            <span class="text-sm font-semibold text-purple-600">{{ stat.total_hours }} soat</span>
                        </div>
                        <div v-if="topDepartments.length === 0" class="text-center py-4 text-gray-500 text-sm">
                            Ma'lumot mavjud emas
                        </div>
                    </div>
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

                <div class="text-sm text-gray-500">
                    <span class="font-medium">Semestr:</span> 
                    {{ currentSemester ? semesters.find(s => s.id == currentSemester)?.name : 'Joriy' }}
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    faculty: Object,
    departmentStats: Array,
    totalStats: Object,
    semesters: Array,
    selectedSemester: [String, Number],
    canExport: Boolean,
});

const currentSemester = ref(props.selectedSemester);

// Top 3 kafedralarni yuklama bo'yicha
const topDepartments = computed(() => {
    return [...props.departmentStats]
        .sort((a, b) => b.total_hours - a.total_hours)
        .slice(0, 3);
});

const changeSemester = () => {
    const params = currentSemester.value ? `?semester_id=${currentSemester.value}` : '';
    router.visit(`/reports/faculty/${props.faculty.id}${params}`);
};

const exportExcel = () => {
    const params = new URLSearchParams({
        type: 'faculty',
        faculty_id: props.faculty.id,
        semester_id: currentSemester.value || '',
    });
    
    window.location.href = `/reports/export/excel?${params.toString()}`;
};

const exportPdf = () => {
    const params = new URLSearchParams({
        type: 'faculty',
        faculty_id: props.faculty.id,
        semester_id: currentSemester.value || '',
    });
    
    window.location.href = `/reports/export/pdf?${params.toString()}`;
};
</script>