<template>
    <AuthenticatedLayout>
        <template #header>{{ direction.name }}</template>

        <div class="space-y-6">

            <!-- Action Buttons — kichik -->
            <div class="flex items-center justify-between">
                <Link href="/directions"
                      class="flex items-center space-x-1.5 text-sm text-gray-600 hover:text-gray-900">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    <span>Orqaga</span>
                </Link>
                <div class="flex items-center gap-2">
                    <Link :href="`/directions/${direction.id}/edit`"
                          class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium
                                 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Tahrirlash
                    </Link>
                    <button @click="deleteTarget = direction"
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
                            <h2 class="text-2xl font-bold text-gray-900">{{ direction.name }}</h2>
                            <p class="text-sm text-gray-600 mt-1">{{ direction.code }}</p>
                        </div>
                        <span class="px-3 py-1 text-sm font-semibold rounded-full"
                              :class="direction.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                            {{ direction.is_active ? 'Faol' : 'Nofaol' }}
                        </span>
                    </div>
                </div>

                <!-- Info Cards -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <!-- Kafedra -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-indigo-100 rounded-lg flex-shrink-0">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs text-gray-500">Kafedra</p>
                                    <Link v-if="direction.department"
                                          :href="`/departments/${direction.department.id}`"
                                          class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 truncate block">
                                        {{ direction.department.name }}
                                    </Link>
                                    <p v-else class="text-sm font-semibold text-gray-900">—</p>
                                    <p v-if="direction.department?.faculty" class="text-xs text-gray-400 truncate">
                                        {{ direction.department.faculty.name }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Ta'lim darajasi -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-blue-100 rounded-lg flex-shrink-0">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Ta'lim darajasi</p>
                                    <p class="text-sm font-semibold text-gray-900">
                                        {{ direction.degree_type === 'magistratura' ? 'Magistratura' : 'Bakalavr' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Davomiyligi -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-orange-100 rounded-lg flex-shrink-0">
                                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Ta'lim davomiyligi</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ direction.duration_years }} yil</p>
                                </div>
                            </div>
                        </div>

                        <!-- Guruhlar -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-green-100 rounded-lg flex-shrink-0">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Guruhlar</p>
                                        <p class="text-sm font-semibold text-gray-900">{{ groups_count }} ta</p>
                                    </div>
                                </div>
                                <Link :href="`/groups?direction_id=${direction.id}`"
                                      class="text-xs font-medium text-green-600 hover:text-green-700 flex-shrink-0">
                                    Ko'rish →
                                </Link>
                            </div>
                        </div>

                    </div>

                    <!-- Tavsif -->
                    <div v-if="direction.description" class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <p class="text-xs font-medium text-blue-700 mb-1">Tavsif</p>
                        <p class="text-sm text-blue-800">{{ direction.description }}</p>
                    </div>
                </div>
            </div>

        </div>

        <DeleteModal :show="!!deleteTarget"
                     title="Yo'nalishni o'chirish"
                     :item-name="deleteTarget?.name"
                     :loading="deleting"
                     @confirm="doDelete"
                     @cancel="deleteTarget = null"/>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { useToast } from '@/Composables/useToast'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import DeleteModal from '@/Components/DeleteModal.vue'

const toast = useToast()

const props = defineProps({
    direction:    Object,
    groups_count: { type: Number, default: 0 },
})

const deleting     = ref(false)
const deleteTarget = ref(null)

function doDelete() {
    deleting.value = true
    router.delete(`/directions/${props.direction.id}`, {
        onSuccess: () => { toast.success('Yo\'nalish muvaffaqiyatli o\'chirildi!'); router.visit('/directions') },
        onFinish:  () => { deleting.value = false; deleteTarget.value = null },
    })
}
</script>
