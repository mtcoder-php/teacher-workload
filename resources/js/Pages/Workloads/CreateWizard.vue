<template>
    <AuthenticatedLayout>
        <template #header>Yangi yuklama yaratish</template>

        <div class="max-w-4xl mx-auto space-y-5">

            <!-- Orqaga -->
            <div>
                <Link href="/workloads"
                      class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Orqaga qaytish
                </Link>
            </div>

            <!-- Progress -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 px-8 py-6">
                <WizardProgress :current-step="currentStep" :steps="stepTitles" />
            </div>

            <!-- Kontent -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100">

                <!-- Server xatosi -->
                <div v-if="$page.props.errors?.error"
                     class="mx-6 mt-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ $page.props.errors.error }}
                </div>

                <div class="p-6 sm:p-8">

                    <!-- Step 1: Kafedra + Yo'nalish + Guruhlar -->
                    <Step1
                        v-if="currentStep === 1"
                        v-model="form"
                        :departments="departments"
                        :directions="directions"
                        :groups="groups"
                        :current-user="currentUser"
                        @valid="stepValid[1] = $event"
                    />

                    <!-- Step 2: Fan + Soatlar -->
                    <Step3
                        v-else-if="currentStep === 2"
                        v-model="form"
                        :subjects="subjects"
                        :groups="groups"
                        :current-academic-year="currentAcademicYear"
                        @valid="stepValid[2] = $event"
                        @subject-loaded="onSubjectLoaded"
                    />

                    <!-- Step 3: O'qituvchi -->
                    <Step4
                        v-else-if="currentStep === 3"
                        v-model="form"
                        :teachers="teachers"
                        @valid="stepValid[3] = $event"
                    />

                    <!-- Step 4: Tasdiqlash -->
                    <Step5
                        v-else-if="currentStep === 4"
                        :form="form"
                        :departments="departments"
                        :directions="directions"
                        :subjects="subjects"
                        :groups="groups"
                        :teachers="teachers"
                        @valid="stepValid[4] = $event"
                    />

                </div>

                <!-- Navigatsiya -->
                <div class="px-6 sm:px-8 py-5 bg-gray-50 rounded-b-lg border-t border-gray-100 flex items-center justify-between">
                    <div class="flex gap-3">
                        <button
                            v-if="currentStep > 1"
                            @click="prevStep"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-600
                                       bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Orqaga
                        </button>
                        <button
                            @click="saveDraft"
                            :disabled="isSaving"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-amber-700
                                       bg-amber-50 border border-amber-200 rounded-lg hover:bg-amber-100
                                       transition-colors disabled:opacity-50"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                            {{ isSaving ? 'Saqlanmoqda...' : 'Qoralama' }}
                        </button>
                    </div>

                    <div>
                        <button
                            v-if="currentStep < totalSteps"
                            @click="nextStep"
                            :disabled="!stepValid[currentStep]"
                            class="inline-flex items-center gap-2 px-6 py-2 text-sm font-medium text-white
                                       bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors
                                       disabled:opacity-40 disabled:cursor-not-allowed"
                        >
                            Keyingisi
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                        <button
                            v-else
                            @click="submitForm"
                            :disabled="isSaving"
                            class="inline-flex items-center gap-2 px-6 py-2 text-sm font-medium text-white
                                       bg-green-600 rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ isSaving ? 'Saqlanmoqda...' : 'Yuklama yaratish' }}
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useToast } from '@/Composables/useToast'
import { Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import WizardProgress from '@/Components/Workloads/WizardProgress.vue'
import Step1 from '@/Components/Workloads/Steps/Step1.vue'   // Kafedra + Yo'nalish + Guruhlar
import Step3 from '@/Components/Workloads/Steps/Step3.vue'   // Fan + Soatlar
import Step4 from '@/Components/Workloads/Steps/Step4.vue'   // O'qituvchi
import Step5 from '@/Components/Workloads/Steps/Step5.vue'   // Tasdiqlash

// ─── Props ───────────────────────────────────────────────────────────────────
const props = defineProps({
    departments:         { type: Array,  default: () => [] },
    directions:          { type: Array,  default: () => [] },
    subjects:            { type: Array,  default: () => [] },
    groups:              { type: Array,  default: () => [] },
    teachers:            { type: Array,  default: () => [] },
    currentAcademicYear: { type: Object, default: null },
    currentUser:         { type: Object, default: () => ({}) },
})

// ─── Wizard ──────────────────────────────────────────────────────────────────
const totalSteps = 4    // Step2 (guruhlar) birinchi qadamga kiritildi
const currentStep = ref(1)
const isSaving    = ref(false)
const toast        = useToast()

const stepTitles = [
    { label: 'Kafedra & Guruhlar' },
    { label: 'Fan & Soatlar' },
    { label: 'O\'qituvchi' },
    { label: 'Tasdiqlash' },
]

const stepValid = reactive({ 1: false, 2: false, 3: false, 4: true })

// ─── Forma ───────────────────────────────────────────────────────────────────
const form = reactive({
    // Step 1
    department_id:  props.currentUser?.is_kafedra_mudiri ? props.currentUser.department_id : null,
    is_potok:       false,
    direction_ids:  [],
    course:         null,
    group_ids:      [],

    // Step 2 (Fan + soatlar)
    subject_id:       null,
    academic_year_id: props.currentAcademicYear?.id ?? null,

    semester_1_lecture:    0, semester_1_practical:  0,
    semester_1_laboratory: 0, semester_1_seminar:    0,
    semester_1_practice:   0, semester_1_exam:       0, semester_1_test: 0,
    semester_2_lecture:    0, semester_2_practical:  0,
    semester_2_laboratory: 0, semester_2_seminar:    0,
    semester_2_practice:   0, semester_2_exam:       0, semester_2_test: 0,
    coursework_hours: 0, diploma_hours: 0, consultation_hours: 0,

    // Step 3
    teacher_id: null,
    notes:      '',
    has_rating:       false,   // ← QO'SHING
    status: 'draft',
})

// ─── Navigatsiya ─────────────────────────────────────────────────────────────
function nextStep() {
    if (stepValid[currentStep.value] && currentStep.value < totalSteps) {
        currentStep.value++
        window.scrollTo({ top: 0, behavior: 'smooth' })
    }
}
function prevStep() {
    if (currentStep.value > 1) {
        currentStep.value--
        window.scrollTo({ top: 0, behavior: 'smooth' })
    }
}

function onSubjectLoaded() {
    const keys = [
        'semester_1_lecture','semester_1_practical','semester_1_laboratory',
        'semester_1_seminar','semester_1_practice','semester_1_exam','semester_1_test',
        'semester_2_lecture','semester_2_practical','semester_2_laboratory',
        'semester_2_seminar','semester_2_practice','semester_2_exam','semester_2_test',
        'coursework_hours','diploma_hours','consultation_hours',
    ]
    keys.forEach(k => { form[k] = 0 })
}

// ─── Saqlash ─────────────────────────────────────────────────────────────────
function saveDraft() {
    isSaving.value = true
    router.post('/workloads', { ...form, status: 'draft' }, {
        onSuccess: () => toast.success('Qoralama muvaffaqiyatli saqlandi!'),
        onError:   (e) => toast.error(e?.error ?? 'Saqlashda xatolik!'),
        onFinish:  () => { isSaving.value = false },
    })
}
function submitForm() {
    isSaving.value = true
    router.post('/workloads', { ...form, status: 'pending' }, {
        onSuccess: () => toast.success('Yuklama yaratildi va tekshiruvga yuborildi!'),
        onError:   (e) => toast.error(e?.error ?? 'Xatolik yuz berdi!'),
        onFinish:  () => { isSaving.value = false },
    })
}
</script>
