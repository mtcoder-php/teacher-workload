<template>
    <Head title="Hisobotlar" />
    
    <AuthenticatedLayout>
        <template #header>Hisobotlar</template>

        <div class="space-y-6">
            <!-- Filter Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Semestr tanlash</h3>
                <select
                    v-model="selectedSemester"
                    @change="updateReports"
                    class="w-full md:w-64 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
                    <option :value="null">Joriy semestr</option>
                    <option v-for="semester in semesters" :key="semester.id" :value="semester.id">
                        {{ semester.name }}
                    </option>
                </select>
            </div>

            <!-- Report Types Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Teacher Report -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white">O'qituvchilar</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-600 mb-4">O'qituvchi bo'yicha yuklama hisoboti</p>
                        <select
                            v-model="selectedTeacher"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 mb-4"
                        >
                            <option :value="null">O'qituvchi tanlang</option>
                            <option v-for="teacher in teachers" :key="teacher.id" :value="teacher.id">
                                {{ teacher.full_name }}
                            </option>
                        </select>
                        <button
                            @click="viewTeacherReport"
                            :disabled="!selectedTeacher"
                            class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition"
                        >
                            Hisobotni ko'rish
                        </button>
                    </div>
                </div>

                <!-- Department Report -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
                    <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white">Kafedralar</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-600 mb-4">Kafedra bo'yicha yuklama hisoboti</p>
                        <select
                            v-model="selectedDepartment"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 mb-4"
                        >
                            <option :value="null">Kafedra tanlang</option>
                            <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                                {{ dept.name }}
                            </option>
                        </select>
                        <button
                            @click="viewDepartmentReport"
                            :disabled="!selectedDepartment"
                            class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition"
                        >
                            Hisobotni ko'rish
                        </button>
                    </div>
                </div>

                <!-- Faculty Report -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white">Fakultetlar</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-600 mb-4">Fakultet bo'yicha yuklama hisoboti</p>
                        <select
                            v-model="selectedFaculty"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 mb-4"
                        >
                            <option :value="null">Fakultet tanlang</option>
                            <option v-for="faculty in faculties" :key="faculty.id" :value="faculty.id">
                                {{ faculty.name }}
                            </option>
                        </select>
                        <button
                            @click="viewFacultyReport"
                            :disabled="!selectedFaculty"
                            class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed transition"
                        >
                            Hisobotni ko'rish
                        </button>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Tezkor statistika</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <p class="text-2xl font-bold text-blue-600">{{ teachers.length }}</p>
                        <p class="text-sm text-gray-600">O'qituvchilar</p>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <p class="text-2xl font-bold text-green-600">{{ departments.length }}</p>
                        <p class="text-sm text-gray-600">Kafedralar</p>
                    </div>
                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                        <p class="text-2xl font-bold text-purple-600">{{ faculties.length }}</p>
                        <p class="text-sm text-gray-600">Fakultetlar</p>
                    </div>
                    <div class="text-center p-4 bg-indigo-50 rounded-lg">
                        <p class="text-2xl font-bold text-indigo-600">{{ semesters.length }}</p>
                        <p class="text-sm text-gray-600">Semestrlar</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    semesters: Array,
    teachers: Array,
    departments: Array,
    faculties: Array,
    canExport: Boolean,
});

const selectedSemester = ref(null);
const selectedTeacher = ref(null);
const selectedDepartment = ref(null);
const selectedFaculty = ref(null);

const updateReports = () => {
    // Semestr o'zgarganda sahifani yangilash
    router.reload({
        data: {
            semester_id: selectedSemester.value
        }
    });
};

const viewTeacherReport = () => {
    if (selectedTeacher.value) {
        const params = selectedSemester.value ? `?semester_id=${selectedSemester.value}` : '';
        router.visit(`/reports/teacher/${selectedTeacher.value}${params}`);
    }
};

const viewDepartmentReport = () => {
    if (selectedDepartment.value) {
        const params = selectedSemester.value ? `?semester_id=${selectedSemester.value}` : '';
        router.visit(`/reports/department/${selectedDepartment.value}${params}`);
    }
};

const viewFacultyReport = () => {
    if (selectedFaculty.value) {
        const params = selectedSemester.value ? `?semester_id=${selectedSemester.value}` : '';
        router.visit(`/reports/faculty/${selectedFaculty.value}${params}`);
    }
};
</script>