<template>
    <div class="space-y-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Fan va soatlar</h3>
            <p class="text-sm text-gray-500 mt-1">
                Fan tanlansin — soatlar avtomatik to'ldiriladi. Kerak bo'lsa o'zgartiring.
            </p>
        </div>

        <!-- ─── Fan tanlash ──────────────────────────────────────────── -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Fan <span class="text-red-500">*</span>
            </label>

            <div class="relative mb-2">
                <input
                    v-model="subjectSearch"
                    type="text"
                    placeholder="Fan nomi yoki kodini kiriting..."
                    class="w-full rounded-lg border border-gray-300 text-sm px-3 py-2 pl-9 outline-none
                 focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition"
                />
                <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-4.35-4.35M17 11A6 6 0 111 11a6 6 0 0116 0z"/>
                </svg>
                <span v-if="selectedSubject" class="absolute right-3 top-2 text-xs text-blue-600 font-medium">
          ✓ {{ selectedSubject.name }}
        </span>
            </div>

            <div class="border border-gray-200 rounded-xl overflow-hidden">
                <div class="max-h-48 overflow-y-auto divide-y divide-gray-100">
                    <div v-if="filteredSubjects.length === 0"
                         class="p-4 text-sm text-gray-400 text-center">Fan topilmadi
                    </div>

                    <button
                        v-for="s in filteredSubjects"
                        :key="s.id"
                        type="button"
                        @click="selectSubject(s)"
                        :class="[
              'w-full flex items-start gap-3 px-4 py-3 text-left hover:bg-blue-50 transition-colors',
              local.subject_id === s.id ? 'bg-blue-50 border-l-4 border-blue-500' : ''
            ]"
                    >
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-gray-800 truncate">{{ s.name }}</div>
                            <div class="text-xs text-gray-400 mt-0.5 flex gap-3">
                                <span v-if="s.code">{{ s.code }}</span>
                                <span v-if="totalSubjectHours(s) > 0" class="text-indigo-500">
                  Jami: {{ totalSubjectHours(s) }} soat
                </span>
                                <span v-if="s.can_be_potok" class="text-purple-500">Potok mumkin</span>
                            </div>
                        </div>
                        <svg v-if="local.subject_id === s.id"
                             class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- ─── Soatlar ──────────────────────────────────────────────── -->
        <template v-if="local.subject_id && selectedSubject">
            <div v-if="isLoading" class="flex items-center gap-2 text-sm text-gray-500 py-2">
                <svg class="animate-spin w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
                Soatlar yuklanmoqda...
            </div>

            <template v-else>

                <!-- ── Guruh allaqachon to'liq taqsimlangan xabardori ──── -->
                <div v-if="fullyDistributedWarning"
                     class="p-4 bg-red-50 border border-red-300 rounded-xl">
                    <div class="flex items-start gap-3">
                        <span class="text-xl mt-0.5">🚫</span>
                        <div>
                            <p class="text-sm font-semibold text-red-700">
                                Bu guruh(lar)ga bu fan allaqachon to'liq taqsimlangan!
                            </p>
                            <p class="text-xs text-red-600 mt-1">
                                Tanlangan guruh(lar) uchun <strong>{{ selectedSubject.name }}</strong> fani
                                bo'yicha barcha soatlar boshqa o'qituvchiga berilgan.
                                Boshqa guruh tanlang yoki boshqa fan tanlang.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- ── Holat paneli ───────────────────────────────────── -->
                <div v-else class="rounded-xl border overflow-hidden" :class="statusBorderClass">
                    <div class="px-4 py-3 flex items-center justify-between" :class="statusBgClass">
                        <div class="flex items-center gap-2">
                            <span class="text-base">{{ statusIcon }}</span>
                            <span class="text-sm font-medium" :class="statusTextClass">{{ statusLabel }}</span>
                        </div>
                        <div class="text-xs font-semibold" :class="statusTextClass">
                            {{ enteredTotal }} / {{ subjectTotal }} soat
                            <span class="font-normal ml-1 opacity-70">({{ distributionPercent }}%)</span>
                        </div>
                    </div>
                    <div class="h-1.5 bg-gray-200">
                        <div
                            class="h-full transition-all duration-300"
                            :class="progressBarClass"
                            :style="{ width: Math.min(distributionPercent, 100) + '%' }"
                        />
                    </div>
                    <div class="px-4 py-2 flex gap-6 text-xs text-gray-500 bg-white">
                        <span>Fan soati: <strong class="text-gray-700">{{ subjectTotal }}</strong></span>
                        <span>Bu guruhga berilgan: <strong class="text-amber-600">{{
                                alreadyDistributed
                            }}</strong></span>
                        <span>Qolgan: <strong :class="remainingTotal > 0 ? 'text-green-600' : 'text-red-500'">
              {{ remainingTotal }}
            </strong></span>
                    </div>
                </div>

                <!-- ── Avtomatik to'ldirish ─────────────────────────── -->
                <div v-if="!fullyDistributedWarning && remainingTotal > 0 && enteredTotal < remainingTotal"
                     class="flex items-center gap-3">
                    <button
                        type="button"
                        @click="autoFillRemaining"
                        class="inline-flex items-center gap-2 text-sm font-medium text-blue-700
                   bg-blue-50 border border-blue-200 px-4 py-2 rounded-lg
                   hover:bg-blue-100 transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0
                       0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Qolgan soatlarni to'ldirish
                    </button>
                    <span class="text-xs text-gray-400">{{ remainingTotal }} soat qolgan</span>
                </div>

                <!-- ── Soat inputlari (faqat to'liq taqsimlanmagan bo'lsa) ─ -->
                <template v-if="!fullyDistributedWarning">

                    <!-- 1-semestr -->
                    <div v-if="hasSemester1Hours">
                        <div class="flex items-center gap-2 mb-3">
              <span class="w-6 h-6 bg-blue-600 text-white rounded-full flex items-center
                           justify-center text-xs font-bold">1</span>
                            <h4 class="text-sm font-semibold text-gray-700">1-semestr soatlari</h4>
                            <span class="text-xs text-gray-400">(Fan: {{ sem1SubjectTotal }} soat)</span>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <!-- 1-semestr -->
                            <HourField
                                v-for="f in semester1Fields"
                                :key="f.key"
                                :label="f.label"
                                v-model="local[f.key]"
                                :subject-max="subjectHours[f.key]"
                                :remaining="remainingHours[f.key]"
                                :disabled="(subjectHours[f.key] ?? 0) === 0 || (local.is_potok && !f.key.includes('lecture'))"
                            />
                        </div>
                    </div>

                    <!-- 2-semestr -->
                    <div v-if="hasSemester2Hours">
                        <div class="flex items-center gap-2 mb-3">
              <span class="w-6 h-6 bg-indigo-600 text-white rounded-full flex items-center
                           justify-center text-xs font-bold">2</span>
                            <h4 class="text-sm font-semibold text-gray-700">2-semestr soatlari</h4>
                            <span class="text-xs text-gray-400">(Fan: {{ sem2SubjectTotal }} soat)</span>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <!-- 1-semestr -->
                            <HourField
                                v-for="f in semester1Fields"
                                :key="f.key"
                                :label="f.label"
                                v-model="local[f.key]"
                                :subject-max="subjectHours[f.key]"
                                :remaining="remainingHours[f.key]"
                                :disabled="(subjectHours[f.key] ?? 0) === 0 || (local.is_potok && !f.key.includes('lecture'))"
                            />
                        </div>
                    </div>

                    <!-- Qo'shimcha -->
                    <div v-if="hasExtraHours">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Qo'shimcha soatlar</h4>
                        <div class="grid grid-cols-3 gap-3">
                            <!-- 1-semestr -->
                            <HourField
                                v-for="f in semester1Fields"
                                :key="f.key"
                                :label="f.label"
                                v-model="local[f.key]"
                                :subject-max="subjectHours[f.key]"
                                :remaining="remainingHours[f.key]"
                                :disabled="(subjectHours[f.key] ?? 0) === 0 || (local.is_potok && !f.key.includes('lecture'))"
                            />
                        </div>
                    </div>
                    <!-- ─── REYTING QISMI ────────────────────────────────────────────────── -->
                    <div class="rounded-xl border overflow-hidden" :class="ratingBorderClass">

                        <!-- Sarlavha -->
                        <div class="px-4 py-3 flex items-center justify-between" :class="ratingBgClass">
                            <div class="flex items-center gap-3">
                                <span class="text-lg">⭐</span>
                                <div>
                                    <p class="text-sm font-semibold" :class="ratingTextClass">Reyting</p>
                                    <p class="text-xs opacity-70 mt-0.5">
                                        {{ ratingStatus.total_students }} talaba ×½ =
                                        <strong>{{ ratingStatus.rating }}</strong> reyting
                                    </p>
                                </div>
                            </div>

                            <!-- Checkbox -->
                            <label
                                class="flex items-center gap-2 cursor-pointer select-none"
                                :class="{ 'opacity-50 cursor-not-allowed': ratingStatus.is_assigned }"
                            >
                                <input
                                    type="checkbox"
                                    v-model="local.has_rating"
                                    :disabled="!ratingStatus.can_assign || isLoadingRating"
                                    class="w-5 h-5 rounded text-blue-600 cursor-pointer disabled:cursor-not-allowed"
                                />
                                <span class="text-sm font-medium" :class="ratingTextClass">
                Reyting berish
            </span>
                            </label>
                        </div>

                        <!-- Allaqachon berilgan holat -->
                        <div v-if="ratingStatus.is_assigned" class="px-4 py-2 bg-red-50 border-t border-red-200">
                            <p class="text-xs text-red-600 flex items-center gap-1.5">
                                <span>🚫</span>
                                Bu guruh(lar) uchun reyting allaqachon
                                <strong>{{ ratingStatus.assigned_to }}</strong>
                                ga berilgan
                            </p>
                        </div>

                        <!-- Reyting beriladi holat -->
                        <div v-else-if="local.has_rating" class="px-4 py-2 bg-green-50 border-t border-green-200">
                            <p class="text-xs text-green-700 flex items-center gap-1.5">
                                <span>✅</span>
                                Ushbu o'qituvchiga
                                <strong>{{ ratingStatus.rating }}</strong>
                                reyting beriladi
                            </p>
                        </div>

                    </div>
                    <!-- Potok eslatma -->
                    <div v-if="local.is_potok"
                         class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-700">
                        ℹ️ Potokli holatda faqat <strong>ma'ruza soatlari</strong> kiritiladi.
                    </div>

                    <!-- Limitdan oshdi -->
                    <div v-if="hasOverLimit"
                         class="p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                        ⚠️ Bir yoki bir nechta soat turi limitidan oshib ketdi.
                    </div>

                </template>

            </template>
        </template>
    </div>
</template>

<script setup>
import {ref, computed, watch, onMounted} from 'vue'
import HourField from '@/Components/Workloads/HourField.vue'

// ─── Props ────────────────────────────────────────────────────────────────────
const props = defineProps({
    modelValue: {type: Object, required: true},
    subjects: {type: Array, default: () => []},
    groups: {type: Array, default: () => []},
    currentAcademicYear: {type: Object, default: null},
})
const emit = defineEmits(['update:modelValue', 'valid', 'subject-loaded'])

const local = computed({
    get: () => props.modelValue,
    set: v => emit('update:modelValue', v),
})

// ─── Holat ────────────────────────────────────────────────────────────────────
const subjectSearch = ref('')
const isLoading = ref(false)
const isFullyUsed = ref(false)
const subjectHours = ref({})
const remainingHours = ref({})

// ─── Reyting holati ───────────────────────────────────────────────────────────
const isLoadingRating = ref(false)
const ratingAssigned   = ref(false)
const ratingAssignedTo = ref(null)

// Talabalar soni va reyting — props.groups dan hisoblash
const ratingStatus = computed(() => {
    const groupIds = local.value.group_ids ?? []

    const totalStudents = groupIds.reduce((sum, id) => {
        const g = props.groups.find(g => g.id === id)
        return sum + (g?.student_count ?? 0)
    }, 0)

    return {
        total_students: totalStudents,
        rating:         Math.floor(totalStudents / 2),
        is_assigned:    ratingAssigned.value,
        assigned_to:    ratingAssignedTo.value,     // ← QO'SHING
        can_assign:     totalStudents > 0 && !ratingAssigned.value,
    }
})

// ─── Soat maydonlari ──────────────────────────────────────────────────────────
const semester1Fields = [
    {key: 'semester_1_lecture', label: 'Ma\'ruza'},
    {key: 'semester_1_practical', label: 'Amaliy'},
    {key: 'semester_1_laboratory', label: 'Laboratoriya'},
    {key: 'semester_1_seminar', label: 'Seminar'},
    {key: 'semester_1_practice', label: 'Amaliyot'},
    {key: 'semester_1_exam', label: 'Imtihon'},
    {key: 'semester_1_test', label: 'Sinov'},
]
const semester2Fields = [
    {key: 'semester_2_lecture', label: 'Ma\'ruza'},
    {key: 'semester_2_practical', label: 'Amaliy'},
    {key: 'semester_2_laboratory', label: 'Laboratoriya'},
    {key: 'semester_2_seminar', label: 'Seminar'},
    {key: 'semester_2_practice', label: 'Amaliyot'},
    {key: 'semester_2_exam', label: 'Imtihon'},
    {key: 'semester_2_test', label: 'Sinov'},
]
const extraFields = [
    {key: 'coursework_hours', label: 'Kurs ishi'},
    {key: 'diploma_hours', label: 'Diplom ishi'},
    {key: 'consultation_hours', label: 'Konsultatsiya'},
]
const allFields = [...semester1Fields, ...semester2Fields, ...extraFields]
function totalSubjectHours(s) {
    return allFields.reduce((sum, f) => sum + (Number(s[f.key]) || 0), 0)
}
// ─── Fan qidiruvi ─────────────────────────────────────────────────────────────
const filteredSubjects = computed(() => {
    const q = subjectSearch.value.toLowerCase()
    return props.subjects.filter(s =>
        s.name.toLowerCase().includes(q) || (s.code ?? '').toLowerCase().includes(q)
    )
})

const selectedSubject = computed(() =>
    props.subjects.find(s => s.id === local.value.subject_id)
)

// ─── onMounted ────────────────────────────────────────────────────────────────
onMounted(async () => {
    if (local.value.subject_id) {
        await loadLimitsOnly(local.value.subject_id)
        await checkRatingAssigned()
    }
})

// ─── Fan tanlash ─────────────────────────────────────────────────────────────
async function selectSubject(subject) {
    const isNew = local.value.subject_id !== subject.id
    local.value.subject_id = subject.id
    emit('subject-loaded', subject)

    if (isNew) {
        allFields.forEach(f => {
            local.value[f.key] = 0
        })
        local.value.has_rating = false
        await loadAndFill(subject.id)
    } else {
        await loadLimitsOnly(subject.id)
    }

    await checkRatingAssigned()
}

// ─── Server: bu guruhlar + fan uchun reyting berilganmi? ─────────────────────
async function checkRatingAssigned() {
    const subjectId = local.value.subject_id
    const groupIds = local.value.group_ids ?? []

    if (!subjectId || groupIds.length === 0) {
        ratingAssigned.value = false
        return
    }

    isLoadingRating.value = true
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? ''
        const params = new URLSearchParams()
        params.set('subject_id', subjectId)
        groupIds.forEach(id => params.append('group_ids[]', id))
        if (local.value.academic_year_id) {
            params.set('academic_year_id', local.value.academic_year_id)
        }

        const res = await fetch(`/workloads/ajax/rating-status?${params}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        })

        if (res.ok) {
            const json = await res.json()
            if (json.success) {
                ratingAssigned.value   = json.is_assigned ?? false
                ratingAssignedTo.value = json.assigned_to ?? null
                if (ratingAssigned.value) {
                    local.value.has_rating = false
                }
            }
        }
    } catch (_) {
        ratingAssigned.value = false
    } finally {
        isLoadingRating.value = false
    }
}

// ─── Yangi fan: yuklash + avtomatik to'ldirish ───────────────────────────────
async function loadAndFill(subjectId) {
    isLoading.value = true
    try {
        const data = await fetchSubjectDetails(subjectId)
        if (data) {
            subjectHours.value = data.max_hours
            remainingHours.value = data.remaining_hours
            isFullyUsed.value = data.is_fully_used ?? false
            if (!isFullyUsed.value) fillFromRemaining()
        }
    } finally {
        isLoading.value = false
    }
}

// ─── Orqaga qaytganda: faqat limitlar ────────────────────────────────────────
async function loadLimitsOnly(subjectId) {
    isLoading.value = true
    try {
        const data = await fetchSubjectDetails(subjectId)
        if (data) {
            subjectHours.value = data.max_hours
            remainingHours.value = data.remaining_hours
            isFullyUsed.value = data.is_fully_used ?? false
        }
    } finally {
        isLoading.value = false
    }
}

// ─── Server: soat limiti ──────────────────────────────────────────────────────
async function fetchSubjectDetails(subjectId) {
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? ''
        const params = new URLSearchParams()

        if (local.value.academic_year_id) {
            params.set('academic_year_id', local.value.academic_year_id)
        }
        const groupIds = local.value.group_ids ?? []
        groupIds.forEach(id => params.append('group_ids[]', id))

        const res = await fetch(`/workloads/ajax/subject/${subjectId}/details?${params}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        })

        if (res.ok) {
            const json = await res.json()
            if (json.success) return json
        }
    } catch (_) { /* network xatosi */
    }

    // Fallback
    const s = props.subjects.find(x => x.id === subjectId)
    if (s) {
        const maxH = {}
        const remH = {}
        allFields.forEach(f => {
            maxH[f.key] = Number(s[f.key]) || 0
            remH[f.key] = Number(s[f.key]) || 0
        })
        return {max_hours: maxH, remaining_hours: remH, is_fully_used: false}
    }
    return null
}

// ─── Soatlarni to'ldirish ─────────────────────────────────────────────────────
function fillFromRemaining() {
    allFields.forEach(f => {
        const rem = Number(remainingHours.value[f.key]) || 0
        local.value[f.key] = (local.value.is_potok && !f.key.includes('lecture')) ? 0 : rem
    })
}

function autoFillRemaining() {
    fillFromRemaining()
}

// ─── Hisob-kitoblar ──────────────────────────────────────────────────────────
const subjectTotal = computed(() =>
    allFields.reduce((s, f) => s + (Number(subjectHours.value[f.key]) || 0), 0)
)
const alreadyDistributed = computed(() =>
    allFields.reduce((s, f) => {
        const max = Number(subjectHours.value[f.key]) || 0
        const rem = Number(remainingHours.value[f.key]) || 0
        return s + Math.max(0, max - rem)
    }, 0)
)
const remainingTotal = computed(() =>
    allFields.reduce((s, f) => s + (Number(remainingHours.value[f.key]) || 0), 0)
)
const enteredTotal = computed(() =>
    allFields.reduce((s, f) => s + (Number(local.value[f.key]) || 0), 0)
)
const distributionPercent = computed(() =>
    subjectTotal.value > 0
        ? Math.round((enteredTotal.value / subjectTotal.value) * 100)
        : 0
)
const hasSemester1Hours = computed(() =>
    semester1Fields.some(f => (Number(subjectHours.value[f.key]) || 0) > 0)
)
const hasSemester2Hours = computed(() =>
    semester2Fields.some(f => (Number(subjectHours.value[f.key]) || 0) > 0)
)
const hasExtraHours = computed(() =>
    extraFields.some(f => (Number(subjectHours.value[f.key]) || 0) > 0)
)
const hasOverLimit = computed(() =>
    allFields.some(f => {
        const entered     = Number(local.value[f.key])           || 0
        const maxLimit    = Number(remainingHours.value[f.key])  || 0
        const subjectMax  = Number(subjectHours.value[f.key])    || 0
        if (subjectMax === 0) return false  // fan uchun bu soat yo'q
        return entered > maxLimit           // 0 dan oshsa ham xato
    })
)

// ─── Reyting stil computed ────────────────────────────────────────────────────
const ratingBorderClass = computed(() => {
    if (ratingStatus.value.is_assigned) return 'border-red-200'
    if (local.value.has_rating) return 'border-green-200'
    return 'border-gray-200'
})
const ratingBgClass = computed(() => {
    if (ratingStatus.value.is_assigned) return 'bg-red-50'
    if (local.value.has_rating) return 'bg-green-50'
    return 'bg-gray-50'
})
const ratingTextClass = computed(() => {
    if (ratingStatus.value.is_assigned) return 'text-red-700'
    if (local.value.has_rating) return 'text-green-700'
    return 'text-gray-700'
})

// ─── Status panel ─────────────────────────────────────────────────────────────
const statusLabel = computed(() => {
    if (hasOverLimit.value) return 'Limitdan oshib ketdi!'
    if (distributionPercent.value === 100) return 'To\'liq taqsimlandi'
    if (distributionPercent.value > 80) return 'Deyarli to\'liq'
    if (distributionPercent.value > 0) return 'Qisman taqsimlandi'
    return 'Soatlar kiritilmagan'
})
const statusIcon = computed(() => {
    if (hasOverLimit.value) return '❌'
    if (distributionPercent.value === 100) return '✅'
    if (distributionPercent.value > 0) return '⏳'
    return '📋'
})
const statusBgClass = computed(() => {
    if (hasOverLimit.value) return 'bg-red-50'
    if (distributionPercent.value === 100) return 'bg-green-50'
    return 'bg-blue-50'
})
const statusBorderClass = computed(() => {
    if (hasOverLimit.value) return 'border-red-200'
    if (distributionPercent.value === 100) return 'border-green-200'
    return 'border-blue-200'
})
const statusTextClass = computed(() => {
    if (hasOverLimit.value) return 'text-red-700'
    if (distributionPercent.value === 100) return 'text-green-700'
    return 'text-blue-700'
})
const progressBarClass = computed(() => {
    if (hasOverLimit.value) return 'bg-red-500'
    if (distributionPercent.value === 100) return 'bg-green-500'
    if (distributionPercent.value > 80) return 'bg-amber-400'
    return 'bg-blue-500'
})

// ─── Validatsiya ──────────────────────────────────────────────────────────────
watch(
    () => [
        local.value.subject_id,
        enteredTotal.value,
        hasOverLimit.value,
        isFullyUsed.value,
    ],
    () => {
        const ok = !!local.value.subject_id
            && enteredTotal.value > 0
            && !hasOverLimit.value
            && !isFullyUsed.value
        emit('valid', ok)
    },
    {immediate: true}
)
</script>
