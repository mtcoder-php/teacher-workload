<template>
    <Head title="Kafedralar" />

    <AuthenticatedLayout>
        <template #header>Kafedralar</template>

        <div class="space-y-6">
            <!-- Filters and Actions -->
            <div class="flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center">
                <div class="flex flex-col sm:flex-row gap-4 flex-1 w-full sm:w-auto">
                    <!-- Search -->
                    <div class="flex-1 max-w-md">
                        <input
                            v-model="searchQuery"
                            @input="search"
                            type="text"
                            placeholder="Kafedra qidirish..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        />
                    </div>

                    <!-- Faculty Filter -->
                    <select
                        v-model="facultyFilter"
                        @change="search"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent  w-1/2"
                    >
                        <option value="">Barcha fakultetlar</option>
                        <option v-for="faculty in faculties" :key="faculty.id" :value="faculty.id">
                            {{ faculty.name }}
                        </option>
                    </select>
                </div>

                <Link
                    href="/departments/create"
                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Yangi Kafedra</span>
                </Link>
            </div>

            <!-- Departments Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kafedra nomi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kod</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fakultet</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mudir</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">O'qituvchilar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amallar</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="(department, index) in departments.data" :key="department.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ (departments.current_page - 1) * departments.per_page + index + 1 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ department.name }}</div>
                                <div v-if="department.description" class="text-sm text-gray-500">
                                    {{ department.description.substring(0, 40) }}...
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                    {{ department.code }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ department.faculty?.name || '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ department.head?.name || '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ department.teachers_count || 0 }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    :class="department.is_active 
                                        ? 'bg-green-100 text-green-800' 
                                        : 'bg-red-100 text-red-800'"
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                >
                                    {{ department.is_active ? 'Aktiv' : 'Nofaol' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <Link
                                    :href="`/departments/${department.id}`"
                                    class="text-blue-600 hover:text-blue-900"
                                >
                                    Ko'rish
                                </Link>
                                <Link
                                    :href="`/departments/${department.id}/edit`"
                                    class="text-indigo-600 hover:text-indigo-900"
                                >
                                    Tahrirlash
                                </Link>
                                <button
                                    @click="deleteDepartment(department)"
                                    class="text-red-600 hover:text-red-900"
                                >
                                    O'chirish
                                </button>
                            </td>
                        </tr>

                        <!-- Empty State -->
                        <tr v-if="departments.data.length === 0">
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-500">
                                    <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <p class="text-lg font-medium">Kafedralar topilmadi</p>
                                    <p class="text-sm">Yangi kafedra qo'shish uchun yuqoridagi tugmani bosing</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div v-if="departments.data.length > 0" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Jami <span class="font-medium">{{ departments.total }}</span> ta kafedra
                        </div>
                        <div class="flex space-x-2">
                            <Link
                                v-for="(link, index) in departments.links"
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
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Kafedrani o'chirish</h3>
                <p class="text-gray-600 mb-6">
                    <strong>{{ departmentToDelete?.name }}</strong> kafedrasini o'chirishni xohlaysizmi? Bu amalni qaytarib bo'lmaydi.
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
import { Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
const props = defineProps({
    departments: Object,
    faculties: Array,
    filters: Object,
});

const searchQuery = ref(props.filters?.search || '');
const facultyFilter = ref(props.filters?.faculty_id || '');
const showDeleteModal = ref(false);
const departmentToDelete = ref(null);

const search = () => {
    router.get('/departments', 
        { 
            search: searchQuery.value || undefined,
            faculty_id: facultyFilter.value || undefined
        }, 
        {
            preserveState: true,
            replace: true,
        }
    );
};

const deleteDepartment = (department) => {
    departmentToDelete.value = department;
    showDeleteModal.value = true;
};

const confirmDelete = () => {
    router.delete(`/departments/${departmentToDelete.value.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
            departmentToDelete.value = null;
        },
    });
};
</script>