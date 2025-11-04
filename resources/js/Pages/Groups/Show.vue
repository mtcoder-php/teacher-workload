<template>
    <Head :title="group.name" />

    <AuthenticatedLayout>
        <template #header>{{ group.name }}</template>

        <div class="space-y-6">
            <!-- Action Buttons -->
            <div class="flex items-center justify-between">
                <Link
                    href="/groups"
                    class="flex items-center space-x-2 text-gray-600 hover:text-gray-900"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span>Orqaga</span>
                </Link>

                <div class="flex items-center space-x-3">
                    <Link
                        :href="`/groups/${group.id}/edit`"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center space-x-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>Tahrirlash</span>
                    </Link>
                </div>
            </div>

            <!-- Main Info Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-purple-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">{{ group.name }}</h2>
                            <p class="text-sm text-gray-600 mt-1">{{ group.code }}</p>
                        </div>
                        <span
                            :class="group.is_active 
                                ? 'bg-green-100 text-green-800' 
                                : 'bg-red-100 text-red-800'"
                            class="px-3 py-1 text-sm font-semibold rounded-full"
                        >
                            {{ group.is_active ? 'Faol' : 'Nofaol' }}
                        </span>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Yo'nalish -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-blue-100 rounded-lg">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Yo'nalish</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ group.direction?.name || '—' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Kurs -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-orange-100 rounded-lg">
                                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Kurs</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ getCourseLabel(group.course) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Ta'lim shakli -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-indigo-100 rounded-lg">
                                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Ta'lim shakli</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ getEducationTypeLabel(group.education_type) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- ✅ YANGI - Ta'lim tili -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-amber-100 rounded-lg">
                                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Ta'lim tili</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ getEducationLanguageLabel(group.education_language) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Talabalar soni -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-green-100 rounded-lg">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Talabalar soni</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ group.student_count || 0 }} ta</p>
                                </div>
                            </div>
                        </div>

                        <!-- Yaratilgan sana -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-purple-100 rounded-lg">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Yaratilgan sana</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ formatDate(group.created_at) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Workloads List -->
            <div v-if="group.workloads && group.workloads.length > 0" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Yuklamalar ro'yxati</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">O'qituvchi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Semestr</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Soatlar</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="workload in group.workloads" :key="workload.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ workload.subject?.name || '—' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ workload.teacher?.user?.name || '—' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ workload.semester?.name || '—' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ workload.total_hours || 0 }} soat
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="!group.workloads || group.workloads.length === 0" 
                 class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-lg font-medium text-gray-900 mb-2">Yuklamalar mavjud emas</p>
                <p class="text-sm text-gray-600">Bu guruhda hali yuklamalar qo'shilmagan</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Link, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    group: Object,
});

const formatDate = (date) => {
    if (!date) return '—';
    const [year, month, day] = date.split('T')[0].split('-');
    return `${day}.${month}.${year}`;
};

const getCourseLabel = (course) => {
    const labels = {
        1: '1-kurs (Bakalavr)',
        2: '2-kurs (Bakalavr)',
        3: '3-kurs (Bakalavr)',
        4: '4-kurs (Bakalavr)',
        5: '1-kurs (Magistratura)',
        6: '2-kurs (Magistratura)',
    };
    return labels[course] || `${course}-kurs`;
};

const getEducationTypeLabel = (type) => {
    const labels = {
        'kunduzgi': 'Kunduzgi',
        'sirtqi': 'Sirtqi',
        'kechki': 'Kechki',
        'masofaviy': 'Masofaviy',
    };
    return labels[type] || type;
};

// ✅ YANGI FUNCTION
const getEducationLanguageLabel = (language) => {
    const labels = {
        'uzbek': 'O\'zbek tili',
        'russian': 'Rus tili',
    };
    return labels[language] || language || '—';
};
</script>