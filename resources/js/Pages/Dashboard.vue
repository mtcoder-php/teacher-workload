<template>
    <Head title="Dashboard" />
    
    <AuthenticatedLayout>
        <template #header>Dashboard</template>

        <div class="space-y-6">
            <!-- Welcome Card -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl p-8 text-white">
                <h1 class="text-3xl font-bold mb-2">
                    Xush kelibsiz, {{ $page.props.auth?.user?.name || 'Foydalanuvchi' }}!
                </h1>
                <p class="text-indigo-100">
                    {{ $page.props.auth?.user?.role?.display_name || 'Rol' }} sifatida tizimga kirdingiz
                </p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Stat Card 1 -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Jami Yuklamalar</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats.total_workloads }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                        <span class="text-green-600 font-medium">↑ 12%</span>
                        <span class="text-gray-500 ml-2">o'tgan oyga nisbatan</span>
                    </div>
                </div>

                <!-- Stat Card 2 -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">O'qituvchilar</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats.teachers_count }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                        <span class="text-green-600 font-medium">Aktiv</span>
                        <span class="text-gray-500 ml-2">{{ stats.active_teachers }} nafar</span>
                    </div>
                </div>

                <!-- Stat Card 3 -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Fanlar</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats.subjects_count }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                        <span class="text-blue-600 font-medium">{{ stats.active_subjects }}</span>
                        <span class="text-gray-500 ml-2">aktiv</span>
                    </div>
                </div>

                <!-- Stat Card 4 -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Tasdiqlash Kerak</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats.pending_approvals }}</p>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                        <span class="text-orange-600 font-medium">Kutilmoqda</span>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">So'nggi Faoliyat</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div v-for="activity in recentActivities" :key="activity.id" class="flex items-start space-x-4">
                            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ activity.title }}</p>
                                <p class="text-sm text-gray-500">{{ activity.description }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ activity.time }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
defineProps({
    stats: {
        type: Object,
        default: () => ({
            total_workloads: 156,
            teachers_count: 45,
            active_teachers: 42,
            subjects_count: 28,
            active_subjects: 25,
            pending_approvals: 8,
        }),
    },
    recentActivities: {
        type: Array,
        default: () => [
            {
                id: 1,
                title: 'Yangi yuklama qo\'shildi',
                description: 'Karimov A. tomonidan Matematika fanidan',
                time: '5 daqiqa oldin',
            },
            {
                id: 2,
                title: 'Yuklama tasdiqlandi',
                description: 'Fizika fani bo\'yicha yuklama tasdiqlandi',
                time: '1 soat oldin',
            },
            {
                id: 3,
                title: 'Yangi o\'qituvchi qo\'shildi',
                description: 'Aliyeva M. tizimga qo\'shildi',
                time: '2 soat oldin',
            },
        ],
    },
});
</script>