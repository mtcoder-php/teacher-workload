<template>
    <Head title="Guruhlar" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-900">Guruhlar</h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- FILTERS -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                        <!-- Search -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Qidirish</label>
                            <input
                                v-model="searchQuery"
                                @input="search"
                                type="text"
                                placeholder="Nomi yoki kodi..."
                                class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition"
                            />
                        </div>

                        <!-- Direction Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Yo'nalish</label>
                            <select
                                v-model="directionFilter"
                                @change="search"
                                class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition"
                            >
                                <option value="">Barcha</option>
                                <option v-for="direction in directionsList" :key="direction.id" :value="direction.id">
                                    {{ direction.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Course Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kurs</label>
                            <select
                                v-model="courseFilter"
                                @change="search"
                                class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition"
                            >
                                <option value="">Barcha</option>
                                <option value="1">1-kurs</option>
                                <option value="2">2-kurs</option>
                                <option value="3">3-kurs</option>
                                <option value="4">4-kurs</option>
                                <option value="5">5-kurs</option>
                                <option value="6">6-kurs</option>
                            </select>
                        </div>

                        <!-- Education Type Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ta'lim shakli</label>
                            <select
                                v-model="educationTypeFilter"
                                @change="search"
                                class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition"
                            >
                                <option value="">Barcha</option>
                                <option value="kunduzgi">Kunduzgi</option>
                                <option value="sirtqi">Sirtqi</option>
                                <option value="kechki">Kechki</option>
                                <option value="masofaviy">Masofaviy</option>
                            </select>
                        </div>

                        <!-- Education Language Filter ✅ YANGI -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ta'lim tili</label>
                            <select
                                v-model="educationLanguageFilter"
                                @change="search"
                                class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition"
                            >
                                <option value="">Barcha</option>
                                <option value="uzbek">O'zbek</option>
                                <option value="russian">Rus</option>
                            </select>
                        </div>

                        <!-- Create Button -->
                        <div class="flex items-end">
                            <Link
                                href="/groups/create"
                                class="w-full px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition flex items-center justify-center gap-2 font-medium shadow-lg"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Yangi</span>
                            </Link>
                        </div>
                    </div>

                
                </div>

                <!-- TABLE -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">#</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Guruh</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Kod</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Yo'nalish</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Kurs</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Ta'lim</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Talabalar</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Amallar</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="(group, index) in groups.data" :key="group.id" class="hover:bg-blue-50 transition">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                        {{ (groups.current_page - 1) * groups.per_page + index + 1 }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-gray-900">{{ group.name }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-bold">
                                            {{ group.code }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ group.direction?.name || '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="font-semibold text-gray-900 text-sm">{{ getCourseLabel(group.course) }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="text-xs space-y-1">
                                            <div class="inline-block px-2 py-1 bg-indigo-100 text-indigo-700 rounded-full font-semibold">
                                                {{ getEducationTypeLabel(group.education_type) }}
                                            </div>
                                            <!-- ✅ YANGI - Ta'lim tili ko'rsatish -->
                                            <div class="inline-block px-2 py-1 bg-amber-100 text-amber-700 rounded-full font-semibold ml-1">
                                                {{ getEducationLanguageLabel(group.education_language) }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-semibold">
                                            👥 {{ group.student_count || 0 }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span
                                            :class="group.is_active 
                                                ? 'bg-green-100 text-green-800' 
                                                : 'bg-red-100 text-red-800'"
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold"
                                        >
                                            <svg class="w-4 h-4" :fill="group.is_active ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ group.is_active ? 'Faol' : 'Nofaol' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <Link
                                                :href="`/groups/${group.id}`"
                                                title="Ko'rish"
                                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </Link>
                                            <Link
                                                :href="`/groups/${group.id}/edit`"
                                                title="Tahrirlash"
                                                class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </Link>
                                            <button
                                                @click="deleteGroup(group)"
                                                title="O'chirish"
                                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Empty State -->
                                <tr v-if="groups.data.length === 0">
                                    <td colspan="9" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-500">
                                            <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            <p class="text-lg font-semibold text-gray-900">Guruhlar topilmadi</p>
                                            <p class="text-sm text-gray-600 mt-1">Yangi guruh qo'shish uchun tugmani bosing</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- PAGINATION -->
                    <div v-if="groups.data.length > 0" class="bg-white px-6 py-4 border-t border-gray-200 flex items-center justify-between flex-wrap gap-4">
                        <div class="text-sm text-gray-600">
                            Jami: <span class="font-semibold">{{ groups.total }}</span> ta guruh
                        </div>
                        <div class="flex space-x-1 flex-wrap">
                            <Link
                                v-for="(link, index) in groups.links"
                                :key="index"
                                :href="link.url || '#'"
                                :class="[
                                    'px-3 py-1 rounded border text-sm transition font-medium',
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
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    groups: Object,
    directions: Array,
    filters: Object,
});

const directionsList = computed(() => {
    return props.directions?.data || props.directions || [];
});

const searchQuery = ref(props.filters?.search || '');
const directionFilter = ref(props.filters?.direction_id || '');
const courseFilter = ref(props.filters?.course || '');
const educationTypeFilter = ref(props.filters?.education_type || '');
const educationLanguageFilter = ref(props.filters?.education_language || ''); // ✅ YANGI

const search = () => {
    router.get('/groups', 
        { 
            search: searchQuery.value || undefined,
            direction_id: directionFilter.value || undefined,
            course: courseFilter.value || undefined,
            education_type: educationTypeFilter.value || undefined,
            education_language: educationLanguageFilter.value || undefined, // ✅ YANGI
        }, 
        {
            preserveState: true,
            replace: true,
        }
    );
};

const deleteGroup = (group) => {
    if (confirm(`"${group.name}" guruhini o'chirmoqchimisiz?`)) {
        router.delete(`/groups/${group.id}`, {
            preserveScroll: true,
        });
    }
};

const getCourseLabel = (course) => {
    const labels = {
        1: '1-kurs',
        2: '2-kurs',
        3: '3-kurs',
        4: '4-kurs',
        5: '1-kurs (Magistr)',
        6: '2-kurs (Magistr)',
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
        'uzbek': 'O\'zbek',
        'russian': 'Rus',
    };
    return labels[language] || language;
};
</script>