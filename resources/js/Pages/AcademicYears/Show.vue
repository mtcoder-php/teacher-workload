<template>
    <AuthenticatedLayout>
        <template #header>{{ academicYear.name }}</template>

        <div class="space-y-6">

            <!-- Action Buttons -->
            <div class="flex items-center justify-between">
                <Link href="/academic-years"
                      class="flex items-center space-x-1.5 text-sm text-gray-600 hover:text-gray-900">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    <span>Orqaga</span>
                </Link>
                <div class="flex items-center gap-2">
                    <!-- Joriy qilish -->
                    <button v-if="!academicYear.is_active"
                            @click="setActive"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium
                                   bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Joriy qilish
                    </button>
                    <Link :href="`/academic-years/${academicYear.id}/edit`"
                          class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium
                                 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Tahrirlash
                    </Link>
                    <button v-if="!academicYear.is_active"
                            @click="deleteTarget = academicYear"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium
                                   bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        O'chirish
                    </button>
                </div>
            </div>

            <!-- Main Info Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <!-- Gradient Header -->
                <div class="px-6 py-4 border-b border-gray-200"
                     :class="academicYear.is_active
                         ? 'bg-gradient-to-r from-indigo-50 to-blue-50'
                         : 'bg-gradient-to-r from-gray-50 to-slate-50'">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">{{ academicYear.name }}</h2>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ formatDate(academicYear.start_date) }} — {{ formatDate(academicYear.end_date) }}
                            </p>
                        </div>
                        <span class="px-3 py-1 text-sm font-semibold rounded-full"
                              :class="academicYear.is_active
                                  ? 'bg-indigo-100 text-indigo-800'
                                  : 'bg-gray-100 text-gray-600'">
                            {{ academicYear.is_active ? 'Joriy yil' : 'Faol emas' }}
                        </span>
                    </div>
                </div>

                <!-- Info Cards -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                        <!-- Jami yuklamalar -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-blue-100 rounded-lg flex-shrink-0">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Jami yuklamalar</p>
                                        <p class="text-sm font-semibold text-gray-900">{{ workloads_count }} ta</p>
                                    </div>
                                </div>
                                <Link :href="`/workloads?academic_year_id=${academicYear.id}`"
                                      class="text-xs font-medium text-blue-600 hover:text-blue-700 flex-shrink-0">
                                    Ko'rish →
                                </Link>
                            </div>
                        </div>

                        <!-- Tasdiqlangan -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-green-100 rounded-lg flex-shrink-0">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Tasdiqlangan</p>
                                        <p class="text-sm font-semibold text-gray-900">{{ confirmed_count }} ta</p>
                                    </div>
                                </div>
                                <Link :href="`/workloads?academic_year_id=${academicYear.id}&status=confirmed`"
                                      class="text-xs font-medium text-green-600 hover:text-green-700 flex-shrink-0">
                                    Ko'rish →
                                </Link>
                            </div>
                        </div>

                        <!-- O'qituvchilar -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-purple-100 rounded-lg flex-shrink-0">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Yuklamali o'qituvchilar</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ teachers_count }} ta</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        <DeleteModal :show="!!deleteTarget"
                     title="O'quv yilini o'chirish"
                     :item-name="deleteTarget?.name"
                     :loading="deleting"
                     @confirm="doDelete"
                     @cancel="deleteTarget = null"/>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import DeleteModal from '@/Components/DeleteModal.vue'

const props = defineProps({
    academicYear:     Object,
    workloads_count:  { type: Number, default: 0 },
    confirmed_count:  { type: Number, default: 0 },
    teachers_count:   { type: Number, default: 0 },
})

const deleting     = ref(false)
const deleteTarget = ref(null)

function formatDate(date) {
    if (!date) return '—'
    const [y, m, d] = date.split('T')[0].split('-')
    return `${d}.${m}.${y}`
}

function setActive() {
    router.post(`/academic-years/${props.academicYear.id}/set-current`, {}, { preserveScroll: true })
}

function doDelete() {
    deleting.value = true
    router.delete(`/academic-years/${props.academicYear.id}`, {
        onSuccess: () => router.visit('/academic-years'),
        onFinish:  () => { deleting.value = false; deleteTarget.value = null },
    })
}
</script>
