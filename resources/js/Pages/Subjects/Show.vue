<template>
    <Head :title="subject.name" />
    
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-indigo-500 to-blue-500 rounded-xl flex items-center justify-center shadow-lg">
                    <span class="text-white font-bold text-lg">{{ subject.code.substring(0, 2) }}</span>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ subject.name }}</h2>
                    <p class="text-sm text-gray-600">{{ subject.code }}</p>
                </div>
            </div>
        </template>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <!-- Orqaga va Tahrirlash tugmalari -->
            <div class="flex items-center justify-between">
                <Link href="/subjects" class="flex items-center space-x-2 text-gray-600 hover:text-gray-900 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span class="font-medium">Orqaga qaytish</span>
                </Link>
                <Link :href="`/subjects/${subject.id}/edit`"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center space-x-2 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span>Tahrirlash</span>
                </Link>
            </div>
            
            <!-- Stats Cards -->
            <div class="grid grid-cols-4 gap-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Kredit soat</p>
                            <p class="text-2xl font-bold text-indigo-600">{{ subject.credit_hours }}</p>
                        </div>
                        <div class="p-3 bg-indigo-100 rounded-lg">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Jami soat</p>
                            <p class="text-2xl font-bold text-blue-600">{{ stats.total_hours }}</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">1-semestr</p>
                            <p class="text-2xl font-bold text-green-600">{{ stats.semester_1.total_hours }}</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">2-semestr</p>
                            <p class="text-2xl font-bold text-purple-600">{{ stats.semester_2.total_hours }}</p>
                        </div>
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-6">
                <!-- Left Column -->
                <div class="col-span-2 space-y-6">
                    
                    <!-- Asosiy Ma'lumotlar -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-blue-50 border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900 flex items-center">
                                <span class="w-1 h-6 bg-indigo-500 rounded-full mr-3"></span>
                                Asosiy Ma'lumotlar
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Kafedra</p>
                                    <p class="text-base font-semibold text-gray-900">{{ subject.department?.name }}</p>
                                    <p class="text-xs text-gray-500">{{ subject.department?.faculty?.name }}</p>
                                </div>
                                <div v-if="subject.direction">
                                    <p class="text-sm text-gray-600">Yo'nalish</p>
                                    <p class="text-base font-semibold text-gray-900">{{ subject.direction?.name }}</p>
                                    <p class="text-xs text-gray-500">{{ subject.direction?.code }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Kurs</p>
                                    <p class="text-base font-semibold text-gray-900">{{ subject.course_level }}-kurs</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Fan turi</p>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                        :class="{
                                            'bg-green-100 text-green-700': subject.subject_type === 'asosiy',
                                            'bg-blue-100 text-blue-700': subject.subject_type === 'yordamchi',
                                            'bg-purple-100 text-purple-700': subject.subject_type === 'ixtiyoriy'
                                        }">
                                        {{ getSubjectTypeLabel(subject.subject_type) }}
                                    </span>
                                </div>
                                <div v-if="subject.education_form">
                                    <p class="text-sm text-gray-600">Ta'lim shakli</p>
                                    <p class="text-base font-semibold text-gray-900">{{ getEducationFormLabel(subject.education_form) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Holat</p>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                        :class="subject.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                                        <span class="w-2 h-2 rounded-full mr-2" :class="subject.is_active ? 'bg-green-500' : 'bg-red-500'"></span>
                                        {{ subject.is_active ? 'Faol' : 'Nofaol' }}
                                    </span>
                                </div>
                                <div v-if="subject.can_be_potok">
                                    <p class="text-sm text-gray-600">Patok</p>
                                    <p class="text-base font-semibold text-gray-900">Min {{ subject.min_groups_for_potok }} guruh</p>
                                </div>
                            </div>
                            <div v-if="subject.description" class="mt-4 pt-4 border-t border-gray-200">
                                <p class="text-sm text-gray-600 mb-1">Tavsif</p>
                                <p class="text-sm text-gray-700">{{ subject.description }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- 1-Semestr Soatlari -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900 flex items-center justify-between">
                                <div class="flex items-center">
                                    <span class="w-1 h-6 bg-green-500 rounded-full mr-3"></span>
                                    1-Semestr Soatlari
                                </div>
                                <span v-if="subject.semester_1_control" class="text-sm font-medium px-3 py-1 bg-white rounded-full text-green-700">
                                    {{ getControlTypeLabel(subject.semester_1_control) }}
                                </span>
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-4 gap-4">
                                <div class="text-center p-3 bg-green-50 rounded-lg">
                                    <p class="text-xs text-gray-600 mb-1">Ma'ruza</p>
                                    <p class="text-xl font-bold text-green-700">{{ stats.semester_1.lecture }}</p>
                                </div>
                                <div class="text-center p-3 bg-green-50 rounded-lg">
                                    <p class="text-xs text-gray-600 mb-1">Amaliy</p>
                                    <p class="text-xl font-bold text-green-700">{{ stats.semester_1.practical }}</p>
                                </div>
                                <div class="text-center p-3 bg-green-50 rounded-lg">
                                    <p class="text-xs text-gray-600 mb-1">Laboratoriya</p>
                                    <p class="text-xl font-bold text-green-700">{{ stats.semester_1.laboratory }}</p>
                                </div>
                                <div class="text-center p-3 bg-green-50 rounded-lg">
                                    <p class="text-xs text-gray-600 mb-1">Seminar</p>
                                    <p class="text-xl font-bold text-green-700">{{ stats.semester_1.seminar }}</p>
                                </div>
                                <div class="text-center p-3 bg-green-50 rounded-lg">
                                    <p class="text-xs text-gray-600 mb-1">Amaliyot</p>
                                    <p class="text-xl font-bold text-green-700">{{ stats.semester_1.practice }}</p>
                                </div>
                                <div class="text-center p-3 bg-green-50 rounded-lg">
                                    <p class="text-xs text-gray-600 mb-1">Imtihon</p>
                                    <p class="text-xl font-bold text-green-700">{{ stats.semester_1.exam }}</p>
                                </div>
                                <div class="text-center p-3 bg-green-50 rounded-lg">
                                    <p class="text-xs text-gray-600 mb-1">Sinov</p>
                                    <p class="text-xl font-bold text-green-700">{{ stats.semester_1.test }}</p>
                                </div>
                                <div class="text-center p-3 bg-green-100 rounded-lg border-2 border-green-300">
                                    <p class="text-xs text-gray-600 mb-1 font-semibold">Jami</p>
                                    <p class="text-xl font-bold text-green-800">{{ stats.semester_1.total_hours }}</p>
                                </div>
                            </div>
                            <div class="mt-4 p-3 bg-green-50 rounded-lg">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Auditoriya soati:</span>
                                    <span class="font-semibold text-green-700">{{ stats.semester_1.auditory_hours }} soat</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2-Semestr Soatlari -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-violet-50 border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900 flex items-center justify-between">
                                <div class="flex items-center">
                                    <span class="w-1 h-6 bg-purple-500 rounded-full mr-3"></span>
                                    2-Semestr Soatlari
                                </div>
                                <span v-if="subject.semester_2_control" class="text-sm font-medium px-3 py-1 bg-white rounded-full text-purple-700">
                                    {{ getControlTypeLabel(subject.semester_2_control) }}
                                </span>
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-4 gap-4">
                                <div class="text-center p-3 bg-purple-50 rounded-lg">
                                    <p class="text-xs text-gray-600 mb-1">Ma'ruza</p>
                                    <p class="text-xl font-bold text-purple-700">{{ stats.semester_2.lecture }}</p>
                                </div>
                                <div class="text-center p-3 bg-purple-50 rounded-lg">
                                    <p class="text-xs text-gray-600 mb-1">Amaliy</p>
                                    <p class="text-xl font-bold text-purple-700">{{ stats.semester_2.practical }}</p>
                                </div>
                                <div class="text-center p-3 bg-purple-50 rounded-lg">
                                    <p class="text-xs text-gray-600 mb-1">Laboratoriya</p>
                                    <p class="text-xl font-bold text-purple-700">{{ stats.semester_2.laboratory }}</p>
                                </div>
                                <div class="text-center p-3 bg-purple-50 rounded-lg">
                                    <p class="text-xs text-gray-600 mb-1">Seminar</p>
                                    <p class="text-xl font-bold text-purple-700">{{ stats.semester_2.seminar }}</p>
                                </div>
                                <div class="text-center p-3 bg-purple-50 rounded-lg">
                                    <p class="text-xs text-gray-600 mb-1">Amaliyot</p>
                                    <p class="text-xl font-bold text-purple-700">{{ stats.semester_2.practice }}</p>
                                </div>
                                <div class="text-center p-3 bg-purple-50 rounded-lg">
                                    <p class="text-xs text-gray-600 mb-1">Imtihon</p>
                                    <p class="text-xl font-bold text-purple-700">{{ stats.semester_2.exam }}</p>
                                </div>
                                <div class="text-center p-3 bg-purple-50 rounded-lg">
                                    <p class="text-xs text-gray-600 mb-1">Sinov</p>
                                    <p class="text-xl font-bold text-purple-700">{{ stats.semester_2.test }}</p>
                                </div>
                                <div class="text-center p-3 bg-purple-100 rounded-lg border-2 border-purple-300">
                                    <p class="text-xs text-gray-600 mb-1 font-semibold">Jami</p>
                                    <p class="text-xl font-bold text-purple-800">{{ stats.semester_2.total_hours }}</p>
                                </div>
                            </div>
                            <div class="mt-4 p-3 bg-purple-50 rounded-lg">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Auditoriya soati:</span>
                                    <span class="font-semibold text-purple-700">{{ stats.semester_2.auditory_hours }} soat</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    
                    <!-- Qo'shimcha Soatlar -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-amber-50 border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900 flex items-center">
                                <span class="w-1 h-6 bg-orange-500 rounded-full mr-3"></span>
                                Qo'shimcha Soatlar
                            </h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                                <span class="text-sm text-gray-700">Kurs ishi</span>
                                <span class="text-lg font-bold text-orange-700">{{ stats.additional.coursework }}</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                                <span class="text-sm text-gray-700">Diplom ishi</span>
                                <span class="text-lg font-bold text-orange-700">{{ stats.additional.diploma }}</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                                <span class="text-sm text-gray-700">Konsultatsiya</span>
                                <span class="text-lg font-bold text-orange-700">{{ stats.additional.consultation }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Jami Statistika -->
                    <div class="bg-gradient-to-br from-indigo-500 to-blue-600 rounded-xl shadow-lg overflow-hidden text-white">
                        <div class="p-6">
                            <h3 class="text-lg font-bold mb-4 flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                Jami Statistika
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center p-3 bg-white/10 backdrop-blur rounded-lg">
                                    <span class="text-sm">Umumiy soat</span>
                                    <span class="text-2xl font-bold">{{ stats.total_hours }}</span>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-white/10 backdrop-blur rounded-lg">
                                    <span class="text-sm">Auditoriya</span>
                                    <span class="text-2xl font-bold">{{ stats.total_auditory_hours }}</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="p-3 bg-white/10 backdrop-blur rounded-lg text-center">
                                        <p class="text-xs opacity-90">1-sem</p>
                                        <p class="text-xl font-bold">{{ stats.semester_1.total_hours }}</p>
                                    </div>
                                    <div class="p-3 bg-white/10 backdrop-blur rounded-lg text-center">
                                        <p class="text-xs opacity-90">2-sem</p>
                                        <p class="text-xl font-bold">{{ stats.semester_2.total_hours }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tizim ma'lumotlari -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900">Tizim ma'lumotlari</h3>
                        </div>
                        <div class="p-6 space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Yaratilgan:</span>
                                <span class="font-medium text-gray-900">{{ formatDate(subject.created_at) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Yangilangan:</span>
                                <span class="font-medium text-gray-900">{{ formatDate(subject.updated_at) }}</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    subject: Object,
    stats: Object,
});

const getSubjectTypeLabel = (type) => {
    const types = {
        'asosiy': 'Asosiy',
        'yordamchi': 'Yordamchi',
        'ixtiyoriy': 'Ixtiyoriy'
    };
    return types[type] || type;
};

const getEducationFormLabel = (form) => {
    const forms = {
        'kunduzgi': 'Kunduzgi',
        'kechki': 'Kechki',
        'sirtqi': 'Sirtqi'
    };
    return forms[form] || form;
};

const getControlTypeLabel = (type) => {
    const types = {
        'imtihon': 'Imtihon',
        'test': 'Test',
        'baholash': 'Baholash'
    };
    return types[type] || type;
};

const formatDate = (date) => {
    if (!date) return '';
    
    const [year, month, day] = date.split('T')[0].split('-');
    return `${day}.${month}.${year}`;
};
</script>