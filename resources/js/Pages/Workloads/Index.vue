<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    workloads: Object,
    filters: Object,
    departments: Array,
    teachers: Array,
    subjects: Array,
    academicYears: Array,
    directions: Array,
});

// Filters
const filters = ref({
    search: props.filters?.search || '',
    department_id: props.filters?.department_id || '',
    teacher_id: props.filters?.teacher_id || '',
    subject_id: props.filters?.subject_id || '',
    academic_year_id: props.filters?.academic_year_id || '',
    direction_id: props.filters?.direction_id || '',
    status: props.filters?.status || '',
    is_potok: props.filters?.is_potok || '',
});

// Search
const search = () => {
    router.get('/workloads', filters.value, {
        preserveState: true,
        preserveScroll: true,
    });
};

// Reset filters
const resetFilters = () => {
    filters.value = {
        search: '',
        department_id: '',
        teacher_id: '',
        subject_id: '',
        academic_year_id: '',
        direction_id: '',
        status: '',
        is_potok: '',
    };
    search();
};

// Delete
const deleteWorkload = (id) => {
    if (confirm('Rostdan ham o\'chirmoqchimisiz?')) {
        router.delete(`/workloads/${id}`, {
            preserveScroll: true,
        });
    }
};

// Status badge colors
const getStatusBadge = (status) => {
    const badges = {
        draft: 'bg-gray-100 text-gray-800',
        pending: 'bg-yellow-100 text-yellow-800',
        confirmed: 'bg-green-100 text-green-800',
        completed: 'bg-blue-100 text-blue-800',
    };
    return badges[status] || 'bg-gray-100 text-gray-800';
};

const getStatusLabel = (status) => {
    const labels = {
        draft: 'Qoralama',
        pending: 'Tekshiruvda',
        confirmed: 'Tasdiqlangan',
        completed: 'Tugatilgan',
    };
    return labels[status] || status;
};

// Workload type badge
const getWorkloadTypeBadge = (workload) => {
    if (workload.is_potok) {
        return 'bg-purple-100 text-purple-800';
    }
    return 'bg-blue-100 text-blue-800';
};

const getWorkloadTypeLabel = (workload) => {
    if (workload.is_potok) {
        return `📚 Potok: ${workload.potok_code}`;
    }
    if (workload.is_potok_remainder) {
        return '📝 Potok qoldig\'i';
    }
    return '📖 Oddiy';
};
</script>

<template>
    <AuthenticatedLayout title="Yuklama">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    O'qituvchilar yuklamasi
                </h2>
                <Link
                    href="/workloads/create"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Yuklama qo'shish
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Filters -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Search -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Qidiruv</label>
                                <input
                                    v-model="filters.search"
                                    type="text"
                                    placeholder="Fan, o'qituvchi..."
                                    @keyup.enter="search"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                            </div>

                            <!-- Department -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kafedra</label>
                                <select
                                    v-model="filters.department_id"
                                    @change="search"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="">Barcha kafedralar</option>
                                    <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                                        {{ dept.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Teacher -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">O'qituvchi</label>
                                <select
                                    v-model="filters.teacher_id"
                                    @change="search"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="">Barcha o'qituvchilar</option>
                                    <option v-for="teacher in teachers" :key="teacher.id" :value="teacher.id">
                                        {{ teacher.full_name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Academic Year -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">O'quv yili</label>
                                <select
                                    v-model="filters.academic_year_id"
                                    @change="search"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="">Barcha yillar</option>
                                    <option v-for="year in academicYears" :key="year.id" :value="year.id">
                                        {{ year.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Holat</label>
                                <select
                                    v-model="filters.status"
                                    @change="search"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="">Barcha holatlar</option>
                                    <option value="draft">Qoralama</option>
                                    <option value="pending">Tekshiruvda</option>
                                    <option value="confirmed">Tasdiqlangan</option>
                                    <option value="completed">Tugatilgan</option>
                                </select>
                            </div>

                            <!-- Potok Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Yuklama turi</label>
                                <select
                                    v-model="filters.is_potok"
                                    @change="search"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="">Hammasi</option>
                                    <option value="1">Potok</option>
                                    <option value="0">Oddiy</option>
                                </select>
                            </div>

                            <!-- Reset Button -->
                            <div class="flex items-end">
                                <button
                                    @click="resetFilters"
                                    class="w-full px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md transition"
                                >
                                    Tozalash
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    #
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Fan
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    O'qituvchi
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Guruhlar
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Soatlar
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Turi
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Holat
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Amallar
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="(workload, index) in workloads.data" :key="workload.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ index + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ workload.subject?.name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ workload.subject?.code }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ workload.teacher?.full_name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ workload.department?.name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                            <span
                                                v-for="group in workload.groups?.slice(0, 3)"
                                                :key="group.id"
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800"
                                            >
                                                {{ group.name }}
                                            </span>
                                        <span
                                            v-if="workload.groups?.length > 3"
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-200 text-gray-600"
                                        >
                                                +{{ workload.groups.length - 3 }}
                                            </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ workload.total_hours || 0 }} soat
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Talabalar: {{ workload.total_students || 0 }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="getWorkloadTypeBadge(workload)"
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                        >
                                            {{ getWorkloadTypeLabel(workload) }}
                                        </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="getStatusBadge(workload.status)"
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                        >
                                            {{ getStatusLabel(workload.status) }}
                                        </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <Link
                                            :href="`/workloads/${workload.id}`"
                                            class="text-blue-600 hover:text-blue-900"
                                            title="Ko'rish"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </Link>
                                        <Link
                                            v-if="workload.status === 'draft'"
                                            :href="`/workloads/${workload.id}/edit`"
                                            class="text-green-600 hover:text-green-900"
                                            title="Tahrirlash"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </Link>
                                        <button
                                            v-if="workload.status === 'draft'"
                                            @click="deleteWorkload(workload.id)"
                                            class="text-red-600 hover:text-red-900"
                                            title="O'chirish"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <!-- Empty state -->
                        <div v-if="!workloads.data || workloads.data.length === 0" class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Yuklama topilmadi</h3>
                            <p class="mt-1 text-sm text-gray-500">Yangi yuklama qo'shish uchun yuqoridagi tugmani bosing</p>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="workloads.data && workloads.data.length > 0 && workloads.links.length > 3" class="px-6 py-4 border-t">
                        <nav class="flex items-center justify-between">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <Link
                                    v-if="workloads.links[0].url"
                                    :href="workloads.links[0].url"
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    Oldingi
                                </Link>
                                <Link
                                    v-if="workloads.links[workloads.links.length - 1].url"
                                    :href="workloads.links[workloads.links.length - 1].url"
                                    class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    Keyingi
                                </Link>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        Ko'rsatilmoqda
                                        <span class="font-medium">{{ workloads.from || 0 }}</span>
                                        dan
                                        <span class="font-medium">{{ workloads.to || 0 }}</span>
                                        gacha,
                                        jami <span class="font-medium">{{ workloads.total || 0 }}</span> ta
                                    </p>
                                </div>
                                <div>
                                    <span class="relative z-0 inline-flex shadow-sm rounded-md">
                                        <template v-for="(link, index) in workloads.links" :key="index">
                                            <Link
                                                v-if="link.url"
                                                :href="link.url"
                                                :class="[
                                                    'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                                    link.active
                                                        ? 'z-10 bg-blue-600 border-blue-600 text-white'
                                                        : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50',
                                                    index === 0 ? 'rounded-l-md' : '',
                                                    index === workloads.links.length - 1 ? 'rounded-r-md' : '',
                                                    index !== 0 ? '-ml-px' : '',
                                                ]"
                                                v-html="link.label"
                                            />
                                            <span
                                                v-else
                                                :class="[
                                                    'relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700',
                                                    index === 0 ? 'rounded-l-md' : '',
                                                    index === workloads.links.length - 1 ? 'rounded-r-md' : '',
                                                    index !== 0 ? '-ml-px' : '',
                                                ]"
                                                v-html="link.label"
                                            />
                                        </template>
                                    </span>
                                </div>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
