<template>
    <AuthenticatedLayout>
        <template #header>{{ group.name }}</template>

        <div class="space-y-6">

            <!-- Action Buttons — kichik -->
            <div class="flex items-center justify-between">
                <Link href="/groups"
                      class="flex items-center space-x-1.5 text-sm text-gray-600 hover:text-gray-900">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    <span>Orqaga</span>
                </Link>
                <div class="flex items-center gap-2">
                    <Link :href="`/groups/${group.id}/edit`"
                          class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium
                                 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Tahrirlash
                    </Link>
                    <button @click="deleteTarget = group"
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
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-purple-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">{{ group.name }}</h2>
                            <p class="text-sm text-gray-600 mt-1">{{ group.code }}</p>
                        </div>
                        <span class="px-3 py-1 text-sm font-semibold rounded-full"
                              :class="group.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                            {{ group.is_active ? 'Faol' : 'Nofaol' }}
                        </span>
                    </div>
                </div>

                <!-- Info Cards -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

                        <!-- Yo'nalish — link -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-blue-100 rounded-lg flex-shrink-0">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs text-gray-500">Yo'nalish</p>
                                    <Link v-if="group.direction"
                                          :href="`/directions/${group.direction.id}`"
                                          class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 truncate block">
                                        {{ group.direction.name }}
                                    </Link>
                                    <p v-else class="text-sm font-semibold text-gray-900">—</p>
                                    <p v-if="group.direction?.department" class="text-xs text-gray-400 truncate">
                                        {{ group.direction.department.name }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Kurs -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-orange-100 rounded-lg flex-shrink-0">
                                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Kurs</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ getCourseLabel(group.course) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Ta'lim shakli -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-indigo-100 rounded-lg flex-shrink-0">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Ta'lim shakli</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ eduTypeLabel(group.education_type) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Ta'lim tili -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-amber-100 rounded-lg flex-shrink-0">
                                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Ta'lim tili</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ eduLangLabel(group.education_language) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Talabalar soni -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-green-100 rounded-lg flex-shrink-0">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Talabalar soni</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ group.student_count || 0 }} ta</p>
                                </div>
                            </div>
                        </div>

                        <!-- Yuklamalar soni -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-purple-100 rounded-lg flex-shrink-0">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Yuklamalar</p>
                                        <p class="text-sm font-semibold text-gray-900">{{ workloads_count }} ta</p>
                                    </div>
                                </div>
                                <Link :href="`/workloads?group_id=${group.id}`"
                                      class="text-xs font-medium text-purple-600 hover:text-purple-700 flex-shrink-0">
                                    Ko'rish →
                                </Link>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        <DeleteModal :show="!!deleteTarget"
                     title="Guruhni o'chirish"
                     :item-name="deleteTarget?.name"
                     :loading="deleting"
                     @confirm="doDelete"
                     @cancel="deleteTarget = null"/>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import { useToast } from '@/Composables/useToast'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import DeleteModal from '@/Components/DeleteModal.vue'

const toast = useToast()

const props = defineProps({
    group:           Object,
    workloads_count: { type: Number, default: 0 },
})

const deleting     = ref(false)
const deleteTarget = ref(null)

function doDelete() {
    deleting.value = true
    router.delete(`/groups/${props.group.id}`, {
        onSuccess: () => {
            const flash = usePage().props.flash
            if (flash?.error) toast.error(flash.error)
            else { toast.success('Guruh muvaffaqiyatli o\'chirildi!'); router.visit('/groups') }
        },
        onFinish:  () => { deleting.value = false; deleteTarget.value = null },
    })
}

function getCourseLabel(course) {
    return {
        1: '1-kurs (Bakalavr)',   2: '2-kurs (Bakalavr)',
        3: '3-kurs (Bakalavr)',   4: '4-kurs (Bakalavr)',
        5: '1-kurs (Magistratura)', 6: '2-kurs (Magistratura)',
    }[course] ?? `${course}-kurs`
}

function eduTypeLabel(t) {
    return { kunduzgi:'Kunduzgi', sirtqi:'Sirtqi', kechki:'Kechki', masofaviy:'Masofaviy' }[t] ?? t ?? '—'
}

function eduLangLabel(l) {
    return { uzbek:"O'zbek tili", russian:'Rus tili' }[l] ?? l ?? '—'
}
</script>
