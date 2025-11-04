<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    workload: Object,
    departments: Array,
    teachers: Array,
    subjects: Array,
    groups: Array,
    directions: Array,
    academicYears: Array,
});

const form = useForm({
    department_id: props.workload.department_id,
    direction_id: props.workload.direction_id,
    subject_id: props.workload.subject_id,
    teacher_id: props.workload.teacher_id,
    academic_year_id: props.workload.academic_year_id,
    group_ids: props.workload.groups?.map(g => g.id) || [],

    // 1-semestr
    semester_1_lecture: props.workload.semester_1_lecture || 0,
    semester_1_practical: props.workload.semester_1_practical || 0,
    semester_1_laboratory: props.workload.semester_1_laboratory || 0,
    semester_1_seminar: props.workload.semester_1_seminar || 0,
    semester_1_practice: props.workload.semester_1_practice || 0,
    semester_1_exam: props.workload.semester_1_exam || 0,
    semester_1_test: props.workload.semester_1_test || 0,

    // 2-semestr
    semester_2_lecture: props.workload.semester_2_lecture || 0,
    semester_2_practical: props.workload.semester_2_practical || 0,
    semester_2_laboratory: props.workload.semester_2_laboratory || 0,
    semester_2_seminar: props.workload.semester_2_seminar || 0,
    semester_2_practice: props.workload.semester_2_practice || 0,
    semester_2_exam: props.workload.semester_2_exam || 0,
    semester_2_test: props.workload.semester_2_test || 0,

    // Umumiy
    coursework_hours: props.workload.coursework_hours || 0,
    diploma_hours: props.workload.diploma_hours || 0,
    consultation_hours: props.workload.consultation_hours || 0,

    notes: props.workload.notes || '',
});

// Filtered data
const filteredTeachers = computed(() => {
    if (!form.department_id) return [];
    return props.teachers.filter(t => t.department_id == form.department_id);
});

const filteredSubjects = computed(() => {
    if (!form.department_id) return [];
    return props.subjects.filter(s => s.department_id == form.department_id);
});

const filteredGroups = computed(() => {
    if (!form.direction_id) return [];
    return props.groups.filter(g => g.direction_id == form.direction_id);
});

// Computed totals
const semester1Total = computed(() => {
    return Number(form.semester_1_lecture || 0) +
        Number(form.semester_1_practical || 0) +
        Number(form.semester_1_laboratory || 0) +
        Number(form.semester_1_seminar || 0) +
        Number(form.semester_1_practice || 0) +
        Number(form.semester_1_exam || 0) +
        Number(form.semester_1_test || 0);
});

const semester2Total = computed(() => {
    return Number(form.semester_2_lecture || 0) +
        Number(form.semester_2_practical || 0) +
        Number(form.semester_2_laboratory || 0) +
        Number(form.semester_2_seminar || 0) +
        Number(form.semester_2_practice || 0) +
        Number(form.semester_2_exam || 0) +
        Number(form.semester_2_test || 0);
});

const totalHours = computed(() => {
    return semester1Total.value +
        semester2Total.value +
        Number(form.coursework_hours || 0) +
        Number(form.diploma_hours || 0) +
        Number(form.consultation_hours || 0);
});

// Submit
const submit = () => {
    form.put(`/workloads/${props.workload.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            // Success
        },
    });
};
</script>

<template>
    <AuthenticatedLayout title="Yuklamani tahrirlash">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Yuklamani tahrirlash
                </h2>
                <Link
                    :href="`/workloads/${workload.id}`"
                    class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300"
                >
                    ← Orqaga
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <form @submit.prevent="submit">
                    <!-- Asosiy ma'lumotlar (Read-only) -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Asosiy ma'lumotlar (o'zgartirish mumkin emas)</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Kafedra</label>
                                    <p class="text-base font-semibold">{{ workload.department?.name }}</p>
                                </div>

                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Yo'nalish</label>
                                    <p class="text-base font-semibold">{{ workload.direction?.name }}</p>
                                </div>

                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Fan</label>
                                    <p class="text-base font-semibold">{{ workload.subject?.name }}</p>
                                </div>

                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <label class="block text-sm font-medium text-gray-500 mb-1">O'qituvchi</label>
                                    <p class="text-base font-semibold">{{ workload.teacher?.full_name }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Guruhlar (Read-only) -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Guruhlar (o'zgartirish mumkin emas)</h3>

                            <div class="flex flex-wrap gap-2">
                                <span
                                    v-for="group in workload.groups"
                                    :key="group.id"
                                    class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-50 text-blue-800 font-medium"
                                >
                                    {{ group.name }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Potok warning -->
                    <div v-if="workload.is_potok" class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6 rounded-lg">
                        <p class="text-yellow-800">
                            ⚠️ Bu potok yuklamasi. Faqat ma'ruza soatlarini o'zgartirishingiz mumkin.
                        </p>
                    </div>

                    <!-- 1-Semestr Soatlari -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">1-Semestr soatlari</h3>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Ma'ruza</label>
                                    <input
                                        v-model.number="form.semester_1_lecture"
                                        type="number"
                                        min="0"
                                        step="0.5"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Amaliy</label>
                                    <input
                                        v-model.number="form.semester_1_practical"
                                        type="number"
                                        min="0"
                                        step="0.5"
                                        :disabled="workload.is_potok"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 disabled:bg-gray-100"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Laboratoriya</label>
                                    <input
                                        v-model.number="form.semester_1_laboratory"
                                        type="number"
                                        min="0"
                                        step="0.5"
                                        :disabled="workload.is_potok"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 disabled:bg-gray-100"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Seminar</label>
                                    <input
                                        v-model.number="form.semester_1_seminar"
                                        type="number"
                                        min="0"
                                        step="0.5"
                                        :disabled="workload.is_potok"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 disabled:bg-gray-100"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Amaliyot</label>
                                    <input
                                        v-model.number="form.semester_1_practice"
                                        type="number"
                                        min="0"
                                        step="0.5"
                                        :disabled="workload.is_potok"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 disabled:bg-gray-100"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Imtihon</label>
                                    <input
                                        v-model.number="form.semester_1_exam"
                                        type="number"
                                        min="0"
                                        step="0.5"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Test</label>
                                    <input
                                        v-model.number="form.semester_1_test"
                                        type="number"
                                        min="0"
                                        step="0.5"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                </div>
                            </div>

                            <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-700">
                                    1-semestr jami: <strong>{{ semester1Total }}</strong> soat
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- 2-Semestr Soatlari -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">2-Semestr soatlari</h3>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Ma'ruza</label>
                                    <input
                                        v-model.number="form.semester_2_lecture"
                                        type="number"
                                        min="0"
                                        step="0.5"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Amaliy</label>
                                    <input
                                        v-model.number="form.semester_2_practical"
                                        type="number"
                                        min="0"
                                        step="0.5"
                                        :disabled="workload.is_potok"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 disabled:bg-gray-100"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Laboratoriya</label>
                                    <input
                                        v-model.number="form.semester_2_laboratory"
                                        type="number"
                                        min="0"
                                        step="0.5"
                                        :disabled="workload.is_potok"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 disabled:bg-gray-100"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Seminar</label>
                                    <input
                                        v-model.number="form.semester_2_seminar"
                                        type="number"
                                        min="0"
                                        step="0.5"
                                        :disabled="workload.is_potok"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 disabled:bg-gray-100"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Amaliyot</label>
                                    <input
                                        v-model.number="form.semester_2_practice"
                                        type="number"
                                        min="0"
                                        step="0.5"
                                        :disabled="workload.is_potok"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 disabled:bg-gray-100"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Imtihon</label>
                                    <input
                                        v-model.number="form.semester_2_exam"
                                        type="number"
                                        min="0"
                                        step="0.5"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Test</label>
                                    <input
                                        v-model.number="form.semester_2_test"
                                        type="number"
                                        min="0"
                                        step="0.5"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                </div>
                            </div>

                            <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-700">
                                    2-semestr jami: <strong>{{ semester2Total }}</strong> soat
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Umumiy Soatlar -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Umumiy soatlar</h3>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kurs ishi</label>
                                    <input
                                        v-model.number="form.coursework_hours"
                                        type="number"
                                        min="0"
                                        step="0.5"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Diplom ishi</label>
                                    <input
                                        v-model.number="form.diploma_hours"
                                        type="number"
                                        min="0"
                                        step="0.5"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Konsultatsiya</label>
                                    <input
                                        v-model.number="form.consultation_hours"
                                        type="number"
                                        min="0"
                                        step="0.5"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Qo'shimcha ma'lumot</h3>

                            <textarea
                                v-model="form.notes"
                                rows="3"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Izoh yoki qo'shimcha ma'lumotlar..."
                            ></textarea>
                        </div>
                    </div>

                    <!-- Total Summary -->
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-6 mb-6 rounded-lg">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-semibold text-blue-900">JAMI SOATLAR</h3>
                                <p class="text-sm text-blue-700 mt-1">
                                    1-semestr: {{ semester1Total }} | 2-semestr: {{ semester2Total }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-3xl font-bold text-blue-900">{{ totalHours }}</p>
                                <p class="text-sm text-blue-700">soat</p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end gap-4">
                        <Link
                            :href="`/workloads/${workload.id}`"
                            class="px-6 py-3 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition"
                        >
                            Bekor qilish
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition disabled:opacity-50"
                        >
                            <span v-if="form.processing">Saqlanmoqda...</span>
                            <span v-else>O'zgarishlarni saqlash</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
