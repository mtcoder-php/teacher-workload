<template>
    <Head title="Yuklamalar" />
    <AuthenticatedLayout>
        <template #header>
            Yuklamalar Boshqaruvi
        </template>

        <div class="max-w-7xl mx-auto">

            <!-- Flash xabarlari -->
            <div v-if="$page.props.flash?.success"
                 class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ $page.props.flash.success }}
            </div>
            <div v-if="$page.props.flash?.error"
                 class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ $page.props.flash.error }}
            </div>

            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Yuklamalar</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Jami {{ workloads.total || 0 }} ta yuklama
                        </p>
                    </div>
                    <Link :href="route('workloads.create')"
                          class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white
                                 rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Yangi yuklama
                    </Link>
                </div>
            </div>

            <!-- Filterlar -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Qidiruv</label>
                        <input v-model="filters.search" @input="search" type="text"
                               placeholder="Fan, o'qituvchi..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                                      focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none"/>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kafedra</label>
                        <select v-model="filters.department_id" @change="search"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                                       focus:ring-2 focus:ring-indigo-500 outline-none">
                            <option value="">Barcha kafedralar</option>
                            <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Holat</label>
                        <select v-model="filters.status" @change="search"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                                       focus:ring-2 focus:ring-indigo-500 outline-none">
                            <option value="">Barcha holatlar</option>
                            <option value="draft">Qoralama</option>
                            <option value="pending">Tekshiruvda</option>
                            <option value="confirmed">Tasdiqlangan</option>
                            <option value="completed">Tugatilgan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Turi</label>
                        <div class="flex gap-2">
                            <select v-model="filters.is_potok" @change="search"
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm
                                           focus:ring-2 focus:ring-indigo-500 outline-none">
                                <option value="">Hammasi</option>
                                <option value="1">Potok</option>
                                <option value="0">Oddiy</option>
                            </select>
                            <button @click="resetFilters"
                                    class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg
                                           hover:bg-gray-200 transition-colors text-sm">
                                Tozalash
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bulk action panel -->
            <div v-if="canBulk && selectedIds.length > 0"
                 class="mb-4 p-3 bg-indigo-50 border border-indigo-200 rounded-lg
                        flex items-center justify-between gap-3">
                <span class="text-sm font-medium text-indigo-700">
                    {{ selectedIds.length }} ta yuklama tanlandi
                </span>
                <div class="flex items-center gap-2">
                    <button v-if="canSubmitBulk" @click="bulkAction('submit')" :disabled="isBulking"
                            class="px-4 py-1.5 text-sm font-medium text-amber-700 bg-amber-50
                                   border border-amber-300 rounded-lg hover:bg-amber-100
                                   disabled:opacity-50 transition-colors">
                        → Tekshiruvga yuborish
                    </button>
                    <button v-if="canApproveBulk" @click="bulkAction('approve')" :disabled="isBulking"
                            class="px-4 py-1.5 text-sm font-medium text-green-700 bg-green-50
                                   border border-green-300 rounded-lg hover:bg-green-100
                                   disabled:opacity-50 transition-colors">
                        ✓ Tasdiqlash
                    </button>
                    <button v-if="canRejectBulk" @click="bulkAction('reject')" :disabled="isBulking"
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
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th v-if="canBulk" class="w-10 px-6 py-3">
                                <input type="checkbox" :checked="allSelected" @change="toggleAll"
                                       class="w-4 h-4 rounded border-gray-300 text-indigo-600 cursor-pointer"/>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fan / O'qituvchi
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
                        <tr v-for="workload in workloads.data" :key="workload.id"
                            class="hover:bg-gray-50 transition-colors"
                            :class="{ 'bg-indigo-50': selectedIds.includes(workload.id) }">

                            <td v-if="canBulk" class="px-6 py-4">
                                <input type="checkbox"
                                       :checked="selectedIds.includes(workload.id)"
                                       @change="toggleOne(workload.id)"
                                       class="w-4 h-4 rounded border-gray-300 text-indigo-600 cursor-pointer"/>
                            </td>

                            <!-- Fan / O'qituvchi -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center
                                                    justify-center text-indigo-600 font-semibold text-sm flex-shrink-0">
                                        {{ workload.subject?.name?.charAt(0)?.toUpperCase() ?? '?' }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ workload.subject?.name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ workload.teacher?.user?.name }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Guruhlar -->
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                        <span v-for="group in workload.groups?.slice(0, 3)" :key="group.id"
                                              class="px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-700">
                                            {{ group.name }}
                                        </span>
                                    <span v-if="workload.groups?.length > 3"
                                          class="px-2 py-0.5 text-xs font-medium rounded-full bg-gray-200 text-gray-600">
                                            +{{ workload.groups.length - 3 }}
                                        </span>
                                </div>
                            </td>

                            <!-- Soatlar -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ workload.total_hours || 0 }} soat</div>
                                <div class="text-sm text-gray-500">{{ workload.total_students || 0 }} talaba</div>
                            </td>

                            <!-- Turi -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full" :class="getTypeBadge(workload)">
                                        {{ getTypeLabel(workload) }}
                                    </span>
                            </td>

                            <!-- Holat -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full" :class="getStatusBadge(workload.status)">
                                        {{ getStatusLabel(workload.status) }}
                                    </span>
                            </td>

                            <!-- Amallar -->
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <Link :href="route('workloads.show', workload.id)"
                                          class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                          title="Ko'rish">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </Link>
                                    <Link v-if="workload.status === 'draft'"
                                          :href="route('workloads.edit', workload.id)"
                                          class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                          title="Tahrirlash">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </Link>
                                    <button v-if="workload.status === 'draft'"
                                            @click="deleteWorkload(workload.id)"
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            title="O'chirish">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Bo'sh holat -->
                        <tr v-if="!workloads.data?.length">
                            <td :colspan="canBulk ? 7 : 6" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-16 w-16 text-gray-300 mb-4"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-gray-500 text-sm">Yuklama topilmadi</p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="workloads.data?.length" class="px-6 py-4 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-sm text-gray-600">
                            {{ workloads.from }}-{{ workloads.to }} / {{ workloads.total }} ta natija
                        </p>
                        <div class="flex gap-1">
                            <template v-for="(link, i) in workloads.links" :key="i">
                                <Link v-if="link.url" :href="link.url"
                                      :class="[
                                          'px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                                          link.active
                                              ? 'bg-indigo-600 text-white'
                                              : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                      ]"
                                      v-html="link.label"/>
                                <span v-else
                                      class="px-3 py-2 rounded-lg text-sm font-medium bg-gray-50 text-gray-400 cursor-not-allowed"
                                      v-html="link.label"/>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import {Head, Link, router, usePage} from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const route = window.route
const page  = usePage()

const props = defineProps({
    workloads:    Object,
    filters:      Object,
    departments:  Array,
    teachers:     Array,
    subjects:     Array,
    academicYears:Array,
    directions:   Array,
})

// ─── Rol ─────────────────────────────────────────────────────────────────────
const isAdmin    = computed(() => page.props.auth?.user?.is_admin     ?? false)
const isDeptHead = computed(() => page.props.auth?.user?.is_dept_head ?? false)
const canBulk    = computed(() => isAdmin.value || isDeptHead.value)

// ─── Filterlar ────────────────────────────────────────────────────────────────
const filters = ref({
    search:          props.filters?.search           || '',
    department_id:   props.filters?.department_id    || '',
    teacher_id:      props.filters?.teacher_id       || '',
    subject_id:      props.filters?.subject_id       || '',
    academic_year_id:props.filters?.academic_year_id || '',
    direction_id:    props.filters?.direction_id     || '',
    status:          props.filters?.status           || '',
    is_potok:        props.filters?.is_potok         || '',
})

const search = () => {
    router.get(route('workloads.index'), filters.value, {
        preserveState: true,
        preserveScroll: true,
    })
}

const resetFilters = () => {
    Object.keys(filters.value).forEach(k => filters.value[k] = '')
    search()
}

// ─── Bulk action ──────────────────────────────────────────────────────────────
const selectedIds = ref([])
const isBulking   = ref(false)

const allPageIds = computed(() => (props.workloads?.data ?? []).map(w => w.id))
const allSelected = computed(() =>
    allPageIds.value.length > 0 &&
    allPageIds.value.every(id => selectedIds.value.includes(id))
)
const selectedWorkloads = computed(() =>
    (props.workloads?.data ?? []).filter(w => selectedIds.value.includes(w.id))
)
const canSubmitBulk  = computed(() => selectedWorkloads.value.some(w => w.status === 'draft'))
const canApproveBulk = computed(() => isAdmin.value && selectedWorkloads.value.some(w => w.status === 'pending'))
const canRejectBulk  = computed(() => isAdmin.value && selectedWorkloads.value.some(w => w.status === 'pending'))

function toggleAll() {
    selectedIds.value = allSelected.value ? [] : [...allPageIds.value]
}
function toggleOne(id) {
    selectedIds.value = selectedIds.value.includes(id)
        ? selectedIds.value.filter(x => x !== id)
        : [...selectedIds.value, id]
}
function bulkAction(action) {
    if (!selectedIds.value.length) return
    const labels = { submit: 'tekshiruvga yuborish', approve: 'tasdiqlash', reject: 'qaytarish' }
    if (!confirm(`${selectedIds.value.length} ta yuklamani ${labels[action]}ni xohlaysizmi?`)) return
    isBulking.value = true
    router.post(route('workloads.bulk-action'), { ids: selectedIds.value, action }, {
        preserveScroll: true,
        onSuccess: () => { selectedIds.value = [] },
        onFinish:  () => { isBulking.value = false },
    })
}

// ─── Delete ───────────────────────────────────────────────────────────────────
function deleteWorkload(id) {
    if (confirm("Rostdan ham o'chirmoqchimisiz?")) {
        router.delete(route('workloads.destroy', id), { preserveScroll: true })
    }
}

// ─── Badge ────────────────────────────────────────────────────────────────────
function getStatusBadge(status) {
    return { draft:'bg-gray-100 text-gray-700', pending:'bg-yellow-100 text-yellow-800',
        confirmed:'bg-green-100 text-green-800', completed:'bg-blue-100 text-blue-800' }[status] ?? 'bg-gray-100 text-gray-700'
}
function getStatusLabel(status) {
    return { draft:'Qoralama', pending:'Tekshiruvda', confirmed:'Tasdiqlangan', completed:'Tugatilgan' }[status] ?? status
}
function getTypeBadge(w) {
    if (w.is_potok)           return 'bg-purple-100 text-purple-800'
    if (w.is_potok_remainder) return 'bg-orange-100 text-orange-800'
    return 'bg-indigo-100 text-indigo-800'
}
function getTypeLabel(w) {
    if (w.is_potok)           return '📚 Potok'
    if (w.is_potok_remainder) return "📝 Qoldig'i"
    return '📖 Oddiy'
}
</script>
