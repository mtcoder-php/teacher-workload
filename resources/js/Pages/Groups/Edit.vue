<template>
    <Head :title="`${group.name} - O'zgartirish`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Guruhni O'zgartirish</h2>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <!-- Form Header -->
                    <div class="px-8 py-6 border-b-2 border-gray-200 bg-gradient-to-r from-purple-50 to-indigo-50">
                        <h3 class="text-xl font-bold text-gray-900">Guruh Ma'lumotlari</h3>
                        <p class="text-sm text-gray-600 mt-2">Quyidagi ma'lumotlarni o'zgartiring va yangilang</p>
                    </div>

                    <!-- Form Body -->
                    <form @submit.prevent="submit" class="p-8 space-y-8">
                        <!-- STEP 1: ASOSIY MA'LUMOTLAR -->
                        <div class="pb-8 border-b-2 border-gray-200">
                            <h4 class="text-lg font-bold text-gray-900 mb-6">1. Asosiy Ma'lumotlar</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Guruh nomi -->
                                <div>
                                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Guruh nomi <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition"
                                        :class="{ 'border-red-500 focus:ring-red-500': form.errors.name }"
                                        placeholder="Masalan: 101-guruh arab tili"
                                    />
                                    <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                                </div>

                                <!-- Kod -->
                                <div>
                                    <label for="code" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Guruh kodi <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="code"
                                        v-model="form.code"
                                        type="text"
                                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition"
                                        :class="{ 'border-red-500 focus:ring-red-500': form.errors.code }"
                                        placeholder="Masalan: 101AK/25"
                                    />
                                    <p v-if="form.errors.code" class="mt-1 text-sm text-red-600">{{ form.errors.code }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- STEP 2: YO'NALISH VA KURS -->
                        <div class="pb-8 border-b-2 border-gray-200">
                            <h4 class="text-lg font-bold text-gray-900 mb-6">2. Ta'lim Yo'nalishi</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Yo'nalish -->
                                <div class="md:col-span-2">
                                    <label for="direction_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Yo'nalish <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        id="direction_id"
                                        v-model="form.direction_id"
                                        @change="updateCourseOptions"
                                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition"
                                        :class="{ 'border-red-500 focus:ring-red-500': form.errors.direction_id }"
                                    >
                                        <option :value="null">Yo'nalishni tanlang</option>
                                        <option v-for="direction in directions" :key="direction.id" :value="direction.id">
                                            {{ direction.name }} ({{ direction.degree_type }})
                                        </option>
                                    </select>
                                    <p v-if="form.errors.direction_id" class="mt-1 text-sm text-red-600">{{ form.errors.direction_id }}</p>
                                </div>

                                <!-- Kurs -->
                                <div>
                                    <label for="course" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Kurs <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        id="course"
                                        v-model.number="form.course"
                                        :disabled="!selectedDirection"
                                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed"
                                        :class="{ 'border-red-500 focus:ring-red-500': form.errors.course }"
                                    >
                                        <option :value="null">{{ selectedDirection ? 'Tanlang' : 'Yo\'nalish tanlang' }}</option>
                                        <option v-for="course in availableCourses" :key="course.value" :value="course.value">
                                            {{ course.label }}
                                        </option>
                                    </select>
                                    <p v-if="form.errors.course" class="mt-1 text-sm text-red-600">{{ form.errors.course }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- STEP 3: TA'LIM SHAKLI, TILI VA TALABALAR -->
                        <div class="pb-8 border-b-2 border-gray-200">
                            <h4 class="text-lg font-bold text-gray-900 mb-6">3. Ta'lim Shakli va Talabalar</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Ta'lim shakli -->
                                <div>
                                    <label for="education_type" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Ta'lim shakli <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        id="education_type"
                                        v-model="form.education_type"
                                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition"
                                        :class="{ 'border-red-500 focus:ring-red-500': form.errors.education_type }"
                                    >
                                        <option value="">Tanlang</option>
                                        <option value="kunduzgi">Kunduzgi</option>
                                        <option value="sirtqi">Sirtqi</option>
                                        <option value="kechki">Kechki</option>
                                        <option value="masofaviy">Masofaviy</option>
                                    </select>
                                    <p v-if="form.errors.education_type" class="mt-1 text-sm text-red-600">{{ form.errors.education_type }}</p>
                                </div>

                                <!-- ✅ YANGI - Ta'lim tili -->
                                <div>
                                    <label for="education_language" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Ta'lim tili <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        id="education_language"
                                        v-model="form.education_language"
                                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition"
                                        :class="{ 'border-red-500 focus:ring-red-500': form.errors.education_language }"
                                    >
                                        <option value="">Tanlang</option>
                                        <option value="uzbek">O'zbek tili</option>
                                        <option value="russian">Rus tili</option>
                                    </select>
                                    <p v-if="form.errors.education_language" class="mt-1 text-sm text-red-600">{{ form.errors.education_language }}</p>
                                </div>

                                <!-- Talabalar soni -->
                                <div>
                                    <label for="student_count" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Talabalar soni
                                    </label>
                                    <input
                                        id="student_count"
                                        v-model.number="form.student_count"
                                        type="number"
                                        min="0"
                                        max="100"
                                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition"
                                        :class="{ 'border-red-500 focus:ring-red-500': form.errors.student_count }"
                                        placeholder="40"
                                    />
                                    <p v-if="form.errors.student_count" class="mt-1 text-sm text-red-600">{{ form.errors.student_count }}</p>
                                </div>

                                <!-- Status -->
                                <div class="flex items-end">
                                    <label class="flex items-center cursor-pointer bg-gradient-to-r from-green-50 to-emerald-50 p-4 rounded-lg border-2 border-green-300 w-full hover:border-green-400 transition">
                                        <input
                                            v-model="form.is_active"
                                            type="checkbox"
                                            class="w-5 h-5 text-green-600 rounded focus:ring-2 focus:ring-green-500"
                                        />
                                        <span class="ml-3 text-sm font-semibold text-gray-900">Faol guruh</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- TAHRIRLASH TARIXI -->
                        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <p class="text-xs text-gray-600">
                                <span class="font-semibold">Yaratilgan:</span> {{ formatDate(group.created_at) }}
                            </p>
                            <p class="text-xs text-gray-600 mt-1">
                                <span class="font-semibold">O'zgartirilgan:</span> {{ formatDate(group.updated_at) }}
                            </p>
                        </div>

                        <!-- XULOSA -->
                        <div class="p-6 bg-gradient-to-r from-purple-50 to-indigo-50 rounded-xl border-2 border-purple-200">
                            <h5 class="font-bold text-gray-900 mb-4">O'zgartirilgan Guruh Ma'lumotlari</h5>
                            
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-purple-100">
                                    <span class="text-sm text-gray-600">Guruh nomi:</span>
                                    <span class="text-sm font-semibold text-gray-900">{{ form.name || '—' }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-purple-100">
                                    <span class="text-sm text-gray-600">Guruh kodi:</span>
                                    <span class="text-sm font-semibold text-gray-900">{{ form.code || '—' }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-purple-100">
                                    <span class="text-sm text-gray-600">Yo'nalish:</span>
                                    <span class="text-sm font-semibold text-gray-900">{{ selectedDirection?.name || '—' }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-purple-100">
                                    <span class="text-sm text-gray-600">Kurs:</span>
                                    <span class="text-sm font-semibold text-gray-900">{{ form.course || '—' }}-kurs</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-purple-100">
                                    <span class="text-sm text-gray-600">Ta'lim shakli:</span>
                                    <span class="text-sm font-semibold text-gray-900">{{ educationTypeLabel || '—' }}</span>
                                </div>
                                <!-- ✅ YANGI - Ta'lim tili ko'rsatish -->
                                <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-purple-100">
                                    <span class="text-sm text-gray-600">Ta'lim tili:</span>
                                    <span class="text-sm font-semibold text-gray-900">{{ educationLanguageLabel || '—' }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-purple-100">
                                    <span class="text-sm text-gray-600">Talabalar soni:</span>
                                    <span class="text-sm font-semibold text-gray-900">{{ form.student_count || 0 }} ta</span>
                                </div>
                            </div>
                        </div>

                        <!-- BUTTONS -->
                        <div class="flex items-center justify-between pt-6 border-t-2 border-gray-200">
                            <div class="flex gap-3">
                                <Link
                                    href="/groups"
                                    class="px-6 py-3 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium"
                                >
                                    Orqaga
                                </Link>
                                <button
                                    type="button"
                                    @click="showDeleteModal = true"
                                    class="px-6 py-3 border-2 border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition font-medium"
                                >
                                    O'chirish
                                </button>
                            </div>

                            <button
                                type="submit"
                                :disabled="form.processing || !canSubmit"
                                class="px-8 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg hover:from-purple-700 hover:to-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 font-medium shadow-lg"
                            >
                                <svg v-if="form.processing" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>{{ form.processing ? 'Saqlanmoqda...' : 'O\'zgartirishlarni Saqlash' }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- DELETE MODAL -->
        <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-2xl max-w-sm w-full p-6 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Guruhni o'chirish?</h3>
                </div>

                <p class="text-gray-600">
                    <strong>{{ group.name }}</strong> guruhini o'chirishni xohlaysizmi? Bu amalni qaytarib bo'lmaydi.
                </p>

                <div class="flex gap-3 pt-4">
                    <button
                        @click="showDeleteModal = false"
                        class="flex-1 px-4 py-2.5 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition"
                    >
                        Bekor qilish
                    </button>
                    <button
                        @click="confirmDelete"
                        :disabled="deleteForm.processing"
                        class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {{ deleteForm.processing ? 'O\'chirilmoqda...' : 'O\'chirish' }}
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm, Link, Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    group: Object,
    directions: Array,
});

const showDeleteModal = ref(false);

const form = useForm({
    name: props.group.name,
    code: props.group.code,
    direction_id: props.group.direction_id,
    course: props.group.course,
    education_type: props.group.education_type,
    education_language: props.group.education_language || 'uzbek', // ✅ YANGI
    student_count: props.group.student_count,
    is_active: props.group.is_active,
});

const deleteForm = useForm({});

const selectedDirection = computed(() => {
    if (!form.direction_id) return null;
    return props.directions?.find(d => d.id === form.direction_id);
});

const availableCourses = computed(() => {
    if (!selectedDirection.value) return [];
    
    const degreeType = selectedDirection.value.degree_type;
    
    if (degreeType === 'bakalavr') {
        return [
            { value: 1, label: '1-kurs (Bakalavr)' },
            { value: 2, label: '2-kurs (Bakalavr)' },
            { value: 3, label: '3-kurs (Bakalavr)' },
            { value: 4, label: '4-kurs (Bakalavr)' },
        ];
    } else if (degreeType === 'magistratura') {
        return [
            { value: 5, label: '1-kurs (Magistratura)' },
            { value: 6, label: '2-kurs (Magistratura)' },
        ];
    }
    
    return [];
});

const educationTypeLabel = computed(() => {
    const labels = {
        'kunduzgi': 'Kunduzgi',
        'sirtqi': 'Sirtqi',
        'kechki': 'Kechki',
        'masofaviy': 'Masofaviy',
    };
    return labels[form.education_type] || '';
});

// ✅ YANGI COMPUTED
const educationLanguageLabel = computed(() => {
    const labels = {
        'uzbek': 'O\'zbek tili',
        'russian': 'Rus tili',
    };
    return labels[form.education_language] || '';
});

const canSubmit = computed(() => {
    return form.name && form.code && form.direction_id && form.course && form.education_type && form.education_language; // ✅ YANGI shart
});

const formatDate = (date) => {
    if (!date) return '—';
    const d = new Date(date);
    return d.toLocaleDateString('uz-UZ', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' });
};

const updateCourseOptions = () => {
    form.course = null;
};

const submit = () => {
    form.put(`/groups/${props.group.id}`, {
        preserveScroll: true,
    });
};

const confirmDelete = () => {
    deleteForm.delete(`/groups/${props.group.id}`, {
        preserveScroll: true,
    });
};
</script>