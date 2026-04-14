<template>
    <AuthenticatedLayout>
        <template #header>Kafedra Hisoboti</template>

        <div class="max-w-7xl mx-auto">

            <!-- Boshqaruv paneli -->
            <div class="bg-white rounded-lg shadow-sm p-5 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">{{ department.name }}</h2>
                        <p class="text-sm text-gray-500">{{ academicYear?.name }} o'quv yili</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <select v-model="selectedYear" @change="reload"
                                class="px-6 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 outline-none">
                            <option v-for="y in academicYears" :key="y.id" :value="y.id">
                                {{ y.name }}
                            </option>
                        </select>
                        <a :href="exportUrl"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white
                                  rounded-lg hover:bg-green-700 transition-colors text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Excel
                        </a>
                        <Link :href="route('reports.index')"
                              class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200
                                     transition-colors text-sm">
                            ← Orqaga
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Sarlavha bloki -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6 p-5 text-center">
                <h3 class="text-base font-bold text-gray-900">{{ department.name }}</h3>
                <p class="text-sm text-gray-600 mt-1">
                    {{ academicYear?.name }} o'quv yili uchun Professor-o'qituvchilarning
                    <span class="font-semibold">O'QUV YUKLAMASI</span>
                </p>
                <p class="text-xs text-gray-400 mt-1">{{ today }} holati</p>
            </div>

            <!-- Jadval -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-xs border-collapse">
                        <thead>
                        <tr class="bg-blue-900 text-white">
                            <th class="px-2 py-3 text-center border border-blue-700 w-8">T/r</th>
                            <th class="px-3 py-3 text-left border border-blue-700 min-w-40">F.I.O</th>
                            <th class="px-2 py-3 text-center border border-blue-700">Shtat birligi</th>
                            <th class="px-2 py-3 text-center border border-blue-700">Stavka</th>
                            <th class="px-3 py-3 text-left border border-blue-700 min-w-28">Lavozimi</th>
                            <th class="px-2 py-3 text-center border border-blue-700">Darajasi</th>
                            <th class="px-2 py-3 text-center border border-blue-700">Auditoriya soati</th>
                            <th class="px-2 py-3 text-center border border-blue-700">Ma'ruza</th>
                            <th class="px-2 py-3 text-center border border-blue-700">Amaliy</th>
                            <th class="px-2 py-3 text-center border border-blue-700">Seminar</th>
                            <th class="px-2 py-3 text-center border border-blue-700">Amaliyot</th>
                            <th class="px-2 py-3 text-center border border-blue-700">Kurs ishi</th>
                            <th class="px-2 py-3 text-center border border-blue-700">Reyting</th>
                            <th class="px-2 py-3 text-center border border-blue-700">Umumiy yuklama</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                        <tr v-for="row in rows" :key="row.num"
                            class="hover:bg-gray-50"
                            :class="row.num % 2 === 0 ? 'bg-blue-50/30' : 'bg-white'">
                            <td class="px-2 py-2.5 text-center border-r border-gray-200 text-gray-500">{{ row.num }}</td>
                            <td class="px-3 py-2.5 font-medium text-gray-900 border-r border-gray-200">{{ row.name }}</td>
                            <td class="px-2 py-2.5 text-center text-gray-600 border-r border-gray-200">{{ row.employment_type }}</td>
                            <td class="px-2 py-2.5 text-center text-gray-600 border-r border-gray-200">{{ row.stavka }}</td>
                            <td class="px-3 py-2.5 text-gray-600 border-r border-gray-200">{{ row.position }}</td>
                            <td class="px-2 py-2.5 text-center text-gray-600 border-r border-gray-200">{{ row.degree }}</td>
                            <td class="px-2 py-2.5 text-center font-medium border-r border-gray-200">{{ row.auditoria || '—' }}</td>
                            <td class="px-2 py-2.5 text-center border-r border-gray-200">{{ row.lecture || '—' }}</td>
                            <td class="px-2 py-2.5 text-center border-r border-gray-200">{{ row.practical || '—' }}</td>
                            <td class="px-2 py-2.5 text-center border-r border-gray-200">{{ row.seminar || '—' }}</td>
                            <td class="px-2 py-2.5 text-center border-r border-gray-200">{{ row.practice || '—' }}</td>
                            <td class="px-2 py-2.5 text-center border-r border-gray-200">{{ row.coursework || '—' }}</td>
                            <td class="px-2 py-2.5 text-center border-r border-gray-200">{{ row.rating || '—' }}</td>
                            <td class="px-2 py-2.5 text-center font-bold text-blue-700">{{ row.total || '—' }}</td>
                        </tr>

                        <!-- Bo'sh holat -->
                        <tr v-if="!rows.length">
                            <td colspan="14" class="px-6 py-12 text-center text-gray-400">
                                Bu o'quv yili uchun tasdiqlangan yuklama topilmadi
                            </td>
                        </tr>
                        </tbody>
                        <!-- Jami -->
                        <tfoot v-if="rows.length">
                        <tr class="bg-yellow-50 font-bold text-sm">
                            <td colspan="6" class="px-3 py-3 text-right border-t-2 border-gray-300">Umumiy JAMI:</td>
                            <td class="px-2 py-3 text-center border-t-2 border-gray-300">{{ totals.auditoria }}</td>
                            <td class="px-2 py-3 text-center border-t-2 border-gray-300">{{ totals.lecture }}</td>
                            <td class="px-2 py-3 text-center border-t-2 border-gray-300">{{ totals.practical }}</td>
                            <td class="px-2 py-3 text-center border-t-2 border-gray-300">{{ totals.seminar }}</td>
                            <td class="px-2 py-3 text-center border-t-2 border-gray-300">{{ totals.practice }}</td>
                            <td class="px-2 py-3 text-center border-t-2 border-gray-300">{{ totals.coursework }}</td>
                            <td class="px-2 py-3 text-center border-t-2 border-gray-300">{{ totals.rating }}</td>
                            <td class="px-2 py-3 text-center border-t-2 border-gray-300 text-blue-700">{{ totals.total }}</td>
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
    department:    Object,
    academicYear:  Object,
    academicYears: Array,
    rows:          Array,
    totals:        Object,
})

const selectedYear = ref(props.academicYear?.id)
const today = new Date().toLocaleDateString('uz-UZ', { day:'2-digit', month:'2-digit', year:'numeric' })

const exportUrl = computed(() =>
    route('reports.export.department', props.department.id) + '?academic_year_id=' + selectedYear.value
)

function reload() {
    router.get(route('reports.department', props.department.id), {
        academic_year_id: selectedYear.value,
    }, { preserveScroll: true })
}
</script>
