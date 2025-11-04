<template>
    <Head :title="faculty.name" />
    
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <span>{{ faculty.name }}</span>
                <Link
                    :href="`/faculties/${faculty.id}/edit`"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm"
                >
                    Tahrirlash
                </Link>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Main Info Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-xl font-semibold text-gray-800">Asosiy ma'lumotlar</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Fakultet nomi</label>
                            <p class="text-lg font-semibold text-gray-900">{{ faculty.name }}</p>
                        </div>

                        <!-- Code -->
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Kod</label>
                            <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                {{ faculty.code }}
                            </span>
                        </div>

                        <!-- Dean -->
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Dekan</label>
                            <div v-if="faculty.dean" class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                                        <span class="text-indigo-600 font-semibold text-lg">
                                            {{ faculty.dean.name.charAt(0) }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ faculty.dean.name }}</p>
                                    <p class="text-xs text-gray-500">{{ faculty.dean.email }}</p>
                                    <p v-if="faculty.dean.phone" class="text-xs text-gray-500">{{ faculty.dean.phone }}</p>
                                </div>
                            </div>
                            <p v-else class="text-gray-500">—</p>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                            <span
                                :class="faculty.is_active 
                                    ? 'bg-green-100 text-green-800' 
                                    : 'bg-red-100 text-red-800'"
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold"
                            >
                                <span :class="faculty.is_active ? 'bg-green-400' : 'bg-red-400'" 
                                      class="w-2 h-2 rounded-full mr-2"></span>
                                {{ faculty.is_active ? 'Faol' : 'Nofaol' }}
                            </span>
                        </div>

                        <!-- Description -->
                        <div v-if="faculty.description" class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Tavsif</label>
                            <p class="text-gray-700">{{ faculty.description }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Departments Count -->
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl shadow-sm border border-purple-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-purple-600 mb-1">Kafedralar</p>
                            <p class="text-3xl font-bold text-purple-900">{{ faculty.departments?.length || 0 }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-200 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Teachers Count -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow-sm border border-blue-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-600 mb-1">O'qituvchilar</p>
                            <p class="text-3xl font-bold text-blue-900">{{ totalTeachers }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-200 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Groups Count -->
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl shadow-sm border border-green-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-600 mb-1">Guruhlar</p>
                            <p class="text-3xl font-bold text-green-900">{{ faculty.groups?.length || 0 }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-200 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Departments List -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-800">Kafedralar</h2>
                    <span class="text-sm text-gray-500">{{ faculty.departments?.length || 0 }} ta</span>
                </div>
                <div class="p-6">
                    <div v-if="faculty.departments && faculty.departments.length > 0" class="space-y-4">
                        <div v-for="department in faculty.departments" :key="department.id" 
                             class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <span class="text-purple-600 font-bold text-sm">
                                        {{ department.code }}
                                    </span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ department.name }}</p>
                                    <p class="text-sm text-gray-500">
                                        {{ department.teachers_count || 0 }} ta o'qituvchi
                                    </p>
                                </div>
                            </div>
                            <Link 
                                :href="`/departments/${department.id}`"
                                class="text-indigo-600 hover:text-indigo-900 text-sm font-medium"
                            >
                                Batafsil →
                            </Link>
                        </div>
                    </div>
                    <div v-else class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">Hozircha kafedralar yo'q</p>
                    </div>
                </div>
            </div>

            <!-- Groups List -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-800">Guruhlar</h2>
                    <span class="text-sm text-gray-500">{{ faculty.groups?.length || 0 }} ta</span>
                </div>
                <div class="p-6">
                    <div v-if="faculty.groups && faculty.groups.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div v-for="group in faculty.groups" :key="group.id" 
                             class="p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-lg border border-green-200 hover:shadow-md transition">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ group.name }}</h3>
                                    <p class="text-sm text-gray-600">{{ group.course }}-kurs</p>
                                </div>
                                <span class="px-2 py-1 bg-green-200 text-green-800 text-xs font-semibold rounded">
                                    {{ group.student_count || 0 }}
                                </span>
                            </div>
                            <Link 
                                :href="`/groups/${group.id}`"
                                class="text-green-700 hover:text-green-900 text-sm font-medium"
                            >
                                Batafsil →
                            </Link>
                        </div>
                    </div>
                    <div v-else class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">Hozircha guruhlar yo'q</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-between items-center">
                <Link
                    href="/faculties"
                    class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Orqaga</span>
                </Link>

                <button
                    @click="showDeleteModal = true"
                    class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
                >
                    O'chirish
                </button>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Fakultetni o'chirish</h3>
                <p class="text-gray-600 mb-6">
                    <strong>{{ faculty.name }}</strong> fakultetini o'chirishni xohlaysizmi? Bu amalni qaytarib bo'lmaydi.
                </p>
                <div class="flex justify-end space-x-3">
                    <button
                        @click="showDeleteModal = false"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                    >
                        Bekor qilish
                    </button>
                    <button
                        @click="confirmDelete"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                    >
                        O'chirish
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    faculty: Object,
});

const showDeleteModal = ref(false);

const totalTeachers = computed(() => {
    if (!props.faculty.departments) return 0;
    return props.faculty.departments.reduce((sum, dept) => {
        return sum + (dept.teachers_count || 0);
    }, 0);
});

const confirmDelete = () => {
    router.delete(`/faculties/${props.faculty.id}`, {
        onSuccess: () => {
            router.visit('/faculties');
        },
    });
};
</script>