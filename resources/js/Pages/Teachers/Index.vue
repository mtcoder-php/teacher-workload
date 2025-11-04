<template>

    <Head title="O'qituvchilar" />

    <AuthenticatedLayout>
        <template #header>O'qituvchilar</template>

        <div class="space-y-6">
            <!-- Filters and Actions -->
            <div class="flex flex-col lg:flex-row gap-4 justify-between items-start lg:items-center">
                <div class="flex flex-col sm:flex-row gap-4 flex-1 w-full lg:w-auto">
                    <!-- Search -->
                    <div class="flex-1 max-w-md">
                        <input v-model="searchQuery" @input="search" type="text"
                            placeholder="Ism yoki email bo'yicha qidirish..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                    </div>

                    <!-- Department Filter -->
                    <select v-model="departmentFilter" @change="search"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="">Barcha kafedralar</option>
                        <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                            {{ dept.name }}
                        </option>
                    </select>

                    <!-- Employment Type Filter -->
                    <select v-model="employmentFilter" @change="search"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="">Barcha bandlik turlari</option>
                        <option value="main_job">Asosiy ish joyi</option>
                        <option value="internal_part_time">O'rindoshlik (ichki-asosiy)</option>
                        <option value="internal_additional">O'rindoshlik (ichki-qo'shimcha)</option>
                        <option value="external_part_time">O'rindoshlik (tashqi)</option>
                        <option value="hourly">Soatbay</option>
                    </select>
                </div>

                <Link href="/teachers/create"
                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition flex items-center space-x-2 whitespace-nowrap">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Yangi O'qituvchi</span>
                </Link>
            </div>

            <!-- Teachers Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    #
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    F.I.O
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kafedra</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Lavozim</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Bandlik</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Amallar</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="(teacher, index) in teachers.data" :key="teacher.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ (teachers.current_page - 1) * teachers.per_page + index + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div
                                                class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                                <span class="text-indigo-600 font-semibold text-sm">
                                                    {{ teacher.user?.name?.charAt(0) || 'O' }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ teacher.user?.name }}
                                            </div>
                                            <div class="text-sm text-gray-500">{{ teacher.user?.email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ teacher.department?.name }}</div>
                                    <div class="text-sm text-gray-500">{{ teacher.department?.faculty?.name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ teacher.position || '—' }}
                                </td>
                                <!-- Table Badge -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="{
                                        'bg-green-100 text-green-800': teacher.employment_type === 'main_job',
                                        'bg-blue-100 text-blue-800': teacher.employment_type === 'internal_part_time',
                                        'bg-purple-100 text-purple-800': teacher.employment_type === 'internal_additional',
                                        'bg-yellow-100 text-yellow-800': teacher.employment_type === 'external_part_time',
                                        'bg-orange-100 text-orange-800': teacher.employment_type === 'hourly'
                                    }" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                        {{ getEmploymentTypeName(teacher.employment_type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="teacher.is_active
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-red-100 text-red-800'"
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                        {{ teacher.is_active ? 'Aktiv' : 'Nofaol' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <Link :href="`/teachers/${teacher.id}`" class="text-blue-600 hover:text-blue-900">
                                    Ko'rish
                                    </Link>
                                    <Link :href="`/teachers/${teacher.id}/edit`"
                                        class="text-indigo-600 hover:text-indigo-900">
                                    Tahrirlash
                                    </Link>
                                    <button @click="deleteTeacher(teacher)" class="text-red-600 hover:text-red-900">
                                        O'chirish
                                    </button>
                                </td>
                            </tr>

                            <!-- Empty State -->
                            <tr v-if="teachers.data.length === 0">
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-500">
                                        <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        <p class="text-lg font-medium">O'qituvchilar topilmadi</p>
                                        <p class="text-sm">Yangi o'qituvchi qo'shish uchun yuqoridagi tugmani bosing</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="teachers.data.length > 0" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Jami <span class="font-medium">{{ teachers.total }}</span> ta o'qituvchi
                        </div>
                        <div class="flex space-x-2">
                            <Link v-for="(link, index) in teachers.links" :key="index" :href="link.url || '#'" :class="[
                                'px-3 py-1 rounded border',
                                link.active
                                    ? 'bg-indigo-600 text-white border-indigo-600'
                                    : link.url
                                        ? 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                                        : 'bg-gray-100 text-gray-400 border-gray-300 cursor-not-allowed'
                            ]" :preserve-scroll="true" v-html="link.label" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">O'qituvchini o'chirish</h3>
                <p class="text-gray-600 mb-6">
                    <strong>{{ teacherToDelete?.user?.name }}</strong> o'qituvchisini o'chirishni xohlaysizmi? Bu amal
                    user
                    akkauntini ham o'chiradi va qaytarib bo'lmaydi.
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
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    teachers: Object,
    departments: Array,
    filters: Object,
});

const searchQuery = ref(props.filters?.search || '');
const departmentFilter = ref(props.filters?.department_id || '');
const employmentFilter = ref(props.filters?.employment_type || '');
const showDeleteModal = ref(false);
const teacherToDelete = ref(null);

const search = () => {
    router.get('/teachers',
        {
            search: searchQuery.value || undefined,
            department_id: departmentFilter.value || undefined,
            employment_type: employmentFilter.value || undefined
        },
        {
            preserveState: true,
            replace: true,
        }
    );
};

const getEmploymentTypeName = (type) => {
    const types = {
        'main_job': 'Asosiy ish joyi',
        'internal_part_time': 'O\'rindoshlik (ichki-asosiy)',
        'internal_additional': 'O\'rindoshlik (ichki-qo\'shimcha)',
        'external_part_time': 'O\'rindoshlik (tashqi)',
        'hourly': 'Soatbay'
    };
    return types[type] || 'Noma\'lum';
};

const deleteTeacher = (teacher) => {
    teacherToDelete.value = teacher;
    showDeleteModal.value = true;
};

const confirmDelete = () => {
    router.delete(`/teachers/${teacherToDelete.value.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
            teacherToDelete.value = null;
        },
    });
};
</script>