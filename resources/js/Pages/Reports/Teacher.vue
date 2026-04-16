<template>
    <AuthenticatedLayout>
        <template #header>O'qituvchi Hisoboti</template>

        <div class="max-w-7xl mx-auto">

            <!-- Boshqaruv paneli -->
            <div class="bg-white rounded-lg shadow-sm p-5 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center
                                    text-blue-600 font-bold text-lg flex-shrink-0">
                            {{ teacher.name?.charAt(0)?.toUpperCase() }}
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">{{ teacher.name }}</h2>
                            <p class="text-sm text-gray-500">
                                {{ teacher.position }}
                                <span v-if="teacher.degree"> · {{ teacher.degree }}</span>
                                <span v-if="teacher.department"> · {{ teacher.department }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <select v-model="selectedYear" @change="reload"
                                class="px-6 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                            <option v-for="y in academicYears" :key="y.id" :value="y.id">
                                {{ y.name }}
                            </option>
                        </select>
                        <a :href="exportUrl"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white
                                  rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Excel
                        </a>
                        <Link :href="route('reports.index')"
                              class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                            ← Orqaga
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Statistika kartalar -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow-sm p-4 text-center">
                    <p class="text-2xl font-bold text-blue-600">{{ auditoria }}</p>
                    <p class="text-xs text-gray-500 mt-1">Auditoriya soati</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4 text-center">
                    <p class="text-2xl font-bold text-green-600">{{ totals.total }}</p>
                    <p class="text-xs text-gray-500 mt-1">Umumiy yuklama</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4 text-center">
                    <p class="text-2xl font-bold text-purple-600">{{ totals.rating }}</p>
                    <p class="text-xs text-gray-500 mt-1">Reyting soati</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4 text-center">
                    <p class="text-2xl font-bold text-orange-600">{{ rows.length }}</p>
                    <p class="text-xs text-gray-500 mt-1">Fanlar soni</p>
                </div>
            </div>

            <!-- Jadval -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-xs border-collapse">
                        <thead>
                        <tr class="bg-blue-900 text-white">
                            <th rowspan="2" class="px-3 py-3 text-left border border-blue-700 min-w-36">FAN NOMI</th>
                            <th rowspan="2" class="px-2 py-3 text-left border border-blue-700 min-w-24">YO'NALISH</th>
                            <th rowspan="2" class="px-2 py-3 text-center border border-blue-700 min-w-20">GURUHLAR</th>
                            <th rowspan="2" class="px-1 py-3 text-center border border-blue-700 w-10">KURS</th>
                            <th rowspan="2" class="px-1 py-3 text-center border border-blue-700 w-12">TALABA</th>
                            <th colspan="5" class="px-2 py-2 text-center border border-blue-700 bg-blue-800">1-SEMESTR</th>
                            <th colspan="5" class="px-2 py-2 text-center border border-blue-700 bg-blue-700">2-SEMESTR</th>
                            <th rowspan="2" class="px-1 py-3 text-center border border-blue-700 w-12">KURS ISHI</th>
                            <th rowspan="2" class="px-1 py-3 text-center border border-blue-700 w-12">DIPLOM</th>
                            <th rowspan="2" class="px-1 py-3 text-center border border-blue-700 w-12">KONSUL.</th>
                            <th rowspan="2" class="px-1 py-3 text-center border border-blue-700 w-12">REYTING</th>
                            <th rowspan="2" class="px-2 py-3 text-center border border-blue-700 w-14 bg-blue-600">JAMI</th>
                            <th rowspan="2" class="px-1 py-3 text-center border border-blue-700 w-16">HOLAT</th>
                        </tr>
                        <tr class="bg-blue-800 text-white text-center">
                            <th class="px-1 py-2 border border-blue-700 w-8"><span style="writing-mode:vertical-rl;transform:rotate(180deg);display:inline-block">Ma'ruza</span></th>
                            <th class="px-1 py-2 border border-blue-700 w-8"><span style="writing-mode:vertical-rl;transform:rotate(180deg);display:inline-block">Amaliy</span></th>
                            <th class="px-1 py-2 border border-blue-700 w-8"><span style="writing-mode:vertical-rl;transform:rotate(180deg);display:inline-block">Laborotoriya</span></th>
                            <th class="px-1 py-2 border border-blue-700 w-8"><span style="writing-mode:vertical-rl;transform:rotate(180deg);display:inline-block">Seminar</span></th>
                            <th class="px-1 py-2 border border-blue-700 w-8"><span style="writing-mode:vertical-rl;transform:rotate(180deg);display:inline-block">Amaliyot</span></th>
                            <th class="px-1 py-2 border border-blue-600 w-8"><span style="writing-mode:vertical-rl;transform:rotate(180deg);display:inline-block">Ma'ruza</span></th>
                            <th class="px-1 py-2 border border-blue-600 w-8"><span style="writing-mode:vertical-rl;transform:rotate(180deg);display:inline-block">Amaliy</span></th>
                            <th class="px-1 py-2 border border-blue-600 w-8"><span style="writing-mode:vertical-rl;transform:rotate(180deg);display:inline-block">Laborotoriya</span></th>
                            <th class="px-1 py-2 border border-blue-600 w-8"><span style="writing-mode:vertical-rl;transform:rotate(180deg);display:inline-block">Seminar</span></th>
                            <th class="px-1 py-2 border border-blue-600 w-8"><span style="writing-mode:vertical-rl;transform:rotate(180deg);display:inline-block">Amaliyot</span></th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                        <tr v-for="(row, i) in rows" :key="i"
                            class="hover:bg-gray-50"
                            :class="i % 2 === 0 ? 'bg-white' : 'bg-blue-50/20'">
                            <td class="px-3 py-2.5 font-medium text-gray-900 border-r border-gray-200">
                                {{ row.subject }}
                                <span v-if="row.is_potok" class="ml-1 px-1 py-0.5 bg-purple-100 text-purple-700 rounded text-xs">Potok</span>
                            </td>
                            <td class="px-2 py-2.5 text-gray-600 border-r border-gray-200">{{ row.direction }}</td>
                            <td class="px-2 py-2.5 text-gray-600 border-r border-gray-200">{{ row.groups }}</td>
                            <td class="px-1 py-2.5 text-center border-r border-gray-200">{{ row.course }}</td>
                            <td class="px-1 py-2.5 text-center border-r border-gray-200">{{ row.students }}</td>
                            <!-- 1-semestr -->
                            <td class="px-1 py-2.5 text-center border-r border-gray-100">{{ row.s1_lecture || '' }}</td>
                            <td class="px-1 py-2.5 text-center border-r border-gray-100">{{ row.s1_practical || '' }}</td>
                            <td class="px-1 py-2.5 text-center border-r border-gray-100">{{ row.s1_laboratory || '' }}</td>
                            <td class="px-1 py-2.5 text-center border-r border-gray-100">{{ row.s1_seminar || '' }}</td>
                            <td class="px-1 py-2.5 text-center border-r border-gray-200">{{ row.s1_practice || '' }}</td>
                            <!-- 2-semestr -->
                            <td class="px-1 py-2.5 text-center border-r border-gray-100 bg-blue-50/30">{{ row.s2_lecture || '' }}</td>
                            <td class="px-1 py-2.5 text-center border-r border-gray-100 bg-blue-50/30">{{ row.s2_practical || '' }}</td>
                            <td class="px-1 py-2.5 text-center border-r border-gray-100 bg-blue-50/30">{{ row.s2_laboratory || '' }}</td>
                            <td class="px-1 py-2.5 text-center border-r border-gray-100 bg-blue-50/30">{{ row.s2_seminar || '' }}</td>
                            <td class="px-1 py-2.5 text-center border-r border-gray-200 bg-blue-50/30">{{ row.s2_practice || '' }}</td>
                            <!-- Qo'shimcha -->
                            <td class="px-1 py-2.5 text-center border-r border-gray-200">{{ row.coursework || '' }}</td>
                            <td class="px-1 py-2.5 text-center border-r border-gray-200">{{ row.diploma || '' }}</td>
                            <td class="px-1 py-2.5 text-center border-r border-gray-200">{{ row.consultation || '' }}</td>
                            <td class="px-1 py-2.5 text-center border-r border-gray-200">{{ row.rating || '' }}</td>
                            <td class="px-1 py-2.5 text-center border-r border-gray-200 font-bold text-blue-700">{{ row.total }}</td>
                            <td class="px-2 py-2.5 text-center">
                                    <span class="px-1.5 py-0.5 rounded-full text-xs font-medium"
                                          :class="statusClass(row.status)">
                                        {{ statusLabel(row.status) }}
                                    </span>
                            </td>
                        </tr>

                        <tr v-if="!rows.length">
                            <td colspan="22" class="px-6 py-12 text-center text-gray-400">
                                Bu o'quv yili uchun yuklama topilmadi
                            </td>
                        </tr>
                        </tbody>
                        <tfoot v-if="rows.length">
                        <tr class="bg-yellow-50 font-bold text-sm">
                            <td colspan="5" class="px-3 py-3 text-right border-t-2 border-gray-300">JAMI:</td>
                            <td class="px-1 py-3 text-center border-t-2 border-gray-300">{{ totals.s1_lecture || '' }}</td>
                            <td class="px-1 py-3 text-center border-t-2 border-gray-300">{{ totals.s1_practical || '' }}</td>
                            <td class="px-1 py-3 text-center border-t-2 border-gray-300">{{ totals.s1_laboratory || '' }}</td>
                            <td class="px-1 py-3 text-center border-t-2 border-gray-300">{{ totals.s1_seminar || '' }}</td>
                            <td class="px-1 py-3 text-center border-t-2 border-gray-300">{{ totals.s1_practice || '' }}</td>
                            <td class="px-1 py-3 text-center border-t-2 border-gray-300">{{ totals.s2_lecture || '' }}</td>
                            <td class="px-1 py-3 text-center border-t-2 border-gray-300">{{ totals.s2_practical || '' }}</td>
                            <td class="px-1 py-3 text-center border-t-2 border-gray-300">{{ totals.s2_laboratory || '' }}</td>
                            <td class="px-1 py-3 text-center border-t-2 border-gray-300">{{ totals.s2_seminar || '' }}</td>
                            <td class="px-1 py-3 text-center border-t-2 border-gray-300">{{ totals.s2_practice || '' }}</td>
                            <td class="px-1 py-3 text-center border-t-2 border-gray-300">{{ totals.coursework || '' }}</td>
                            <td class="px-1 py-3 text-center border-t-2 border-gray-300">{{ totals.diploma || '' }}</td>
                            <td class="px-1 py-3 text-center border-t-2 border-gray-300">{{ totals.consultation || '' }}</td>
                            <td class="px-1 py-3 text-center border-t-2 border-gray-300">{{ totals.rating || '' }}</td>
                            <td class="px-1 py-3 text-center border-t-2 border-gray-300 text-blue-700">{{ totals.total }}</td>
                            <td class="border-t-2 border-gray-300"></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const route = window.route

const props = defineProps({
    teacher:       Object,
    academicYear:  Object,
    academicYears: Array,
    rows:          Array,
    totals:        Object,
    auditoria:     Number,
})

const selectedYear = ref(props.academicYear?.id)

const exportUrl = computed(() =>
    route('reports.export.teacher', props.teacher.id) + '?academic_year_id=' + selectedYear.value
)

function reload() {
    router.get(route('reports.teacher', props.teacher.id), {
        academic_year_id: selectedYear.value,
    }, { preserveScroll: true })
}

function statusClass(status) {
    return {
        confirmed: 'bg-green-100 text-green-700',
        pending:   'bg-yellow-100 text-yellow-700',
        draft:     'bg-gray-100 text-gray-600',
    }[status] ?? 'bg-gray-100 text-gray-600'
}

function statusLabel(status) {
    return {
        confirmed: 'Tasdiqlangan',
        pending:   'Tekshiruvda',
        draft:     'Qoralama',
    }[status] ?? status
}
</script>
