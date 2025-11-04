<script setup>
import { ref, computed, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    departments: Array,
    teachers: Array,
    subjects: Array,
    groups: Array,
    directions: Array,
    academicYears: Array,
    currentAcademicYear: Object,
});

const form = useForm({
    department_id: '',
    direction_id: '',
    subject_id: '',
    teacher_id: '',
    academic_year_id: props.currentAcademicYear?.id || '',
    group_ids: [],
    is_potok: false,

    // 1-semestr
    semester_1_lecture: 0,
    semester_1_practical: 0,
    semester_1_laboratory: 0,
    semester_1_seminar: 0,
    semester_1_practice: 0,
    semester_1_exam: 0,
    semester_1_test: 0,

    // 2-semestr
    semester_2_lecture: 0,
    semester_2_practical: 0,
    semester_2_laboratory: 0,
    semester_2_seminar: 0,
    semester_2_practice: 0,
    semester_2_exam: 0,
    semester_2_test: 0,

    // Umumiy
    coursework_hours: 0,
    diploma_hours: 0,
    consultation_hours: 0,

    notes: '',
});

// Filtered data based on selections
const filteredDirections = computed(() => {
    if (!form.department_id) return [];
    return props.directions.filter(d => d.department_id == form.department_id);
});

const filteredSubjects = computed(() => {
    if (!form.department_id) return [];
    return props.subjects.filter(s => s.department_id == form.department_id);
});

const filteredTeachers = computed(() => {
    if (!form.department_id) return [];
    return props.teachers.filter(t => t.department_id == form.department_id);
});

const filteredGroups = computed(() => {
    if (!form.direction_id) return [];
    return props.groups.filter(g => g.direction_id == form.direction_id);
});

// Watch department - reset dependent fields
watch(() => form.department_id, () => {
    form.direction_id = '';
    form.subject_id = '';
    form.teacher_id = '';
    form.group_ids = [];
});

// Watch direction - reset groups
watch(() => form.direction_id, () => {
    form.group_ids = [];
});

// Watch subject selection - auto-fill hours
watch(() => form.subject_id, async (newVal) => {
    if (!newVal) return;

    console.log('Fan tanlandi:', newVal);

    try {
        const response = await axios.get(`/workloads/ajax/subject/${newVal}/details`);
        console.log('Subject details:', response.data);

        if (response.data.success) {
            const data = response.data.data;

            console.log('Soatlar to\'ldirilmoqda...', data);

            // Auto-fill hours from subject
            form.semester_1_lecture = data.semester_1_lecture || 0;
            form.semester_1_practical = data.semester_1_practical || 0;
            form.semester_1_laboratory = data.semester_1_laboratory || 0;
            form.semester_1_seminar = data.semester_1_seminar || 0;
            form.semester_1_practice = data.semester_1_practice || 0;
            form.semester_1_exam = data.semester_1_exam || 0;
            form.semester_1_test = data.semester_1_test || 0;

            form.semester_2_lecture = data.semester_2_lecture || 0;
            form.semester_2_practical = data.semester_2_practical || 0;
            form.semester_2_laboratory = data.semester_2_laboratory || 0;
            form.semester_2_seminar = data.semester_2_seminar || 0;
            form.semester_2_practice = data.semester_2_practice || 0;
            form.semester_2_exam = data.semester_2_exam || 0;
            form.semester_2_test = data.semester_2_test || 0;

            form.coursework_hours = data.coursework_hours || 0;
            form.diploma_hours = data.diploma_hours || 0;
            form.consultation_hours = data.consultation_hours || 0;

            console.log('Soatlar to\'ldirildi!');
        }
    } catch (error) {
        console.error('Subject details fetch error:', error);
        alert('Fan ma\'lumotlarini yuklashda xatolik yuz berdi!');
    }
});

// Potok mode - clear non-lecture hours
watch(() => form.is_potok, (isPotok) => {
    if (isPotok) {
        // Potokda faqat ma'ruza
        form.semester_1_practical = 0;
        form.semester_1_laboratory = 0;
        form.semester_1_seminar = 0;
        form.semester_1_practice = 0;

        form.semester_2_practical = 0;
        form.semester_2_laboratory = 0;
        form.semester_2_seminar = 0;
        form.semester_2_practice = 0;
    }
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
    form.post('/workloads', {
        preserveScroll: true,
        onSuccess: () => {
            // Success
        },
    });
};
</script>

<template>
    <AuthenticatedLayout title="Yuklama qo'shish">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Yuklama qo'shish
                </h2>
                <Link
                    href="/workloads"
                    class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300"
                >
                    ← Orqaga
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <form @submit.prevent="submit">
                    <!-- Asosiy ma'lumotlar -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Asosiy ma'lumotlar</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Department -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Kafedra <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        v-model="form.department_id"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        required
                                    >
                                        <option value="">Kafedrani tanlang</option>
                                        <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                                            {{ dept.name }}
                                        </option>
                                    </select>
                                    <div v-if="form.errors.department_id" class="text-red-500 text-sm mt-1">
                                        {{ form.errors.department_id }}
                                    </div>
                                </div>

                                <!-- Direction -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Yo'nalish <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        v-model="form.direction_id"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :disabled="!form.department_id"
                                        required
                                    >
                                        <option value="">Yo'nalishni tanlang</option>
                                        <option v-for="dir in filteredDirections" :key="dir.id" :value="dir.id">
                                            {{ dir.name }}
                                        </option>
                                    </select>
                                    <div v-if="form.errors.direction_id" class="text-red-500 text-sm mt-1">
                                        {{ form.errors.direction_id }}
                                    </div>
                                </div>

                                <!-- Subject -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Fan <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        v-model="form.subject_id"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :disabled="!form.department_id"
                                        required
                                    >
                                        <option value="">Fanni tanlang</option>
                                        <option v-for="subject in filteredSubjects" :key="subject.id" :value="subject.id">
                                            {{ subject.name }} ({{ subject.code }})
                                        </option>
                                    </select>
                                    <div v-if="form.errors.subject_id" class="text-red-500 text-sm mt-1">
                                        {{ form.errors.subject_id }}
                                    </div>
                                </div>

                                <!-- Teacher -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        O'qituvchi <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        v-model="form.teacher_id"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :disabled="!form.department_id"
                                        required
                                    >
                                        <option value="">O'qituvchini tanlang</option>
                                        <option v-for="teacher in filteredTeachers" :key="teacher.id" :value="teacher.id">
                                            {{ teacher.full_name }}
                                        </option>
                                    </select>
                                    <div v-if="form.errors.teacher_id" class="text-red-500 text-sm mt-1">
                                        {{ form.errors.teacher_id }}
                                    </div>
                                </div>

                                <!-- Academic Year -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        O'quv yili <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        v-model="form.academic_year_id"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        required
                                    >
                                        <option value="">O'quv yilini tanlang</option>
                                        <option v-for="year in academicYears" :key="year.id" :value="year.id">
                                            {{ year.name }}
                                        </option>
                                    </select>
                                    <div v-if="form.errors.academic_year_id" class="text-red-500 text-sm mt-1">
                                        {{ form.errors.academic_year_id }}
                                    </div>
                                </div>

                                <!-- Potok Checkbox -->
                                <div class="flex items-center">
                                    <input
                                        id="is_potok"
                                        v-model="form.is_potok"
                                        type="checkbox"
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                    />
                                    <label for="is_potok" class="ml-2 block text-sm text-gray-900">
                                        Potok yaratish (bir nechta guruh birlashtiriladi)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Groups Selection -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">
                                Guruhlar
                                <span v-if="form.is_potok" class="text-sm text-blue-600">(Potok uchun kamida 2 ta guruh)</span>
                            </h3>

                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                <label
                                    v-for="group in filteredGroups"
                                    :key="group.id"
                                    class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50"
                                    :class="form.group_ids.includes(group.id) ? 'border-blue-500 bg-blue-50' : 'border-gray-300'"
                                >
                                    <input
                                        v-model="form.group_ids"
                                        type="checkbox"
                                        :value="group.id"
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                    />
                                    <span class="ml-2 text-sm">{{ group.name }}</span>
                                </label>
                            </div>

                            <div v-if="form.errors.group_ids" class="text-red-500 text-sm mt-2">
                                {{ form.errors.group_ids }}
                            </div>

                            <div v-if="form.group_ids.length > 0" class="mt-4 p-3 bg-blue-50 rounded-lg">
                                <p class="text-sm text-blue-800">
                                    Tanlangan: <strong>{{ form.group_ids.length }}</strong> ta guruh
                                </p>
                            </div>
                        </div>
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
                                        :disabled="form.is_potok"
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
                                        :disabled="form.is_potok"
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
                                        :disabled="form.is_potok"
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
                                        :disabled="form.is_potok"
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
                                        :disabled="form.is_potok"
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
                                        :disabled="form.is_potok"
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
                                        :disabled="form.is_potok"
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
                                        :disabled="form.is_potok"
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
                            href="/workloads"
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
                            <span v-else>Saqlash</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
