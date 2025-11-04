<template>
    <Head title="Ta'lim yo'nalishlari" />

    <AuthenticatedLayout>
        <template #header>Ta'lim yo'nalishlari</template>

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
                            placeholder="Yo'nalish nomi yoki kodi bo'yicha qidirish..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        />
                    </div>

                    <!-- Department Filter -->
                    <select
                        v-model="departmentFilter"
                        @change="search"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full sm:w-auto"
                    >
                        <option value="">Barcha kafedralar</option>
                        <option v-for="department in departments" :key="department.id" :value="department.id">
                            {{ department.name }}
                        </option>
                    </select>

                    <!-- Degree Type Filter -->
                    <select
                        v-model="degreeTypeFilter"
                        @change="search"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full sm:w-auto"
                    >
                        <option value="">Barcha darajalar</option>
                        <option value="bakalavr">Bakalavr</option>
                        <option value="magistratura">Magistratura</option>
                    </select>
                </div>

                <Link
                    href="/directions/create"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Yangi Yo'nalish</span>
                </Link>
            </div>

            <!-- Directions Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Yo'nalish nomi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kod</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kafedra</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Daraja</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Davomiyligi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amallar</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="(direction, index) in directions.data" :key="direction.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ (directions.current_page - 1) * directions.per_page + index + 1 }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ direction.name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                        {{ direction.code }}
                                    </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ direction.department?.name || '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800 capitalize">
                                        {{ direction.degree_type }}
                                    </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ direction.duration_years }} yil
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="direction.is_active
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-red-100 text-red-800'"
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    >
                                        {{ direction.is_active ? 'Faol' : 'Nofaol' }}
                                    </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <Link
                                    :href="`/directions/${direction.id}`"
                                    class="text-blue-600 hover:text-blue-900"
                                >
                                    Ko'rish
                                </Link>
                                <Link
                                    :href="`/directions/${direction.id}/edit`"
                                    class="text-indigo-600 hover:text-indigo-900"
                                >
                                    Tahrirlash
                                </Link>
                                <button
                                    @click="deleteDirection(direction)"
                                    class="text-red-600 hover:text-red-900"
                                >
                                    O'chirish
                                </button>
                            </td>
                        </tr>

                        <!-- Empty State -->
                        <tr v-if="directions.data.length === 0">
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-500">
                                    <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                    </svg>
                                    <p class="text-lg font-medium">Yo'nalishlar topilmadi</p>
                                    <p class="text-sm">Yangi yo'nalish qo'shish uchun yuqoridagi tugmani bosing</p>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="directions.data.length > 0" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Jami <span class="font-medium">{{ directions.total }}</span> ta yo'nalish
                        </div>
                        <div class="flex space-x-2">
                            <Link
                                v-for="(link, index) in directions.links"
                                :key="index"
                                :href="link.url || '#'"
                                :class="[
                                    'px-3 py-1 rounded border',
                                    link.active
                                        ? 'bg-blue-600 text-white border-blue-600'
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
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    directions: Object,
    departments: Array,
    filters: Object,
});

const searchQuery = ref(props.filters?.search || '');
const departmentFilter = ref(props.filters?.department_id || '');
const degreeTypeFilter = ref(props.filters?.degree_type || '');

const search = () => {
    router.get('/directions',
        {
            search: searchQuery.value || undefined,
            department_id: departmentFilter.value || undefined,
            degree_type: degreeTypeFilter.value || undefined,
        },
        {
            preserveState: true,
            replace: true,
        }
    );
};

const deleteDirection = (direction) => {
    if (confirm(`${direction.name} yo'nalishini o'chirmoqchimisiz?`)) {
        router.delete(`/directions/${direction.id}`, {
            preserveScroll: true,
        });
    }
};
</script>
