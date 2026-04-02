<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const route = window.route
const page  = usePage()

const props = defineProps({
    workloads:          Object,
    filters:            Object,
    departments:        Array,
    teachers:           Array,
    subjects:           Array,
    academicYears:      Array,
    directions:         Array,
    currentAcademicYear:Object,
})

// ─── Foydalanuvchi roli ───────────────────────────────────────────────────────
const isAdmin    = computed(() => page.props.auth?.user?.is_admin    ?? false)
const isDeptHead = computed(() => page.props.auth?.user?.is_dept_head ?? false)
const canBulk    = computed(() => isAdmin.value || isDeptHead.value)

// ─── Filterlar ────────────────────────────────────────────────────────────────
const filters = ref({
    search:          props.filters?.search          || '',
    department_id:   props.filters?.department_id   || '',
    teacher_id:      props.filters?.teacher_id      || '',
    subject_id:      props.filters?.subject_id      || '',
    academic_year_id:props.filters?.academic_year_id|| '',
    direction_id:    props.filters?.direction_id    || '',
    status:          props.filters?.status          || '',
    is_potok:        props.filters?.is_potok        || '',
})

const search = () => {
    router.get(route('workloads.index'), filters.value, {
        preserveState: true,
        preserveScroll: true,
    })
}

const resetFilters = () => {
    filters.value = {
        search: '', department_id: '', teacher_id: '',
        subject_id: '', academic_year_id: '', direction_id: '',
        status: '', is_potok: '',
    }
    search()
}

// ─── Bulk action ──────────────────────────────────────────────────────────────
const selectedIds  = ref([])
const isBulking    = ref(false)

const allPageIds = computed(() =>
    (props.workloads?.data ?? []).map(w => w.id)
)

const allSelected = computed(() =>
    allPageIds.value.length > 0 &&
    allPageIds.value.every(id => selectedIds.value.includes(id))
)

function toggleAll() {
    if (allSelected.value) {
        selectedIds.value = []
    } else {
        selectedIds.value = [...allPageIds.value]
    }
}

function toggleOne(id) {
    if (selectedIds.value.includes(id)) {
        selectedIds.value = selectedIds.value.filter(x => x !== id)
    } else {
        selectedIds.value.push(id)
    }
}

// Tanlanganlar uchun mumkin bo'lgan amallar
const selectedWorkloads = computed(() =>
    (props.workloads?.data ?? []).filter(w => selectedIds.value.includes(w.id))
)
const canSubmitBulk = computed(() =>
    selectedWorkloads.value.some(w => w.status === 'draft')
)
const canApproveBulk = computed(() =>
    isAdmin.value && selectedWorkloads.value.some(w => w.status === 'pending')
)
const canRejectBulk = computed(() =>
    isAdmin.value && selectedWorkloads.value.some(w => w.status === 'pending')
)

function bulkAction(action) {
    if (selectedIds.value.length === 0) return

    const labels = {
        submit:  'tekshiruvga yuborish',
        approve: 'tasdiqlash',
        reject:  'qaytarish',
    }

    if (!confirm(`${selectedIds.value.length} ta yuklamani ${labels[action]}ni xohlaysizmi?`)) return

    isBulking.value = true
    router.post(route('workloads.bulk-action'), {
        ids:    selectedIds.value,
        action: action,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            selectedIds.value = []
        },
        onFinish: () => {
            isBulking.value = false
        },
    })
}

// ─── Amallar ──────────────────────────────────────────────────────────────────
const deleteWorkload = (id) => {
    if (confirm("Rostdan ham o'chirmoqchimisiz?")) {
        router.delete(route('workloads.destroy', id), { preserveScroll: true })
    }
}

// ─── Badge yordamchi funksiyalar ──────────────────────────────────────────────
const getStatusBadge = (status) => ({
    draft:     'bg-gray-100 text-gray-800',
    pending:   'bg-yellow-100 text-yellow-800',
    confirmed: 'bg-green-100 text-green-800',
    completed: 'bg-blue-100 text-blue-800',
}[status] ?? 'bg-gray-100 text-gray-800')

const getStatusLabel = (status) => ({
    draft:     'Qoralama',
    pending:   'Tekshiruvda',
    confirmed: 'Tasdiqlangan',
    completed: 'Tugatilgan',
}[status] ?? status)

const getWorkloadTypeBadge = (w) =>
    w.is_potok ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'

const getWorkloadTypeLabel = (w) => {
    if (w.is_potok)           return `📚 Potok: ${w.potok_code}`
    if (w.is_potok_remainder) return "📝 Potok qoldig'i"
    return '📖 Oddiy'
}
</script>

<template>
    <AuthenticatedLayout title="Yuklamalar">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800">Yuklamalar</h2>
                <Link :href="route('workloads.create')"
                      class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg
                             hover:bg-blue-700 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Yangi yuklama
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

                <!-- Flash xabarlari -->
                <div v-if="$page.props.flash?.success"
                     class="p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm flex items-center gap-2">
                    ✅ {{ $page.props.flash.success }}
                </div>
                <div v-if="$page.props.flash?.error"
                     class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm flex items-center gap-2">
                    ⚠️ {{ $page.props.flash.error }}
                </div>

                <!-- Filterlar -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7 gap-3">
                        <input v-model="filters.search" @input="search" type="text"
                               placeholder="Qidirish..."
                               class="rounded-lg border border-gray-300 text-sm px-3 py-2 outline-none
                                      focus:ring-2 focus:ring-blue-200 focus:border-blue-400"/>

                        <select v-model="filters.department_id" @change="search"
                                class="rounded-lg border border-gray-300 text-sm px-3 py-2 outline-none
                                       focus:ring-2 focus:ring-blue-200">
                            <option value="">Barcha kafedralar</option>
                            <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
                        </select>

                        <select v-model="filters.teacher_id" @change="search"
                                class="rounded-lg border border-gray-300 text-sm px-3 py-2 outline-none
                                       focus:ring-2 focus:ring-blue-200">
                            <option value="">Barcha o'qituvchilar</option>
                            <option v-for="t in teachers" :key="t.id" :value="t.id">{{ t.name }}</option>
                        </select>

                        <select v-model="filters.academic_year_id" @change="search"
                                class="rounded-lg border border-gray-300 text-sm px-3 py-2 outline-none
                                       focus:ring-2 focus:ring-blue-200">
                            <option value="">Barcha yillar</option>
                            <option v-for="y in academicYears" :key="y.id" :value="y.id">{{ y.name }}</option>
                        </select>

                        <select v-model="filters.status" @change="search"
                                class="rounded-lg border border-gray-300 text-sm px-3 py-2 outline-none
                                       focus:ring-2 focus:ring-blue-200">
                            <option value="">Barcha holatlar</option>
                            <option value="draft">Qoralama</option>
                            <option value="pending">Tekshiruvda</option>
                            <option value="confirmed">Tasdiqlangan</option>
                            <option value="completed">Tugatilgan</option>
                        </select>

                        <select v-model="filters.is_potok" @change="search"
                                class="rounded-lg border border-gray-300 text-sm px-3 py-2 outline-none
                                       focus:ring-2 focus:ring-blue-200">
                            <option value="">Hammasi</option>
                            <option value="1">Potok</option>
                            <option value="0">Oddiy</option>
                        </select>

                        <button @click="resetFilters"
                                class="rounded-lg border border-gray-300 text-sm px-3 py-2 text-gray-600
                                       hover:bg-gray-50 transition-colors">
                            Tozalash
                        </button>
                    </div>
                </div>

                <!-- Bulk action panel -->
                <div v-if="canBulk && selectedIds.length > 0"
                     class="bg-blue-50 border border-blue-200 rounded-xl p-3 flex items-center justify-between gap-3">
                    <span class="text-sm font-medium text-blue-700">
                        {{ selectedIds.length }} ta yuklama tanlandi
                    </span>
                    <div class="flex items-center gap-2">
                        <!-- Tekshiruvga yuborish — draft lar uchun -->
                        <button v-if="canSubmitBulk"
                                @click="bulkAction('submit')"
                                :disabled="isBulking"
                                class="px-4 py-1.5 text-sm font-medium text-amber-700 bg-amber-50
                                       border border-amber-300 rounded-lg hover:bg-amber-100
                                       disabled:opacity-50 transition-colors">
                            → Tekshiruvga yuborish
                        </button>
                        <!-- Tasdiqlash — faqat admin, pending lar uchun -->
                        <button v-if="canApproveBulk"
                                @click="bulkAction('approve')"
                                :disabled="isBulking"
                                class="px-4 py-1.5 text-sm font-medium text-green-700 bg-green-50
                                       border border-green-300 rounded-lg hover:bg-green-100
                                       disabled:opacity-50 transition-colors">
                            ✓ Tasdiqlash
                        </button>
                        <!-- Qaytarish — faqat admin, pending lar uchun -->
                        <button v-if="canRejectBulk"
                                @click="bulkAction('reject')"
                                :disabled="isBulking"
                                class="px-4 py-1.5 text-sm font-medium text-red-700 bg-red-50
                                       border border-red-300 rounded-lg hover:bg-red-100
                                       disabled:opacity-50 transition-colors">
                            ← Qaytarish
                        </button>
                        <button @click="selectedIds = []"
                                class="px-3 py-1.5 text-sm text-gray-500 hover:text-gray-700">
                            Bekor
                        </button>
                    </div>
                </div>

                <!-- Jadval -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <!-- Checkbox ustun -->
                                <th v-if="canBulk" class="w-10 px-4 py-3">
                                    <input type="checkbox"
                                           :checked="allSelected"
                                           @change="toggleAll"
                                           class="w-4 h-4 rounded border-gray-300 text-blue-600 cursor-pointer"/>
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">O'qituvchi</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Guruhlar</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Soatlar</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Turi</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Holat</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amallar</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                            <tr v-for="(workload, index) in workloads.data" :key="workload.id"
                                class="hover:bg-gray-50 transition-colors"
                                :class="{ 'bg-blue-50': selectedIds.includes(workload.id) }">

                                <!-- Checkbox -->
                                <td v-if="canBulk" class="px-4 py-3">
                                    <input type="checkbox"
                                           :checked="selectedIds.includes(workload.id)"
                                           @change="toggleOne(workload.id)"
                                           class="w-4 h-4 rounded border-gray-300 text-blue-600 cursor-pointer"/>
                                </td>

                                <td class="px-4 py-3 text-sm text-gray-500">{{ index + 1 }}</td>

                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-900">{{ workload.subject?.name }}</div>
                                    <div class="text-xs text-gray-400">{{ workload.subject?.code }}</div>
                                </td>

                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-900">{{ workload.teacher?.user?.name }}</div>
                                    <div class="text-xs text-gray-400">{{ workload.department?.name }}</div>
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                            <span v-for="group in workload.groups?.slice(0, 3)" :key="group.id"
                                                  class="inline-flex items-center px-2 py-0.5 rounded text-xs
                                                         font-medium bg-gray-100 text-gray-800">
                                                {{ group.name }}
                                            </span>
                                        <span v-if="workload.groups?.length > 3"
                                              class="inline-flex items-center px-2 py-0.5 rounded text-xs
                                                         font-medium bg-gray-200 text-gray-600">
                                                +{{ workload.groups.length - 3 }}
                                            </span>
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ workload.total_hours || 0 }} soat
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        {{ workload.total_students || 0 }} talaba
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                        <span :class="getWorkloadTypeBadge(workload)"
                                              class="inline-flex items-center px-2.5 py-0.5 rounded-full
                                                     text-xs font-medium">
                                            {{ getWorkloadTypeLabel(workload) }}
                                        </span>
                                </td>

                                <td class="px-4 py-3">
                                        <span :class="getStatusBadge(workload.status)"
                                              class="inline-flex items-center px-2.5 py-0.5 rounded-full
                                                     text-xs font-medium">
                                            {{ getStatusLabel(workload.status) }}
                                        </span>
                                </td>

                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-2">
                                        <Link :href="route('workloads.show', workload.id)"
                                              class="text-blue-600 hover:text-blue-800" title="Ko'rish">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </Link>
                                        <Link v-if="workload.status === 'draft'"
                                              :href="route('workloads.edit', workload.id)"
                                              class="text-green-600 hover:text-green-800" title="Tahrirlash">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </Link>
                                        <button v-if="workload.status === 'draft'"
                                                @click="deleteWorkload(workload.id)"
                                                class="text-red-600 hover:text-red-800" title="O'chirish">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <!-- Bo'sh holat -->
                        <div v-if="!workloads.data?.length" class="text-center py-16">
                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">Yuklama topilmadi</p>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="workloads.data?.length && workloads.links?.length > 3"
                         class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
                        <p class="text-sm text-gray-600">
                            Jami <span class="font-medium">{{ workloads.total }}</span> ta,
                            ko'rsatilmoqda {{ workloads.from }}–{{ workloads.to }}
                        </p>
                        <div class="flex gap-1">
                            <template v-for="(link, i) in workloads.links" :key="i">
                                <Link v-if="link.url" :href="link.url"
                                      :class="[
                                          'px-3 py-1.5 text-sm rounded-lg border transition-colors',
                                          link.active
                                              ? 'bg-blue-600 text-white border-blue-600'
                                              : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50'
                                      ]"
                                      v-html="link.label"/>
                                <span v-else
                                      class="px-3 py-1.5 text-sm rounded-lg border border-gray-200
                                             text-gray-400 bg-gray-50"
                                      v-html="link.label"/>
                            </template>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
