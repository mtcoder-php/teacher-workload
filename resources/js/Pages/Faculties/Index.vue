<template>
    <Head title="Fakultetlar" />
    <AuthenticatedLayout>
        <template #header>Fakultetlar</template>

        <div class="space-y-6">
            <!-- Top Actions -->
            <div class="flex justify-between items-center">
                <div class="flex-1 max-w-lg">
                    <input
                        v-model="searchQuery"
                        @input="searchFaculties"
                        type="text"
                        placeholder="Fakultet qidirish..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    />
                </div>
                <Link
                    href="/faculties/create"
                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Yangi Fakultet</span>
                </Link>
            </div>

            <!-- Faculties Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                #
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fakultet nomi
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kod
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Dekan
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kafedralar
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Amallar
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="(faculty, index) in faculties.data" :key="faculty.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ (faculties.current_page - 1) * faculties.per_page + index + 1 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ faculty.name }}</div>
                                <div v-if="faculty.description" class="text-sm text-gray-500">
                                    {{ faculty.description.substring(0, 50) }}...
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                    {{ faculty.code }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ faculty.dean?.name || '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ faculty.departments_count || 0 }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    :class="faculty.is_active 
                                        ? 'bg-green-100 text-green-800' 
                                        : 'bg-red-100 text-red-800'"
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                >
                                    {{ faculty.is_active ? 'Aktiv' : 'Nofaol' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <Link
                                    :href="`/faculties/${faculty.id}`"
                                    class="text-blue-600 hover:text-blue-900"
                                >
                                    Ko'rish
                                </Link>
                                <Link
                                    :href="`/faculties/${faculty.id}/edit`"
                                    class="text-indigo-600 hover:text-indigo-900"
                                >
                                    Tahrirlash
                                </Link>
                                <button
                                    @click="deleteFaculty(faculty)"
                                    class="text-red-600 hover:text-red-900"
                                >
                                    O'chirish
                                </button>
                            </td>
                        </tr>

                        <!-- Empty State -->
                        <tr v-if="faculties.data.length === 0">
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-500">
                                    <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <p class="text-lg font-medium">Fakultetlar topilmadi</p>
                                    <p class="text-sm">Yangi fakultet qo'shish uchun yuqoridagi tugmani bosing</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div v-if="faculties.data.length > 0" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Jami <span class="font-medium">{{ faculties.total }}</span> ta fakultet
                        </div>
                        <div class="flex space-x-2">
                            <Link
                                v-for="(link, index) in faculties.links"
                                :key="index"
                                :href="link.url || '#'"
                                :class="[
                                    'px-3 py-1 rounded border',
                                    link.active 
                                        ? 'bg-indigo-600 text-white border-indigo-600' 
                                        : link.url 
                                            ? 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                                            : 'bg-gray-100 text-gray-400 border-gray-300 cursor-not-allowed'
                                ]"
                                :preserve-scroll="true"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Fakultetni o'chirish</h3>
                <p class="text-gray-600 mb-6">
                    <strong>{{ facultyToDelete?.name }}</strong> fakultetini o'chirishni xohlaysizmi? Bu amalni qaytarib bo'lmaydi.
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
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    faculties: Object,
    filters: Object,
});

const searchQuery = ref(props.filters?.search || '');
const showDeleteModal = ref(false);
const facultyToDelete = ref(null);

const searchFaculties = () => {
    router.get('/faculties', 
        { search: searchQuery.value || undefined },
        {
            preserveState: true,
            replace: true,
        }
    );
};

const deleteFaculty = (faculty) => {
    facultyToDelete.value = faculty;
    showDeleteModal.value = true;
};

const confirmDelete = () => {
    router.delete(`/faculties/${facultyToDelete.value.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
            facultyToDelete.value = null;
        },
    });
};
</script>