<template>

    <Head>
        <title>{{ department.name }}</title>
    </Head>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <span>{{ department.name }}</span>
                <Link :href="`/departments/${department.id}/edit`"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">
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
                            <label class="block text-sm font-medium text-gray-500 mb-1">Kafedra nomi</label>
                            <p class="text-lg font-semibold text-gray-900">{{ department.name }}</p>
                        </div>

                        <!-- Code -->
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Kod</label>
                            <span
                                class="inline-block px-3 py-1 text-sm font-semibold rounded-full bg-purple-100 text-purple-800">
                                {{ department.code }}
                            </span>
                        </div>

                        <!-- Faculty -->
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Fakultet</label>
                            <Link :href="`/faculties/${department.faculty?.id}`"
                                class="text-lg font-semibold text-indigo-600 hover:text-indigo-900">
                            {{ department.faculty?.name || '—' }}
                            </Link>
                        </div>

                        <!-- Head -->
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Kafedra mudiri</label>
                            <div v-if="department.head" class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                        <span class="text-indigo-600 font-semibold">
                                            {{ department.head.name.charAt(0) }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ department.head.name }}</p>
                                    <p class="text-xs text-gray-500">{{ department.head.email }}</p>
                                </div>
                            </div>
                            <p v-else class="text-gray-500">—</p>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                            <span :class="department.is_active
                                ? 'bg-green-100 text-green-800'
                                : 'bg-red-100 text-red-800'"
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold">
                                <span :class="department.is_active ? 'bg-green-400' : 'bg-red-400'"
                                    class="w-2 h-2 rounded-full mr-2"></span>
                                {{ department.is_active ? 'Faol' : 'Nofaol' }}
                            </span>
                        </div>

                        <!-- Description -->
                        <div v-if="department.description" class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Tavsif</label>
                            <p class="text-gray-700">{{ department.description }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Teachers Count -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow-sm border border-blue-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-600 mb-1">O'qituvchilar</p>
                            <p class="text-3xl font-bold text-blue-900">{{ stats.teachers_count }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-200 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Subjects Count -->
                <div
                    class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl shadow-sm border border-purple-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-purple-600 mb-1">Fanlar</p>
                            <p class="text-3xl font-bold text-purple-900">{{ stats.subjects_count }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-200 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Teachers List -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-800">O'qituvchilar</h2>
                    <span class="text-sm text-gray-500">{{ department.teachers?.length || 0 }} ta</span>
                </div>
                <div class="p-6">
                    <div v-if="department.teachers && department.teachers.length > 0" class="space-y-4">
                        <div v-for="teacher in department.teachers" :key="teacher.id"
                            class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                                    <span class="text-indigo-600 font-semibold text-lg">
                                        {{ teacher.user?.name?.charAt(0) || 'O' }}
                                    </span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ teacher.user?.name || 'Noma\'lum' }}</p>
                                    <p class="text-sm text-gray-500">{{ teacher.position || 'O\'qituvchi' }}</p>
                                </div>
                            </div>
                            <Link :href="`/teachers/${teacher.id}`"
                                class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                            Batafsil →
                            </Link>
                        </div>
                    </div>
                    <div v-else class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">Hozircha o'qituvchilar yo'q</p>
                    </div>
                </div>
            </div>

            <!-- Subjects List -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-800">Fanlar</h2>
                    <span class="text-sm text-gray-500">{{ department.subjects?.length || 0 }} ta</span>
                </div>
                <div class="p-6">
                    <div v-if="department.subjects && department.subjects.length > 0"
                        class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-for="subject in department.subjects" :key="subject.id"
                            class="p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="font-medium text-gray-900 mb-1">{{ subject.name }}</h3>
                                    <p class="text-sm text-gray-500">Kod: {{ subject.code }}</p>
                                    <div class="flex items-center space-x-3 mt-2">
                                        <span class="text-xs text-gray-600">
                                            {{ subject.credit }} kredit
                                        </span>
                                        <span :class="subject.is_active
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-red-100 text-red-800'"
                                            class="px-2 py-0.5 rounded text-xs font-medium">
                                            {{ subject.is_active ? 'Aktiv' : 'Nofaol' }}
                                        </span>
                                    </div>
                                </div>
                                <Link :href="`/subjects/${subject.id}`"
                                    class="text-indigo-600 hover:text-indigo-900 text-sm font-medium ml-4">
                                →
                                </Link>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">Hozircha fanlar yo'q</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-between items-center">
                <Link href="/departments"
                    class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Orqaga</span>
                </Link>

                <button @click="showDeleteModal = true"
                    class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    O'chirish
                </button>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Kafedrani o'chirish</h3>
                <p class="text-gray-600 mb-6">
                    <strong>{{ department.name }}</strong> kafedrasini o'chirishni xohlaysizmi? Bu amalni qaytarib
                    bo'lmaydi.
                </p>
                <div class="flex justify-end space-x-3">
                    <button @click="showDeleteModal = false"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Bekor qilish
                    </button>
                    <button @click="confirmDelete" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        O'chirish
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    department: Object,
    stats: Object,
});

const showDeleteModal = ref(false);

const confirmDelete = () => {
    router.delete(`/departments/${props.department.id}`, {
        onSuccess: () => {
            router.visit('/departments');
        },
    });
};
</script>