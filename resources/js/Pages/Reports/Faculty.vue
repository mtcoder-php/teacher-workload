<template>
    <AuthenticatedLayout>
        <template #header>Fakultet Hisoboti</template>

        <div class="max-w-full mx-auto">

            <!-- Boshqaruv paneli -->
            <div class="bg-white rounded-lg shadow-sm p-5 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">{{ faculty.name }}</h2>
                        <p class="text-sm text-gray-500">{{ academicYear?.name }} o'quv yili — to'liq yuklama jadvali</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <select v-model="selectedYear" @change="reload"
                                class="px-6 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 outline-none">
                            <option v-for="y in academicYears" :key="y.id" :value="y.id">
                                {{ y.name }}
                            </option>
                        </select>
                        <a :href="exportUrl"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 text-white
                                  rounded-lg hover:bg-purple-700 transition-colors text-sm font-medium">
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

            <!-- Jadval -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="p-4 bg-blue-900 text-white text-center text-sm font-semibold">
                    REJA (Ikki semestr uchun) — {{ academicYear?.name }}
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-xs border-collapse" style="min-width: 1600px">
                        <thead>
                        <!-- Guruh sarlavhalar -->
                        <tr class="bg-blue-900 text-white">
                            <th rowspan="2" class="px-2 py-3 text-left border border-blue-700 min-w-36">O'QITUVCHI</th>
                            <th rowspan="2" class="px-2 py-3 text-left border border-blue-700 min-w-36">FAN NOMI</th>
                            <th rowspan="2" class="px-2 py-3 text-left border border-blue-700 min-w-24">TA'LIM YO'NALISHI</th>
                            <th rowspan="2" class="px-2 py-3 text-center border border-blue-700 min-w-24">GURUHLAR</th>
                            <th rowspan="2" class="px-2 py-3 text-center border border-blue-700 w-10">KURS</th>
                            <th rowspan="2" class="px-2 py-3 text-center border border-blue-700 w-12">GURUH SONI</th>
                            <th rowspan="2" class="px-2 py-3 text-center border border-blue-700 w-12">TALABA SONI</th>
                            <th rowspan="2" class="px-2 py-3 text-center border border-blue-700 w-10">POTOK</th>
                            <th colspan="6" class="px-2 py-2 text-center border border-blue-700 bg-blue-800">1-SEMESTR</th>
                            <th colspan="6" class="px-2 py-2 text-center border border-blue-700 bg-blue-700">2-SEMESTR</th>
                            <th rowspan="2" class="px-2 py-3 text-center border border-blue-700 w-16">MA'RUZA JAMI</th>
                            <th rowspan="2" class="px-2 py-3 text-center border border-blue-700 w-16">AMALIY JAMI</th>
                            <th rowspan="2" class="px-2 py-3 text-center border border-blue-700 w-14">KURS ISHI</th>
                            <th rowspan="2" class="px-2 py-3 text-center border border-blue-700 w-10">BMI</th>
                            <th rowspan="2" class="px-2 py-3 text-center border border-blue-700 w-14">REYTING</th>
                            <th rowspan="2" class="px-2 py-3 text-center border border-blue-700 w-16 bg-blue-600">JAMI</th>
                            <th rowspan="2" class="px-2 py-3 text-center border border-blue-700 w-16">SHTAT JAMI</th>
                            <th rowspan="2" class="px-2 py-3 text-center border border-blue-700 w-12">SOATBAY</th>
                        </tr>
                        <tr class="bg-blue-800 text-white text-center">
                            <th class="px-1 py-2 border border-blue-700 w-10">Ma'r</th>
                            <th class="px-1 py-2 border border-blue-700 w-10">Aml</th>
                            <th class="px-1 py-2 border border-blue-700 w-10">Lab</th>
                            <th class="px-1 py-2 border border-blue-700 w-10">Sem</th>
                            <th class="px-1 py-2 border border-blue-700 w-10">Aml-t</th>
                            <th class="px-1 py-2 border border-blue-700 w-12 font-bold">Jami</th>
                            <th class="px-1 py-2 border border-blue-600 w-10">Ma'r</th>
                            <th class="px-1 py-2 border border-blue-600 w-10">Aml</th>
                            <th class="px-1 py-2 border border-blue-600 w-10">Lab</th>
                            <th class="px-1 py-2 border border-blue-600 w-10">Sem</th>
                            <th class="px-1 py-2 border border-blue-600 w-10">Aml-t</th>
                            <th class="px-1 py-2 border border-blue-600 w-12 font-bold">Jami</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                        <tr v-for="(row, i) in rows" :key="i"
                            class="hover:bg-gray-50"
                            :class="i % 2 === 0 ? 'bg-white' : 'bg-blue-50/20'">
                            <!-- O'qituvchi — faqat birinchi qatorda ko'rsatiladi -->
                            <td class="px-2 py-2 border-r border-gray-200 font-medium text-gray-900 align-top">
                                <span v-if="row.teacher_name" class="font-semibold">{{ row.teacher_name }}</span>
                                <span v-if="row.teacher_info" class="block text-gray-500 text-xs">{{ row.teacher_info }}</span>
                            </td>
                            <td class="px-2 py-2 border-r border-gray-200 text-gray-700">{{ row.subject }}</td>
                            <td class="px-2 py-2 border-r border-gray-200 text-gray-600">{{ row.direction }}</td>
                            <td class="px-2 py-2 border-r border-gray-200 text-gray-600 whitespace-pre-line">{{ row.groups }}</td>
                            <td class="px-1 py-2 text-center border-r border-gray-200">{{ row.course }}</td>
                            <td class="px-1 py-2 text-center border-r border-gray-200">{{ row.group_count }}</td>
                            <td class="px-1 py-2 text-center border-r border-gray-200">{{ row.students }}</td>
                            <td class="px-1 py-2 text-center border-r border-gray-200 text-purple-600 font-medium">{{ row.is_potok }}</td>
                            <!-- 1-semestr -->
                            <td class="px-1 py-2 text-center border-r border-gray-100">{{ row.s1_lecture }}</td>
                            <td class="px-1 py-2 text-center border-r border-gray-100">{{ row.s1_practical }}</td>
                            <td class="px-1 py-2 text-center border-r border-gray-100">{{ row.s1_laboratory }}</td>
                            <td class="px-1 py-2 text-center border-r border-gray-100">{{ row.s1_seminar }}</td>
                            <td class="px-1 py-2 text-center border-r border-gray-100">{{ row.s1_practice }}</td>
                            <td class="px-1 py-2 text-center border-r border-gray-200 font-medium">{{ row.s1_total }}</td>
                            <!-- 2-semestr -->
                            <td class="px-1 py-2 text-center border-r border-gray-100 bg-blue-50/30">{{ row.s2_lecture }}</td>
                            <td class="px-1 py-2 text-center border-r border-gray-100 bg-blue-50/30">{{ row.s2_practical }}</td>
                            <td class="px-1 py-2 text-center border-r border-gray-100 bg-blue-50/30">{{ row.s2_laboratory }}</td>
                            <td class="px-1 py-2 text-center border-r border-gray-100 bg-blue-50/30">{{ row.s2_seminar }}</td>
                            <td class="px-1 py-2 text-center border-r border-gray-100 bg-blue-50/30">{{ row.s2_practice }}</td>
                            <td class="px-1 py-2 text-center border-r border-gray-200 font-medium bg-blue-50/30">{{ row.s2_total }}</td>
                            <!-- Qo'shimcha -->
                            <td class="px-1 py-2 text-center border-r border-gray-200">{{ row.auditoria_total }}</td>
                            <td class="px-1 py-2 text-center border-r border-gray-200">{{ row.coursework }}</td>
                            <td class="px-1 py-2 text-center border-r border-gray-200">{{ row.diploma }}</td>
                            <td class="px-1 py-2 text-center border-r border-gray-200">{{ row.coursework }}</td>
                            <td class="px-1 py-2 text-center border-r border-gray-200">{{ row.rating }}</td>
                            <td class="px-1 py-2 text-center border-r border-gray-200 font-bold text-blue-700">{{ row.total }}</td>
                            <td class="px-1 py-2 text-center border-r border-gray-200"></td>
                            <td class="px-1 py-2 text-center text-gray-500 text-xs">{{ row.employment_type === 'hourly' ? 'soatbay' : '' }}</td>
                        </tr>

                        <tr v-if="!rows.length">
                            <td colspan="30" class="px-6 py-12 text-center text-gray-400">
                                Bu o'quv yili uchun tasdiqlangan yuklama topilmadi
                            </td>
                        </tr>
                        </tbody>
                        <tfoot v-if="rows.length">
                        <tr class="bg-yellow-50 font-bold text-sm">
                            <td colspan="25" class="px-3 py-3 text-right border-t-2 border-gray-300">JAMI:</td>
                            <td class="px-2 py-3 text-center border-t-2 border-gray-300 text-blue-700">{{ totals.total }}</td>
                            <td colspan="2" class="border-t-2 border-gray-300"></td>
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
    faculty:       Object,
    academicYear:  Object,
    academicYears: Array,
    rows:          Array,
    totals:        Object,
})

const selectedYear = ref(props.academicYear?.id)

const exportUrl = computed(() =>
    route('reports.export.faculty', props.faculty.id) + '?academic_year_id=' + selectedYear.value
)

function reload() {
    router.get(route('reports.faculty', props.faculty.id), {
        academic_year_id: selectedYear.value,
    }, { preserveScroll: true })
}
</script>
