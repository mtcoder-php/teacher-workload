<template>
    <AuthenticatedLayout title="Yuklamani tahrirlash">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800">Yuklamani tahrirlash</h2>
                <Link :href="route('workloads.show', workload.id)"
                      class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                    ← Orqaga
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

                <!-- Server xatosi -->
                <div v-if="$page.props.errors?.error"
                     class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
                    ⚠️ {{ $page.props.errors.error }}
                </div>

                <!-- Yuklama info -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">📋 Yuklama ma'lumotlari</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Fan:</span>
                            <p class="font-medium text-gray-800">{{ workload.subject?.name }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Guruhlar:</span>
                            <p class="font-medium text-gray-800">
                                {{ workload.groups?.map(g => g.name).join(', ') || '—' }}
                            </p>
                        </div>
                        <div>
                            <span class="text-gray-500">Tur:</span>
                            <p class="font-medium">
                                <span v-if="workload.is_potok"
                                      class="text-purple-600 bg-purple-50 px-2 py-0.5 rounded-full text-xs">
                                    Potokli
                                </span>
                                <span v-else class="text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full text-xs">
                                    Potoksiz
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Potok ogohlantirish -->
                    <div v-if="workload.is_potok"
                         class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-700">
                        ℹ️ Potokli yuklama — faqat <strong>ma'ruza soatlari</strong> ko'rsatiladi.
                        Ma'ruza soatlarini <strong>kamaytirish mumkin emas</strong>, oshirish esa limit doirasida mumkin.
                    </div>
                </div>

                <!-- O'qituvchi -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">👨‍🏫 O'qituvchi</h3>

                    <div class="relative mb-2">
                        <input v-model="teacherSearch" type="text"
                               placeholder="O'qituvchi qidirish..."
                               class="w-full rounded-lg border border-gray-300 text-sm px-3 py-2 pl-9
                                      focus:ring-2 focus:ring-blue-300 focus:border-blue-400 outline-none" />
                        <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-4.35-4.35M17 11A6 6 0 111 11a6 6 0 0116 0z"/>
                        </svg>
                    </div>

                    <div class="border border-gray-200 rounded-xl max-h-52 overflow-y-auto divide-y divide-gray-100 mb-3">
                        <div v-if="filteredTeachers.length === 0"
                             class="p-3 text-sm text-gray-400 text-center">
                            O'qituvchi topilmadi
                        </div>
                        <button v-for="t in filteredTeachers" :key="t.id"
                                type="button"
                                @click="form.teacher_id = t.id"
                                :class="[
                                    'w-full flex items-center gap-3 px-4 py-2.5 text-left hover:bg-blue-50 transition-colors',
                                    form.teacher_id === t.id ? 'bg-blue-50 border-l-2 border-blue-500' : ''
                                ]">
                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center
                                        justify-center text-xs font-bold flex-shrink-0">
                                {{ t.name?.charAt(0) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ t.name }}</p>
                                <p class="text-xs text-gray-500">{{ t.position }}</p>
                            </div>
                            <svg v-if="form.teacher_id === t.id"
                                 class="w-4 h-4 text-blue-500 ml-auto"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </button>
                    </div>
                    <p v-if="form.errors.teacher_id" class="text-xs text-red-500">
                        {{ form.errors.teacher_id }}
                    </p>
                </div>

                <!-- Soatlar -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-700">⏱️ Soatlar</h3>
                        <div v-if="isLoadingHours" class="text-xs text-gray-400 flex items-center gap-1">
                            <svg class="animate-spin w-3 h-3" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor"
                                      d="M4 12a8 8 0 018-8v8z"/>
                            </svg>
                            Yuklanmoqda...
                        </div>
                    </div>

                    <!-- Progress -->
                    <div class="mb-5 p-3 bg-gray-50 rounded-xl border border-gray-200">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs text-gray-600">Taqsimlash holati</span>
                            <span class="text-xs text-gray-500">
                                {{ enteredTotal }} / {{ maxTotal }} soat
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full transition-all"
                                 :class="progressBarClass"
                                 :style="{ width: Math.min(distributionPercent, 100) + '%' }" />
                        </div>
                        <p v-if="hasOverLimit" class="text-xs text-red-500 mt-1">
                            ⚠️ Limitdan oshib ketdi!
                        </p>
                        <p v-else-if="hasBelowOriginal" class="text-xs text-red-500 mt-1">
                            ⚠️ Potok ma'ruza soatlarini kamaytirish mumkin emas!
                        </p>
                    </div>

                    <!-- 1-semestr -->
                    <div v-if="hasSemester1Hours" class="mb-5">
                        <h4 class="text-xs font-semibold text-gray-600 mb-3 flex items-center gap-2">
                            <span class="w-5 h-5 bg-blue-100 text-blue-600 rounded-full flex items-center
                                         justify-center text-xs font-bold">1</span>
                            1-semestr soatlari
                        </h4>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div v-for="f in semester1Fields" :key="f.key">
                                <label class="block text-xs text-gray-500 mb-1">
                                    {{ f.label }}
                                    <span v-if="maxHours[f.key] > 0"
                                          class="text-gray-400">(max: {{ remainingHours[f.key] ?? maxHours[f.key] }})</span>
                                </label>
                                <input
                                    v-model.number="form[f.key]"
                                    type="number" min="0" step="0.5"
                                    :disabled="isFieldDisabled(f.key)"
                                    :class="[
                                        'w-full text-sm px-3 py-2 rounded-lg border transition outline-none',
                                        isFieldDisabled(f.key)
                                            ? 'bg-gray-100 text-gray-400 cursor-not-allowed border-gray-200'
                                            : isOverLimit(f.key)
                                                ? 'border-red-400 bg-red-50 text-red-700 focus:ring-2 focus:ring-red-200'
                                                : isBelowOriginal(f.key)
                                                    ? 'border-red-400 bg-red-50 text-red-700 focus:ring-2 focus:ring-red-200'
                                                    : 'border-gray-300 bg-white text-gray-800 focus:ring-2 focus:ring-blue-200 focus:border-blue-400'
                                    ]"
                                />
                                <p v-if="isOverLimit(f.key)"
                                   class="text-xs text-red-500 mt-0.5">
                                    Limit: {{ remainingHours[f.key] }} soat
                                </p>
                                <p v-else-if="isBelowOriginal(f.key)"
                                   class="text-xs text-red-500 mt-0.5">
                                    Kamaytirish mumkin emas
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- 2-semestr -->
                    <div v-if="hasSemester2Hours" class="mb-5">
                        <h4 class="text-xs font-semibold text-gray-600 mb-3 flex items-center gap-2">
                            <span class="w-5 h-5 bg-indigo-100 text-indigo-600 rounded-full flex items-center
                                         justify-center text-xs font-bold">2</span>
                            2-semestr soatlari
                        </h4>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div v-for="f in semester2Fields" :key="f.key">
                                <label class="block text-xs text-gray-500 mb-1">
                                    {{ f.label }}
                                    <span v-if="maxHours[f.key] > 0"
                                          class="text-gray-400">(max: {{ remainingHours[f.key] ?? maxHours[f.key] }})</span>
                                </label>
                                <input
                                    v-model.number="form[f.key]"
                                    type="number" min="0" step="0.5"
                                    :disabled="isFieldDisabled(f.key)"
                                    :class="[
                                        'w-full text-sm px-3 py-2 rounded-lg border transition outline-none',
                                        isFieldDisabled(f.key)
                                            ? 'bg-gray-100 text-gray-400 cursor-not-allowed border-gray-200'
                                            : isOverLimit(f.key)
                                                ? 'border-red-400 bg-red-50 text-red-700 focus:ring-2 focus:ring-red-200'
                                                : isBelowOriginal(f.key)
                                                    ? 'border-red-400 bg-red-50 text-red-700 focus:ring-2 focus:ring-red-200'
                                                    : 'border-gray-300 bg-white text-gray-800 focus:ring-2 focus:ring-blue-200 focus:border-blue-400'
                                    ]"
                                />
                                <p v-if="isOverLimit(f.key)"
                                   class="text-xs text-red-500 mt-0.5">
                                    Limit: {{ remainingHours[f.key] }} soat
                                </p>
                                <p v-else-if="isBelowOriginal(f.key)"
                                   class="text-xs text-red-500 mt-0.5">
                                    Kamaytirish mumkin emas
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Qo'shimcha soatlar -->
                    <div v-if="hasExtraHours && !workload.is_potok">
                        <h4 class="text-xs font-semibold text-gray-600 mb-3">Qo'shimcha soatlar</h4>
                        <div class="grid grid-cols-3 gap-3">
                            <div v-for="f in extraFields" :key="f.key">
                                <label class="block text-xs text-gray-500 mb-1">
                                    {{ f.label }}
                                    <span v-if="maxHours[f.key] > 0"
                                          class="text-gray-400">(max: {{ remainingHours[f.key] ?? maxHours[f.key] }})</span>
                                </label>
                                <input
                                    v-model.number="form[f.key]"
                                    type="number" min="0" step="0.5"
                                    :disabled="maxHours[f.key] === 0"
                                    :class="[
                                        'w-full text-sm px-3 py-2 rounded-lg border transition outline-none',
                                        maxHours[f.key] === 0
                                            ? 'bg-gray-100 text-gray-400 cursor-not-allowed border-gray-200'
                                            : isOverLimit(f.key)
                                                ? 'border-red-400 bg-red-50 text-red-700 focus:ring-2 focus:ring-red-200'
                                                : 'border-gray-300 bg-white text-gray-800 focus:ring-2 focus:ring-blue-200 focus:border-blue-400'
                                    ]"
                                />
                                <p v-if="isOverLimit(f.key)"
                                   class="text-xs text-red-500 mt-0.5">
                                    Limit: {{ remainingHours[f.key] }} soat
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Izoh -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">📝 Izoh</h3>
                    <textarea v-model="form.notes" rows="3"
                              placeholder="Qo'shimcha izoh..."
                              class="w-full rounded-lg border border-gray-300 text-sm px-3 py-2
                                     focus:ring-2 focus:ring-blue-300 focus:border-blue-400
                                     outline-none resize-none" />
                </div>

                <!-- Tugmalar -->
                <div class="flex items-center justify-between">
                    <Link :href="route('workloads.show', workload.id)"
                          class="px-4 py-2 text-sm text-gray-600 bg-white border border-gray-300
                                 rounded-lg hover:bg-gray-50 transition-colors">
                        Bekor qilish
                    </Link>
                    <button
                        @click="submit"
                        :disabled="form.processing || hasOverLimit || hasBelowOriginal"
                        class="px-6 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg
                               hover:bg-blue-700 transition-colors
                               disabled:opacity-40 disabled:cursor-not-allowed">
                        {{ form.processing ? 'Saqlanmoqda...' : '✓ Saqlash' }}
                    </button>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
const route = window.route
// ─── Props ────────────────────────────────────────────────────────────────────
const props = defineProps({
    workload: { type: Object, required: true },
    teachers: { type: Array,  default: () => [] },
    subject:  { type: Object, default: null },
})

// ─── Form ─────────────────────────────────────────────────────────────────────
const form = useForm({
    teacher_id: props.workload.teacher_id,
    notes:      props.workload.notes ?? '',

    semester_1_lecture:    props.workload.semester_1_lecture    ?? 0,
    semester_1_practical:  props.workload.semester_1_practical  ?? 0,
    semester_1_laboratory: props.workload.semester_1_laboratory ?? 0,
    semester_1_seminar:    props.workload.semester_1_seminar    ?? 0,
    semester_1_practice:   props.workload.semester_1_practice   ?? 0,
    semester_1_exam:       props.workload.semester_1_exam       ?? 0,
    semester_1_test:       props.workload.semester_1_test       ?? 0,

    semester_2_lecture:    props.workload.semester_2_lecture    ?? 0,
    semester_2_practical:  props.workload.semester_2_practical  ?? 0,
    semester_2_laboratory: props.workload.semester_2_laboratory ?? 0,
    semester_2_seminar:    props.workload.semester_2_seminar    ?? 0,
    semester_2_practice:   props.workload.semester_2_practice   ?? 0,
    semester_2_exam:       props.workload.semester_2_exam       ?? 0,
    semester_2_test:       props.workload.semester_2_test       ?? 0,

    coursework_hours:   props.workload.coursework_hours   ?? 0,
    diploma_hours:      props.workload.diploma_hours      ?? 0,
    consultation_hours: props.workload.consultation_hours ?? 0,
})

// ─── O'qituvchi qidiruvi ──────────────────────────────────────────────────────
const teacherSearch = ref('')

const filteredTeachers = computed(() => {
    if (!props.teachers?.length) return []
    const q = teacherSearch.value.toLowerCase()
    if (!q) return props.teachers
    return props.teachers.filter(t =>
        t.name?.toLowerCase().includes(q) ||
        t.position?.toLowerCase().includes(q)
    )
})

// ─── Soat maydonlari ──────────────────────────────────────────────────────────
const semester1Fields = [
    { key: 'semester_1_lecture',    label: "Ma'ruza"      },
    { key: 'semester_1_practical',  label: 'Amaliy'       },
    { key: 'semester_1_laboratory', label: 'Laboratoriya' },
    { key: 'semester_1_seminar',    label: 'Seminar'      },
    { key: 'semester_1_practice',   label: 'Amaliyot'     },
    { key: 'semester_1_exam',       label: 'Imtihon'      },
    { key: 'semester_1_test',       label: 'Sinov'        },
]
const semester2Fields = [
    { key: 'semester_2_lecture',    label: "Ma'ruza"      },
    { key: 'semester_2_practical',  label: 'Amaliy'       },
    { key: 'semester_2_laboratory', label: 'Laboratoriya' },
    { key: 'semester_2_seminar',    label: 'Seminar'      },
    { key: 'semester_2_practice',   label: 'Amaliyot'     },
    { key: 'semester_2_exam',       label: 'Imtihon'      },
    { key: 'semester_2_test',       label: 'Sinov'        },
]
const extraFields = [
    { key: 'coursework_hours',   label: 'Kurs ishi'     },
    { key: 'diploma_hours',      label: 'Diplom ishi'   },
    { key: 'consultation_hours', label: 'Konsultatsiya' },
]
const allFields = [...semester1Fields, ...semester2Fields, ...extraFields]

// ─── Soat limitleri ───────────────────────────────────────────────────────────
const maxHours       = ref({})
const remainingHours = ref({})
const isLoadingHours = ref(false)

onMounted(async () => {
    await loadHourLimits()
})

async function loadHourLimits() {
    isLoadingHours.value = true
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? ''
        const params    = new URLSearchParams()

        if (props.workload.academic_year_id) {
            params.set('academic_year_id', props.workload.academic_year_id)
        }
        params.set('exclude_workload_id', props.workload.id)

        const res = await fetch(
            `/workloads/ajax/subject/${props.workload.subject_id}/details?${params}`,
            {
                headers: {
                    'Accept':           'application/json',
                    'X-CSRF-TOKEN':     csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            }
        )

        if (res.ok) {
            const json = await res.json()
            if (json.success) {
                maxHours.value       = json.max_hours
                remainingHours.value = json.remaining_hours
                return
            }
        }
    } catch (_) {
        // Fallback: subject prop dan
        if (props.subject) {
            allFields.forEach(f => {
                maxHours.value[f.key]       = Number(props.subject[f.key]) || 0
                remainingHours.value[f.key] = Number(props.subject[f.key]) || 0
            })
        }
    } finally {
        isLoadingHours.value = false   // ← SHUNGA O'TKAZING
    }
}

// ─── Potok: maydon disabled holati ───────────────────────────────────────────
function isFieldDisabled(key) {
    // Fan uchun bu maydon 0 bo'lsa — disable
    if ((maxHours.value[key] ?? 0) === 0) return true
    // Potok: faqat ma'ruza soatlari
    if (props.workload.is_potok) {
        return !key.includes('lecture')
    }
    return false
}

// ─── Limit tekshiruvi ─────────────────────────────────────────────────────────
function isOverLimit(key) {
    if (isFieldDisabled(key)) return false
    const entered  = Number(form[key]) || 0
    const limit    = Number(remainingHours.value[key]) || 0
    if (limit === 0) return false
    return entered > limit
}

// Potok: ma'ruza soatini kamaytirish taqiqlangan
function isBelowOriginal(key) {
    if (!props.workload.is_potok) return false
    if (!key.includes('lecture'))  return false
    const original = Number(props.workload[key]) || 0
    const entered  = Number(form[key]) || 0
    return entered < original
}

// ─── Hisob-kitoblar ──────────────────────────────────────────────────────────
const enteredTotal = computed(() =>
    allFields.reduce((s, f) => s + (Number(form[f.key]) || 0), 0)
)
const maxTotal = computed(() =>
    allFields.reduce((s, f) => s + (Number(maxHours.value[f.key]) || 0), 0)
)
const distributionPercent = computed(() =>
    maxTotal.value > 0 ? Math.round((enteredTotal.value / maxTotal.value) * 100) : 0
)
const hasOverLimit = computed(() =>
    allFields.some(f => isOverLimit(f.key))
)
const hasBelowOriginal = computed(() =>
    allFields.some(f => isBelowOriginal(f.key))
)
const hasSemester1Hours = computed(() =>
    semester1Fields.some(f => (maxHours.value[f.key] || 0) > 0)
)
const hasSemester2Hours = computed(() =>
    semester2Fields.some(f => (maxHours.value[f.key] || 0) > 0)
)
const hasExtraHours = computed(() =>
    extraFields.some(f => (maxHours.value[f.key] || 0) > 0)
)
const progressBarClass = computed(() => {
    if (hasOverLimit.value || hasBelowOriginal.value) return 'bg-red-500'
    if (distributionPercent.value === 100)             return 'bg-green-500'
    if (distributionPercent.value > 80)                return 'bg-amber-400'
    return 'bg-blue-500'
})

// ─── Saqlash ─────────────────────────────────────────────────────────────────
function submit() {
    if (hasOverLimit.value || hasBelowOriginal.value) return
    form.put(route('workloads.update', props.workload.id))
}
</script>
