<template>
    <AuthenticatedLayout title="Yuklama ma'lumotlari">

        <template #header>Yuklama ma'lumotlari</template>

        <div class="max-w-5xl mx-auto space-y-5">

            <!-- ═══════════════════════════════════════════════════════════
                 1. STATUS + AMALLAR KARTASI
                 ═══════════════════════════════════════════════════════════ -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                <!-- Yuqori qism: status banner -->
                <div :class="['px-6 py-4 flex items-center justify-between', statusBannerClass]">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-white/40 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path v-if="workload.status === 'confirmed'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                <path v-else-if="workload.status === 'pending'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-base">{{ statusLabel }}</p>
                            <p class="text-xs opacity-70 mt-0.5">{{ statusHint }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-bold">{{ stats.grand_total }}</p>
                        <p class="text-xs opacity-70">jami soat</p>
                    </div>
                </div>

                <!-- Quyi qism: amal tugmalari -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between flex-wrap gap-3">

                    <!-- Orqaga -->
                    <Link
                        href="/workloads"
                        class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Yuklamalar ro'yxati
                    </Link>

                    <!-- Amal tugmalari -->
                    <div class="flex items-center gap-2 flex-wrap">

                        <!-- Tekshiruvga yuborish (draft → pending) -->
                        <button
                            v-if="canSubmit"
                            @click="confirmAction('submit')"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 text-white text-sm font-medium rounded-lg hover:bg-amber-600 transition-colors shadow-sm"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            Tekshiruvga yuborish
                        </button>

                        <!-- Tasdiqlash (pending → confirmed) -->
                        <button
                            v-if="canApprove"
                            @click="confirmAction('approve')"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors shadow-sm"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Tasdiqlash
                        </button>

                        <!-- Qaytarish (pending → draft) -->
                        <button
                            v-if="canReject"
                            @click="confirmAction('reject')"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition-colors shadow-sm"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                            Qaytarish
                        </button>

                        <!-- Tahrirlash -->
                        <Link
                            v-if="canEdit"
                            :href="`/workloads/${workload.id}/edit`"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Tahrirlash
                        </Link>

                        <!-- O'chirish -->
                        <button
                            v-if="canDelete"
                            @click="confirmAction('delete')"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 text-red-600 border border-red-200 text-sm font-medium rounded-lg hover:bg-red-100 transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            O'chirish
                        </button>

                    </div>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════════════
                 2. ASOSIY MA'LUMOTLAR
                 ═══════════════════════════════════════════════════════════ -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4">
                    Asosiy ma'lumotlar
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Fan</p>
                            <p class="text-sm font-semibold text-gray-800">{{ workload.subject?.name }}</p>
                            <p class="text-xs text-gray-400">{{ workload.subject?.code }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">O'qituvchi</p>
                            <p class="text-sm font-semibold text-gray-800">{{ workload.teacher?.user?.name }}</p>
                            <p class="text-xs text-gray-400">{{ workload.teacher?.position }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-purple-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Kafedra</p>
                            <p class="text-sm font-medium text-gray-800">{{ workload.department?.name }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">O'quv yili</p>
                            <p class="text-sm font-medium text-gray-800">{{ workload.academic_year?.name }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Yo'nalish</p>
                            <p class="text-sm font-medium text-gray-800">{{ workload.direction?.name || '—' }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-pink-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Turi</p>
                            <span :class="[
                                'text-xs font-medium px-2.5 py-1 rounded-full',
                                workload.is_potok
                                    ? 'bg-purple-100 text-purple-700'
                                    : 'bg-blue-100 text-blue-700'
                            ]">
                                {{ workload.is_potok ? 'Potokli' : 'Potoksiz' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════════════
                 3. GURUHLAR
                 ═══════════════════════════════════════════════════════════ -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-3">
                    Guruhlar
                    <span class="ml-2 normal-case font-normal text-gray-400">
                        — {{ workload.groups?.length || 0 }} ta, {{ totalStudents }} talaba
                    </span>
                </h3>
                <div class="flex flex-wrap gap-2">
                    <div
                        v-for="g in workload.groups" :key="g.id"
                        class="flex items-center gap-2 px-3 py-2 bg-blue-50 border border-blue-100 rounded-xl"
                    >
                        <div class="w-6 h-6 rounded-full bg-blue-200 text-blue-700 flex items-center justify-center text-xs font-bold">
                            {{ g.name?.charAt(0) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-blue-800 leading-none">{{ g.name }}</p>
                            <p class="text-xs text-blue-400 mt-0.5">{{ g.student_count }} talaba</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════════════
                 4. SOATLAR JADVALI
                 ═══════════════════════════════════════════════════════════ -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4">
                    Soatlar taqsimoti
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                        <tr class="border-b-2 border-gray-100">
                            <th class="text-left pb-3 pr-4 text-gray-500 font-medium">Turi</th>
                            <th class="text-right pb-3 px-3 text-gray-500 font-medium">1-semestr</th>
                            <th class="text-right pb-3 px-3 text-gray-500 font-medium">2-semestr</th>
                            <th class="text-right pb-3 text-gray-700 font-semibold">Jami</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                        <template v-for="row in hourRows" :key="row.label">
                            <tr v-if="row.s1 > 0 || row.s2 > 0" class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 pr-4 text-gray-700">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-blue-400 flex-shrink-0"></span>
                                        {{ row.label }}
                                    </div>
                                </td>
                                <td class="text-right py-3 px-3 text-gray-600 tabular-nums">
                                    {{ row.s1 > 0 ? row.s1 : '—' }}
                                </td>
                                <td class="text-right py-3 px-3 text-gray-600 tabular-nums">
                                    {{ row.s2 > 0 ? row.s2 : '—' }}
                                </td>
                                <td class="text-right py-3 font-semibold text-gray-800 tabular-nums">
                                    {{ row.s1 + row.s2 }}
                                </td>
                            </tr>
                        </template>

                        <!-- Qo'shimcha soatlar -->
                        <tr v-if="workload.coursework_hours > 0" class="hover:bg-gray-50">
                            <td colspan="3" class="py-3 text-gray-700">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-purple-400 flex-shrink-0"></span>
                                    Kurs ishi
                                </div>
                            </td>
                            <td class="text-right py-3 font-semibold text-gray-800">{{ workload.coursework_hours }}</td>
                        </tr>
                        <tr v-if="workload.diploma_hours > 0" class="hover:bg-gray-50">
                            <td colspan="3" class="py-3 text-gray-700">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-purple-400 flex-shrink-0"></span>
                                    Diplom ishi
                                </div>
                            </td>
                            <td class="text-right py-3 font-semibold text-gray-800">{{ workload.diploma_hours }}</td>
                        </tr>
                        <tr v-if="workload.consultation_hours > 0" class="hover:bg-gray-50">
                            <td colspan="3" class="py-3 text-gray-700">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-purple-400 flex-shrink-0"></span>
                                    Konsultatsiya
                                </div>
                            </td>
                            <td class="text-right py-3 font-semibold text-gray-800">{{ workload.consultation_hours }}</td>
                        </tr>
                        </tbody>

                        <!-- Jami qator -->
                        <tfoot>
                        <tr class="border-t-2 border-gray-200">
                            <td class="pt-3 pr-4 font-semibold text-gray-800">Jami</td>
                            <td class="text-right pt-3 px-3 font-semibold text-gray-700 tabular-nums">
                                {{ stats.semester_1_total }}
                            </td>
                            <td class="text-right pt-3 px-3 font-semibold text-gray-700 tabular-nums">
                                {{ stats.semester_2_total }}
                            </td>
                            <td class="text-right pt-3 font-bold text-blue-700 text-base tabular-nums">
                                {{ stats.grand_total }}
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════════════
                 5. IZOH
                 ═══════════════════════════════════════════════════════════ -->
            <div v-if="workload.notes" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-2">Izoh</h3>
                <p class="text-gray-700 text-sm leading-relaxed">{{ workload.notes }}</p>
            </div>

            <!-- ═══════════════════════════════════════════════════════════
                 6. TASDIQLANGAN MA'LUMOT
                 ═══════════════════════════════════════════════════════════ -->
            <div
                v-if="workload.status === 'confirmed' && workload.approved_at"
                class="bg-green-50 border border-green-200 rounded-2xl p-4 flex items-center gap-3"
            >
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-green-800">Tasdiqlangan</p>
                    <p class="text-xs text-green-600 mt-0.5">{{ formatDate(workload.approved_at) }}</p>
                </div>
            </div>

        </div>

        <!-- ═══════════════════════════════════════════════════════════
             AMAL TASDIQLASH MODALI
             ═══════════════════════════════════════════════════════════ -->
        <div
            v-if="showModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 backdrop-blur-sm"
            @click.self="showModal = false"
        >
            <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md mx-4">
                <div class="flex items-start gap-4 mb-6">
                    <div :class="['w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0', activeConfig.iconBg]">
                        <svg class="w-6 h-6" :class="activeConfig.iconColor" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path v-if="currentAction === 'submit'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            <path v-else-if="currentAction === 'approve'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            <path v-else-if="currentAction === 'reject'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">{{ activeConfig.title }}</h3>
                        <p class="text-sm text-gray-500 mt-1 leading-relaxed">{{ activeConfig.text }}</p>
                    </div>
                </div>
                <div class="flex gap-3 justify-end">
                    <button
                        @click="showModal = false"
                        class="px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors"
                    >
                        Bekor qilish
                    </button>
                    <button
                        @click="executeAction"
                        :disabled="isProcessing"
                        :class="['px-5 py-2.5 text-sm font-medium text-white rounded-xl transition-colors disabled:opacity-50', activeConfig.btnClass]"
                    >
                        {{ isProcessing ? 'Bajarilmoqda...' : activeConfig.btnLabel }}
                    </button>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { useToast } from '@/Composables/useToast'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const toast = useToast()

const props = defineProps({
    workload:    { type: Object,  required: true },
    stats:       { type: Object,  default: () => ({ semester_1_total: 0, semester_2_total: 0, grand_total: 0 }) },
    canEdit:     { type: Boolean, default: false },
    canDelete:   { type: Boolean, default: false },
    canSubmit:   { type: Boolean, default: false },
    canApprove:  { type: Boolean, default: false },
    canReject:   { type: Boolean, default: false },
    currentUser: { type: Object,  default: () => ({}) },
})

// ─── Modal ────────────────────────────────────────────────────────────────────
const showModal     = ref(false)
const isProcessing  = ref(false)
const currentAction = ref('')

const modalConfigs = computed(() => ({
    submit: {
        title:    'Tekshiruvga yuborish',
        text:     'Yuklama adminга tekshiruvga yuboriladi. Tasdiqlangandan keyin hisobotlarda ko\'rinadi.',
        iconBg:   'bg-amber-100',
        iconColor:'text-amber-600',
        btnClass: 'bg-amber-500 hover:bg-amber-600',
        btnLabel: 'Ha, yuborish',
    },
    approve: {
        title:    'Yuklamani tasdiqlash',
        text:     'Yuklama tasdiqlanadi va hisobotlarda ko\'rinadi. Davom etasizmi?',
        iconBg:   'bg-green-100',
        iconColor:'text-green-600',
        btnClass: 'bg-green-600 hover:bg-green-700',
        btnLabel: 'Ha, tasdiqlash',
    },
    reject: {
        title:    'Yuklamani qaytarish',
        text:     'Yuklama qoralamaga qaytariladi. Kafedra mudiri qayta ko\'rib chiqadi.',
        iconBg:   'bg-orange-100',
        iconColor:'text-orange-600',
        btnClass: 'bg-orange-500 hover:bg-orange-600',
        btnLabel: 'Ha, qaytarish',
    },
    delete: {
        title:    'Yuklamani o\'chirish',
        text:     props.workload.status === 'confirmed'
            ? 'Tasdiqlangan yuklama arxivlanadi. Ma\'lumotlar saqlanib qoladi, hisobotlarda ko\'rinmaydi.'
            : 'Yuklama butunlay o\'chiriladi. Bu amalni qaytarib bo\'lmaydi!',
        iconBg:   'bg-red-100',
        iconColor:'text-red-600',
        btnClass: 'bg-red-600 hover:bg-red-700',
        btnLabel: 'Ha, o\'chirish',
    },
}))

const activeConfig = computed(() =>
    modalConfigs.value[currentAction.value] ?? {}
)

function confirmAction(action) {
    currentAction.value = action
    showModal.value = true
}

function executeAction() {
    isProcessing.value = true
    const id = props.workload.id
    const config = {
        submit:  { url: `/workloads/${id}/submit`,  method: 'post'   },
        approve: { url: `/workloads/${id}/approve`, method: 'post'   },
        reject:  { url: `/workloads/${id}/reject`,  method: 'post'   },
        delete:  { url: `/workloads/${id}`,         method: 'delete' },
    }
    const { url, method } = config[currentAction.value]

    router[method](url, {}, {
        onFinish:  () => { isProcessing.value = false },
        onSuccess: () => { toast.success('Yuklama muvaffaqiyatli o\'chirildi!'); router.visit('/workloads') },
        onError:   () => { isProcessing.value = false },
    })
}

// ─── Guruhlar ────────────────────────────────────────────────────────────────
const totalStudents = computed(() =>
    (props.workload.groups ?? []).reduce((s, g) => s + (g.student_count ?? 0), 0)
)

// ─── Soatlar ─────────────────────────────────────────────────────────────────
const hourRows = [
    { label: 'Ma\'ruza',     s1k: 'semester_1_lecture',    s2k: 'semester_2_lecture'    },
    { label: 'Amaliy',       s1k: 'semester_1_practical',  s2k: 'semester_2_practical'  },
    { label: 'Laboratoriya', s1k: 'semester_1_laboratory', s2k: 'semester_2_laboratory' },
    { label: 'Seminar',      s1k: 'semester_1_seminar',    s2k: 'semester_2_seminar'    },
    { label: 'Amaliyot',     s1k: 'semester_1_practice',   s2k: 'semester_2_practice'   },
    { label: 'Imtihon',      s1k: 'semester_1_exam',       s2k: 'semester_2_exam'       },
    { label: 'Sinov',        s1k: 'semester_1_test',       s2k: 'semester_2_test'       },
].map(r => ({
    label: r.label,
    s1:    Number(props.workload[r.s1k]) || 0,
    s2:    Number(props.workload[r.s2k]) || 0,
}))

// ─── Status ───────────────────────────────────────────────────────────────────
const statusMap = {
    draft:     { label: 'Qoralama',     banner: 'bg-gray-100  text-gray-800',   hint: 'Hali yuborilmagan' },
    pending:   { label: 'Tekshiruvda',  banner: 'bg-amber-50  text-amber-800',  hint: 'Admin tasdiqlashini kutmoqda' },
    confirmed: { label: 'Tasdiqlangan', banner: 'bg-green-50  text-green-800',  hint: 'Tasdiqlangan — hisobotlarda ko\'rinadi' },
    completed: { label: 'Tugatilgan',   banner: 'bg-blue-50   text-blue-800',   hint: 'Tugatilgan' },
}

const statusLabel       = computed(() => statusMap[props.workload.status]?.label  ?? props.workload.status)
const statusBannerClass = computed(() => statusMap[props.workload.status]?.banner ?? 'bg-gray-100 text-gray-800')
const statusHint        = computed(() => statusMap[props.workload.status]?.hint   ?? '')

function formatDate(date) {
    if (!date) return '—'
    const d = new Date(date)
    const day   = String(d.getDate()).padStart(2, '0')
    const month = String(d.getMonth() + 1).padStart(2, '0')
    const year  = d.getFullYear()
    const hour  = String(d.getHours()).padStart(2, '0')
    const min   = String(d.getMinutes()).padStart(2, '0')
    return `${day}.${month}.${year} ${hour}:${min}`
}
</script>
