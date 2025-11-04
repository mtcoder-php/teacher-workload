<template>
    <Head title="Fanlar" />
    
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-indigo-100 rounded-lg">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Fanlar</h2>
                    <p class="text-sm text-gray-600">Barcha fanlar ro'yxati</p>
                </div>
            </div>
        </template>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Yangi Fan tugmasi -->
            <div class="mb-6 flex justify-end">
                <Link href="/subjects/create"
                    class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-blue-600 text-white rounded-lg hover:from-indigo-700 hover:to-blue-700 transition flex items-center space-x-2 shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Yangi Fan</span>
                </Link>
            </div>
            <!-- Filters -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
                <div class="grid grid-cols-5 gap-4">
                    <!-- Search -->
                    <div class="col-span-2">
                        <input
                            v-model="filters.search"
                            type="text"
                            placeholder="Fan nomi yoki kodi bo'yicha qidirish..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm"
                        />
                    </div>

                    <!-- Department -->
                    <div>
                        <select
                            v-model="filters.department_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm"
                        >
                            <option :value="null">Barcha kafedra</option>
                            <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                                {{ dept.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Course Level -->
                    <div>
                        <select
                            v-model="filters.course_level"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm"
                        >
                            <option :value="null">Barcha kurs</option>
                            <option v-for="level in courseLevels" :key="level.value" :value="level.value">
                                {{ level.label }}
                            </option>
                        </select>
                    </div>

                    <!-- Subject Type -->
                    <div>
                        <select
                            v-model="filters.subject_type"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm"
                        >
                            <option :value="null">Barcha tur</option>
                            <option v-for="type in subjectTypes" :key="type.value" :value="type.value">
                                {{ type.label }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Filter Actions -->
                <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-200">
                    <div class="text-sm text-gray-600">
                        Jami: <span class="font-semibold text-gray-900">{{ subjects.total }}</span> ta fan
                    </div>
                    <button
                        v-if="hasActiveFilters"
                        @click="clearFilters"
                        class="text-sm text-indigo-600 hover:text-indigo-700 font-medium"
                    >
                        Filtrlarni tozalash
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Fan</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kafedra</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Kurs</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Kredit</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Soatlar</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Tur</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Holat</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Amallar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="subject in subjects.data" :key="subject.id" class="hover:bg-gray-50 transition">
                                <!-- Fan -->
                                <td class="px-4 py-3">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-indigo-500 to-blue-500 rounded-lg flex items-center justify-center">
                                            <span class="text-white font-bold text-sm">{{ subject.code.substring(0, 2) }}</span>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ subject.name }}</div>
                                            <div class="text-sm text-gray-500">{{ subject.code }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Kafedra -->
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-900">{{ subject.department?.name }}</div>
                                    <div class="text-xs text-gray-500">{{ subject.department?.faculty?.name }}</div>
                                </td>

                                <!-- Kurs -->
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">
                                        {{ subject.course_level }}-kurs
                                    </span>
                                </td>

                                <!-- Kredit -->
                                <td class="px-4 py-3 text-center">
                                    <span class="font-semibold text-gray-900">{{ subject.credit_hours }}</span>
                                </td>

                                <!-- Soatlar -->
                                <td class="px-4 py-3 text-center">
                                    <div class="text-sm font-semibold text-gray-900">{{ subject.total_hours }}</div>
                                    <div class="text-xs text-gray-500">
                                        1-sem: {{ calculateSemesterTotal(subject, 1) }} | 
                                        2-sem: {{ calculateSemesterTotal(subject, 2) }}
                                    </div>
                                </td>

                                <!-- Tur -->
                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                        :class="{
                                            'bg-green-100 text-green-700': subject.subject_type === 'asosiy',
                                            'bg-blue-100 text-blue-700': subject.subject_type === 'yordamchi',
                                            'bg-purple-100 text-purple-700': subject.subject_type === 'ixtiyoriy'
                                        }"
                                    >
                                        {{ getSubjectTypeLabel(subject.subject_type) }}
                                    </span>
                                </td>

                                <!-- Holat -->
                                <td class="px-4 py-3 text-center">
                                    <button
                                        @click="toggleActive(subject)"
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium transition"
                                        :class="subject.is_active 
                                            ? 'bg-green-100 text-green-700 hover:bg-green-200' 
                                            : 'bg-red-100 text-red-700 hover:bg-red-200'"
                                    >
                                        <span class="w-2 h-2 rounded-full mr-1.5" :class="subject.is_active ? 'bg-green-500' : 'bg-red-500'"></span>
                                        {{ subject.is_active ? 'Faol' : 'Nofaol' }}
                                    </button>
                                </td>

                                <!-- Amallar -->
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <Link :href="`/subjects/${subject.id}`"
                                            class="p-2 text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition"
                                            title="Ko'rish">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </Link>
                                        <Link :href="`/subjects/${subject.id}/edit`"
                                            class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                            title="Tahrirlash">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </Link>
                                        <button
                                            @click="confirmDelete(subject)"
                                            class="p-2 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                            title="O'chirish">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Empty State -->
                            <tr v-if="subjects.data.length === 0">
                                <td colspan="8" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-3">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-gray-900 font-medium">Fanlar topilmadi</p>
                                            <p class="text-sm text-gray-500 mt-1">Yangi fan qo'shish uchun yuqoridagi tugmani bosing</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="subjects.data.length > 0" class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            Ko'rsatilmoqda <span class="font-semibold">{{ subjects.from }}</span> dan 
                            <span class="font-semibold">{{ subjects.to }}</span> gacha, 
                            jami <span class="font-semibold">{{ subjects.total }}</span> ta
                        </div>
                        <div class="flex items-center space-x-2">
                            <template v-for="(link, index) in subjects.links" :key="index">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    v-html="link.label"
                                    :class="[
                                        'px-3 py-1 text-sm rounded-lg transition',
                                        link.active 
                                            ? 'bg-indigo-600 text-white font-semibold' 
                                            : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-300'
                                    ]"
                                />
                                <span
                                    v-else
                                    v-html="link.label"
                                    class="px-3 py-1 text-sm rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed"
                                />
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    subjects: Object,
    departments: Array,
    directions: Array,
    filters: Object,
    courseLevels: Array,
    subjectTypes: Array,
});

const filters = ref({
    search: props.filters.search || '',
    department_id: props.filters.department_id || null,
    course_level: props.filters.course_level || null,
    subject_type: props.filters.subject_type || null,
});

const hasActiveFilters = computed(() => {
    return filters.value.search || 
           filters.value.department_id || 
           filters.value.course_level ||
           filters.value.subject_type;
});

// Watch filters and update URL
watch(filters, (newFilters) => {
    router.get('/subjects', newFilters, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}, { deep: true });

const clearFilters = () => {
    filters.value = {
        search: '',
        department_id: null,
        course_level: null,
        subject_type: null,
    };
};

const calculateSemesterTotal = (subject, semester) => {
    const prefix = `semester_${semester}_`;
    return (
        parseFloat(subject[`${prefix}lecture`] || 0) +
        parseFloat(subject[`${prefix}practical`] || 0) +
        parseFloat(subject[`${prefix}laboratory`] || 0) +
        parseFloat(subject[`${prefix}seminar`] || 0) +
        parseFloat(subject[`${prefix}practice`] || 0) +
        parseFloat(subject[`${prefix}exam`] || 0) +
        parseFloat(subject[`${prefix}test`] || 0)
    ).toFixed(1);
};

const getSubjectTypeLabel = (type) => {
    const types = {
        'asosiy': 'Asosiy',
        'yordamchi': 'Yordamchi',
        'ixtiyoriy': 'Ixtiyoriy'
    };
    return types[type] || type;
};

const toggleActive = (subject) => {
    if (confirm(`${subject.name} fanini ${subject.is_active ? 'faolsizlantirish' : 'faollashtirish'}ni xohlaysizmi?`)) {
        router.patch(`/subjects/${subject.id}/toggle-active`, {}, {
            preserveScroll: true,
        });
    }
};

const confirmDelete = (subject) => {
    if (confirm(`${subject.name} fanini o'chirishni xohlaysizmi? Bu amalni qaytarib bo'lmaydi!`)) {
        router.delete(`/subjects/${subject.id}`, {
            preserveScroll: true,
        });
    }
};
</script>