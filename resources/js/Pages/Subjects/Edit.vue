<template>

    <Head :title="`${subject.name} - Tahrirlash`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center space-x-3">
                <Link href="/subjects" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                </Link>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ subject.name }}</h2>
                    <p class="text-sm text-gray-600">Fanni tahrirlash</p>
                </div>
            </div>
        </template>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <!-- Header -->
                <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-indigo-600">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white">Fan ma'lumotlarini tahrirlash</h3>
                            <p class="text-sm text-blue-100">O'zgarishlar kiriting</p>
                        </div>
                    </div>
                </div>

                <!-- Form (Create.vue dagi formaning to'liq nusxasi, faqat submit metodi o'zgaradi) -->
                <form @submit.prevent="submit" class="p-6 space-y-8">

                    <!-- 1. Asosiy Ma'lumotlar -->
                    <div>
                        <div class="flex items-center mb-4">
                            <div class="w-1 h-6 bg-indigo-500 rounded-full mr-3"></div>
                            <h3 class="text-lg font-bold text-gray-900">Asosiy Ma'lumotlar</h3>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Fan nomi <span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.name" type="text"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm"
                                    :class="{ 'border-red-500': form.errors.name }" required />
                                <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Fan kodi <span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.code" type="text"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm uppercase"
                                    :class="{ 'border-red-500': form.errors.code }" required />
                                <p v-if="form.errors.code" class="mt-1 text-xs text-red-600">{{ form.errors.code }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Kafedra <span class="text-red-500">*</span>
                                </label>
                                <select v-model="form.department_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm"
                                    required>
                                    <option :value="null">Tanlang</option>
                                    <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                                        {{ dept.name }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Yo'nalish</label>
                                <select v-model="form.direction_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                                    <option :value="null">Tanlang</option>
                                    <option v-for="dir in filteredDirections" :key="dir.id" :value="dir.id">
                                        {{ dir.name }} ({{ dir.code }})
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Kurs <span class="text-red-500">*</span>
                                </label>
                                <select v-model="form.course_level"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm"
                                    required>
                                    <option v-for="level in courseLevels" :key="level.value" :value="level.value">
                                        {{ level.label }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Kredit soat <span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.credit_hours" type="number" min="1" max="10"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm"
                                    required />
                            </div>
                        </div>
                    </div>

                    <!-- 2. Fan turlari -->
                    <div>
                        <div class="flex items-center mb-4">
                            <div class="w-1 h-6 bg-blue-500 rounded-full mr-3"></div>
                            <h3 class="text-lg font-bold text-gray-900">Fan Turlari</h3>
                        </div>
                        <div class="grid grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fan turi <span
                                        class="text-red-500">*</span></label>
                                <select v-model="form.subject_type"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm"
                                    required>
                                    <option v-for="type in subjectTypes" :key="type.value" :value="type.value">{{
                                        type.label }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ta'lim shakli</label>
                                <select v-model="form.education_form"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                                    <option :value="null">Tanlang</option>
                                    <option v-for="edu in educationForms" :key="edu.value" :value="edu.value">{{
                                        edu.label }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">1-sem nazorat</label>
                                <select v-model="form.semester_1_control"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                                    <option :value="null">Tanlang</option>
                                    <option v-for="ctrl in controlTypes" :key="ctrl.value" :value="ctrl.value">{{
                                        ctrl.label }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">2-sem nazorat</label>
                                <select v-model="form.semester_2_control"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                                    <option :value="null">Tanlang</option>
                                    <option v-for="ctrl in controlTypes" :key="ctrl.value" :value="ctrl.value">{{
                                        ctrl.label }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- 3. 1-Semestr -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="w-1 h-6 bg-green-500 rounded-full mr-3"></div>
                                <h3 class="text-lg font-bold text-gray-900">1-Semestr Soatlari</h3>
                            </div>
                            <div class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-semibold">
                                Jami: {{ semester1Total }}
                            </div>
                        </div>
                        <div class="grid grid-cols-7 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Ma'ruza</label><input
                                    v-model="form.semester_1_lecture" type="number" min="0" step="0.5"
                                    class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-green-500" />
                            </div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Seminar</label><input
                                    v-model="form.semester_1_seminar" type="number" min="0" step="0.5"
                                    class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-green-500" />
                            </div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Amaliy</label><input
                                    v-model="form.semester_1_practical" type="number" min="0" step="0.5"
                                    class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-green-500" />
                            </div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Lab</label><input
                                    v-model="form.semester_1_laboratory" type="number" min="0" step="0.5"
                                    class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-green-500" />
                            </div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Amaliyot</label><input
                                    v-model="form.semester_1_practice" type="number" min="0" step="0.5"
                                    class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-green-500" />
                            </div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Imtihon</label><input
                                    v-model="form.semester_1_exam" type="number" min="0" step="0.5"
                                    class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-green-500" />
                            </div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Sinov</label><input
                                    v-model="form.semester_1_test" type="number" min="0" step="0.5"
                                    class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-green-500" />
                            </div>
                        </div>
                    </div>

                    <!-- 4. 2-Semestr -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="w-1 h-6 bg-purple-500 rounded-full mr-3"></div>
                                <h3 class="text-lg font-bold text-gray-900">2-Semestr Soatlari</h3>
                            </div>
                            <div class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm font-semibold">
                                Jami: {{ semester2Total }}
                            </div>
                        </div>
                        <div class="grid grid-cols-7 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Ma'ruza</label><input
                                    v-model="form.semester_2_lecture" type="number" min="0" step="0.5"
                                    class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-purple-500" />
                            </div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Seminar</label><input
                                    v-model="form.semester_2_seminar" type="number" min="0" step="0.5"
                                    class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-purple-500" />
                            </div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Amaliy</label><input
                                    v-model="form.semester_2_practical" type="number" min="0" step="0.5"
                                    class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-purple-500" />
                            </div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Lab</label><input
                                    v-model="form.semester_2_laboratory" type="number" min="0" step="0.5"
                                    class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-purple-500" />
                            </div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Amaliyot</label><input
                                    v-model="form.semester_2_practice" type="number" min="0" step="0.5"
                                    class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-purple-500" />
                            </div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Imtihon</label><input
                                    v-model="form.semester_2_exam" type="number" min="0" step="0.5"
                                    class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-purple-500" />
                            </div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Sinov</label><input
                                    v-model="form.semester_2_test" type="number" min="0" step="0.5"
                                    class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-purple-500" />
                            </div>
                        </div>
                    </div>

                    <!-- 5. Qo'shimcha -->
                    <div>
                        <div class="flex items-center mb-4">
                            <div class="w-1 h-6 bg-orange-500 rounded-full mr-3"></div>
                            <h3 class="text-lg font-bold text-gray-900">Qo'shimcha Soatlar</h3>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Kurs ishi</label><input
                                    v-model="form.coursework_hours" type="number" min="0" step="0.5"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 text-sm" />
                            </div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Diplom ishi</label><input
                                    v-model="form.diploma_hours" type="number" min="0" step="0.5"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 text-sm" />
                            </div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Konsultatsiya</label><input
                                    v-model="form.consultation_hours" type="number" min="0" step="0.5"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 text-sm" />
                            </div>
                        </div>
                    </div>

                    <!-- 6. Tavsif va Sozlamalar -->
                    <div>
                        <div class="flex items-center mb-4">
                            <div class="w-1 h-6 bg-gray-500 rounded-full mr-3"></div>
                            <h3 class="text-lg font-bold text-gray-900">Qo'shimcha</h3>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tavsif</label>
                                <textarea v-model="form.description" rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm"
                                    maxlength="1000"></textarea>
                                <p class="mt-1 text-xs text-gray-500">{{ form.description?.length || 0 }}/1000</p>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <label
                                    class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input v-model="form.is_active" type="checkbox"
                                        class="w-4 h-4 text-indigo-600 rounded" />
                                    <span class="text-sm font-medium text-gray-700">Fan faol</span>
                                </label>
                                <label
                                    class="flex items-center space-x-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input v-model="form.can_be_potok" type="checkbox"
                                        class="w-4 h-4 text-indigo-600 rounded" />
                                    <span class="text-sm font-medium text-gray-700">Potok mumkin</span>
                                </label>
                                <div v-if="form.can_be_potok">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Min guruhlar</label>
                                    <input v-model="form.min_groups_for_potok" type="number" min="2"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Jami -->
                    <div class="p-4 bg-gradient-to-r from-indigo-50 to-blue-50 border-2 border-indigo-200 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Umumiy jami soat</p>
                                <p class="text-3xl font-bold text-indigo-600">{{ totalHours }}</p>
                            </div>
                            <div class="text-right space-y-1">
                                <p class="text-sm text-gray-600">1-semestr: <span class="font-bold text-green-600">{{
                                        semester1Total }}</span></p>
                                <p class="text-sm text-gray-600">2-semestr: <span class="font-bold text-purple-600">{{
                                        semester2Total }}</span></p>
                                <p class="text-sm text-gray-600">Qo'shimcha: <span class="font-bold text-orange-600">{{
                                        additionalHours }}</span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Tugmalar -->
                    <div class="flex items-center justify-between pt-6 border-t-2 border-gray-200">
                        <Link :href="`/subjects/${subject.id}`"
                            class="px-6 py-2.5 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition flex items-center space-x-2 font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>Bekor qilish</span>
                        </Link>
                        <button type="submit" :disabled="form.processing"
                            class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition disabled:opacity-50 flex items-center space-x-2 font-medium shadow-lg">
                            <svg v-if="form.processing" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <span>{{ form.processing ? 'Saqlanmoqda...' : 'O\'zgarishlarni saqlash' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    subject: Object,
    departments: Array,
    directions: Array,
    courseLevels: Array,
    subjectTypes: Array,
    educationForms: Array,
    controlTypes: Array,
});

const form = useForm({
    name: props.subject.name,
    code: props.subject.code,
    department_id: props.subject.department_id,
    direction_id: props.subject.direction_id,
    course_level: props.subject.course_level,
    subject_type: props.subject.subject_type,
    education_form: props.subject.education_form,
    credit_hours: props.subject.credit_hours,
    semester_1_lecture: props.subject.semester_1_lecture,
    semester_1_practical: props.subject.semester_1_practical,
    semester_1_laboratory: props.subject.semester_1_laboratory,
    semester_1_seminar: props.subject.semester_1_seminar,
    semester_1_practice: props.subject.semester_1_practice,
    semester_1_exam: props.subject.semester_1_exam,
    semester_1_test: props.subject.semester_1_test,
    semester_1_control: props.subject.semester_1_control,
    semester_2_lecture: props.subject.semester_2_lecture,
    semester_2_practical: props.subject.semester_2_practical,
    semester_2_laboratory: props.subject.semester_2_laboratory,
    semester_2_seminar: props.subject.semester_2_seminar,
    semester_2_practice: props.subject.semester_2_practice,
    semester_2_exam: props.subject.semester_2_exam,
    semester_2_test: props.subject.semester_2_test,
    semester_2_control: props.subject.semester_2_control,
    coursework_hours: props.subject.coursework_hours,
    diploma_hours: props.subject.diploma_hours,
    consultation_hours: props.subject.consultation_hours,
    description: props.subject.description,
    is_active: props.subject.is_active,
    can_be_potok: props.subject.can_be_potok,
    min_groups_for_potok: props.subject.min_groups_for_potok,
});

const filteredDirections = computed(() => {
    if (!form.department_id) return props.directions;
    return props.directions.filter(d => d.department_id === form.department_id);
});

const semester1Total = computed(() => {
    return (
        parseFloat(form.semester_1_lecture || 0) +
        parseFloat(form.semester_1_practical || 0) +
        parseFloat(form.semester_1_laboratory || 0) +
        parseFloat(form.semester_1_seminar || 0) +
        parseFloat(form.semester_1_practice || 0) +
        parseFloat(form.semester_1_exam || 0) +
        parseFloat(form.semester_1_test || 0)
    ).toFixed(1);
});

const semester2Total = computed(() => {
    return (
        parseFloat(form.semester_2_lecture || 0) +
        parseFloat(form.semester_2_practical || 0) +
        parseFloat(form.semester_2_laboratory || 0) +
        parseFloat(form.semester_2_seminar || 0) +
        parseFloat(form.semester_2_practice || 0) +
        parseFloat(form.semester_2_exam || 0) +
        parseFloat(form.semester_2_test || 0)
    ).toFixed(1);
});

const additionalHours = computed(() => {
    return (
        parseFloat(form.coursework_hours || 0) +
        parseFloat(form.diploma_hours || 0) +
        parseFloat(form.consultation_hours || 0)
    ).toFixed(1);
});

const totalHours = computed(() => {
    return (
        parseFloat(semester1Total.value) +
        parseFloat(semester2Total.value) +
        parseFloat(additionalHours.value)
    ).toFixed(1);
});

const submit = () => {
    form.put(`/subjects/${props.subject.id}`, {
        preserveScroll: true,
    });
};
</script>