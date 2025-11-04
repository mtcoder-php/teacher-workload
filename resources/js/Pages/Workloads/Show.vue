<script setup>
import { Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    workload: Object,
});

const getStatusBadge = (status) => {
    const badges = {
        draft: 'bg-gray-100 text-gray-800',
        pending: 'bg-yellow-100 text-yellow-800',
        confirmed: 'bg-green-100 text-green-800',
        completed: 'bg-blue-100 text-blue-800',
    };
    return badges[status] || 'bg-gray-100 text-gray-800';
};

const getStatusLabel = (status) => {
    const labels = {
        draft: 'Qoralama',
        pending: 'Tekshiruvda',
        confirmed: 'Tasdiqlangan',
        completed: 'Tugatilgan',
    };
    return labels[status] || status;
};
</script>

<template>
    <AuthenticatedLayout title="Yuklama ma'lumotlari">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Yuklama ma'lumotlari
                </h2>
                <div class="flex gap-2">
                    <Link
                        v-if="workload.status === 'draft'"
                        :href="`/workloads/${workload.id}/edit`"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
                    >
                        Tahrirlash
                    </Link>
                    <Link
                        href="/workloads"
                        class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300"
                    >
                        ← Orqaga
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Asosiy ma'lumotlar -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-6">
                            <h3 class="text-lg font-semibold">Asosiy ma'lumotlar</h3>
                            <span
                                :class="getStatusBadge(workload.status)"
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                            >
                                {{ getStatusLabel(workload.status) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Fan</label>
                                <p class="text-lg font-semibold">{{ workload.subject?.name }}</p>
                                <p class="text-sm text-gray-600">{{ workload.subject?.code }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">O'qituvchi</label>
                                <p class="text-lg font-semibold">{{ workload.teacher?.full_name }}</p>
                                <p class="text-sm text-gray-600">{{ workload.teacher?.position }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Kafedra</label>
                                <p class="text-base">{{ workload.department?.name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Yo'nalish</label>
                                <p class="text-base">{{ workload.direction?.name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">O'quv yili</label>
                                <p class="text-base">{{ workload.academic_year?.name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Yuklama turi</label>
                                <div class="flex gap-2">
                                    <span
                                        v-if="workload.is_potok"
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800"
                                    >
                                        📚 Potok: {{ workload.potok_code }}
                                    </span>
                                    <span
                                        v-else-if="workload.is_potok_remainder"
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800"
                                    >
                                        📝 Potok qoldig'i
                                    </span>
                                    <span
                                        v-else
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800"
                                    >
                                        📖 Oddiy yuklama
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Guruhlar -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Guruhlar ({{ workload.groups?.length || 0 }})</h3>

                        <div class="flex flex-wrap gap-2">
                            <span
                                v-for="group in workload.groups"
                                :key="group.id"
                                class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-50 text-blue-800 font-medium"
                            >
                                {{ group.name }}
                                <span class="ml-2 text-sm text-blue-600">({{ group.students_count || 0 }} ta talaba)</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- 1-Semestr Soatlari -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">1-Semestr soatlari</h3>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <label class="block text-sm font-medium text-gray-500 mb-1">Ma'ruza</label>
                                <p class="text-2xl font-bold text-gray-900">{{ workload.semester_1_lecture || 0 }}</p>
                            </div>

                            <div class="p-4 bg-gray-50 rounded-lg">
                                <label class="block text-sm font-medium text-gray-500 mb-1">Amaliy</label>
                                <p class="text-2xl font-bold text-gray-900">{{ workload.semester_1_practical || 0 }}</p>
                            </div>

                            <div class="p-4 bg-gray-50 rounded-lg">
                                <label class="block text-sm font-medium text-gray-500 mb-1">Laboratoriya</label>
                                <p class="text-2xl font-bold text-gray-900">{{ workload.semester_1_laboratory || 0 }}</p>
                            </div>

                            <div class="p-4 bg-gray-50 rounded-lg">
                                <label class="block text-sm font-medium text-gray-500 mb-1">Seminar</label>
                                <p class="text-2xl font-bold text-gray-900">{{ workload.semester_1_seminar || 0 }}</p>
                            </div>

                            <div class="p-4 bg-gray-50 rounded-lg">
                                <label class="block text-sm font-medium text-gray-500 mb-1">Amaliyot</label>
                                <p class="text-2xl font-bold text-gray-900">{{ workload.semester_1_practice || 0 }}</p>
                            </div>

                            <div class="p-4 bg-gray-50 rounded-lg">
                                <label class="block text-sm font-medium text-gray-500 mb-1">Imtihon</label>
                                <p class="text-2xl font-bold text-gray-900">{{ workload.semester_1_exam || 0 }}</p>
                            </div>

                            <div class="p-4 bg-gray-50 rounded-lg">
                                <label class="block text-sm font-medium text-gray-500 mb-1">Test</label>
                                <p class="text-2xl font-bold text-gray-900">{{ workload.semester_1_test || 0 }}</p>
                            </div>
                        </div>

                        <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                            <p class="text-sm text-blue-800">
                                1-semestr jami: <strong class="text-lg">{{
                                    (workload.semester_1_lecture || 0) +
                                    (workload.semester_1_practical || 0) +
                                    (workload.semester_1_laboratory || 0) +
                                    (workload.semester_1_seminar || 0) +
                                    (workload.semester_1_practice || 0) +
                                    (workload.semester_1_exam || 0) +
                                    (workload.semester_1_test || 0)
                                }}</strong> soat
                            </p>
                        </div>
                    </div>
                </div>

                <!-- 2-Semestr Soatlari -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">2-Semestr soatlari</h3>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <label class="block text-sm font-medium text-gray-500 mb-1">Ma'ruza</label>
                                <p class="text-2xl font-bold text-gray-900">{{ workload.semester_2_lecture || 0 }}</p>
                            </div>

                            <div class="p-4 bg-gray-50 rounded-lg">
                                <label class="block text-sm font-medium text-gray-500 mb-1">Amaliy</label>
                                <p class="text-2xl font-bold text-gray-900">{{ workload.semester_2_practical || 0 }}</p>
                            </div>

                            <div class="p-4 bg-gray-50 rounded-lg">
                                <label class="block text-sm font-medium text-gray-500 mb-1">Laboratoriya</label>
                                <p class="text-2xl font-bold text-gray-900">{{ workload.semester_2_laboratory || 0 }}</p>
                            </div>

                            <div class="p-4 bg-gray-50 rounded-lg">
                                <label class="block text-sm font-medium text-gray-500 mb-1">Seminar</label>
                                <p class="text-2xl font-bold text-gray-900">{{ workload.semester_2_seminar || 0 }}</p>
                            </div>

                            <div class="p-4 bg-gray-50 rounded-lg">
                                <label class="block text-sm font-medium text-gray-500 mb-1">Amaliyot</label>
                                <p class="text-2xl font-bold text-gray-900">{{ workload.semester_2_practice || 0 }}</p>
                            </div>

                            <div class="p-4 bg-gray-50 rounded-lg">
                                <label class="block text-sm font-medium text-gray-500 mb-1">Imtihon</label>
                                <p class="text-2xl font-bold text-gray-900">{{ workload.semester_2_exam || 0 }}</p>
                            </div>

                            <div class="p-4 bg-gray-50 rounded-lg">
                                <label class="block text-sm font-medium text-gray-500 mb-1">Test</label>
                                <p class="text-2xl font-bold text-gray-900">{{ workload.semester_2_test || 0 }}</p>
                            </div>
                        </div>

                        <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                            <p class="text-sm text-blue-800">
                                2-semestr jami: <strong class="text-lg">{{
                                    (workload.semester_2_lecture || 0) +
                                    (workload.semester_2_practical || 0) +
                                    (workload.semester_2_laboratory || 0) +
                                    (workload.semester_2_seminar || 0) +
                                    (workload.semester_2_practice || 0) +
                                    (workload.semester_2_exam || 0) +
                                    (workload.semester_2_test || 0)
                                }}</strong> soat
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Umumiy Soatlar -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Umumiy soatlar</h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <label class="block text-sm font-medium text-gray-500 mb-1">Kurs ishi</label>
                                <p class="text-2xl font-bold text-gray-900">{{ workload.coursework_hours || 0 }}</p>
                            </div>

                            <div class="p-4 bg-gray-50 rounded-lg">
                                <label class="block text-sm font-medium text-gray-500 mb-1">Diplom ishi</label>
                                <p class="text-2xl font-bold text-gray-900">{{ workload.diploma_hours || 0 }}</p>
                            </div>

                            <div class="p-4 bg-gray-50 rounded-lg">
                                <label class="block text-sm font-medium text-gray-500 mb-1">Konsultatsiya</label>
                                <p class="text-2xl font-bold text-gray-900">{{ workload.consultation_hours || 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistika -->
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="text-center">
                                <p class="text-sm opacity-90 mb-1">JAMI SOATLAR</p>
                                <p class="text-4xl font-bold">{{ workload.total_hours || 0 }}</p>
                            </div>

                            <div class="text-center">
                                <p class="text-sm opacity-90 mb-1">TALABALAR SONI</p>
                                <p class="text-4xl font-bold">{{ workload.total_students || 0 }}</p>
                            </div>

                            <div class="text-center">
                                <p class="text-sm opacity-90 mb-1">REYTING</p>
                                <p class="text-4xl font-bold">{{ workload.rating || 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Izoh -->
                <div v-if="workload.notes" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Izoh</h3>
                        <p class="text-gray-700">{{ workload.notes }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
