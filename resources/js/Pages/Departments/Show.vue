<template>
    <AuthenticatedLayout>
        <template #header>{{ department.name }}</template>

        <div class="space-y-6">

            <!-- Action Buttons — kichik -->
            <div class="flex items-center justify-between">
                <Link href="/departments"
                      class="flex items-center space-x-1.5 text-sm text-gray-600 hover:text-gray-900">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    <span>Orqaga</span>
                </Link>
                <div class="flex items-center gap-2">
                    <Link :href="`/departments/${department.id}/edit`"
                          class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium
                                 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Tahrirlash
                    </Link>
                    <button @click="deleteTarget = department"
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
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-indigo-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">{{ department.name }}</h2>
                            <p class="text-sm text-gray-600 mt-1">{{ department.code }}</p>
                        </div>
                        <span class="px-3 py-1 text-sm font-semibold rounded-full"
                              :class="department.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                            {{ department.is_active ? 'Faol' : 'Nofaol' }}
                        </span>
                    </div>
                </div>

                <!-- Info Cards -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <!-- Fakultet -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-indigo-100 rounded-lg flex-shrink-0">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs text-gray-500">Fakultet</p>
                                    <Link v-if="department.faculty"
                                          :href="`/faculties/${department.faculty.id}`"
                                          class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 truncate block">
                                        {{ department.faculty.name }}
                                    </Link>
                                    <p v-else class="text-sm font-semibold text-gray-900">—</p>
                                </div>
                            </div>
                        </div>

                        <!-- Kafedra mudiri -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-purple-100 rounded-lg flex-shrink-0">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs text-gray-500">Kafedra mudiri</p>
                                    <p class="text-sm font-semibold text-gray-900 truncate">
                                        {{ department.head?.name || '—' }}
                                    </p>
                                    <p v-if="department.head?.email" class="text-xs text-gray-400 truncate">
                                        {{ department.head.email }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- O'qituvchilar -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-blue-100 rounded-lg flex-shrink-0">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">O'qituvchilar</p>
                                        <p class="text-sm font-semibold text-gray-900">{{ teachers_count }} ta</p>
                                    </div>
                                </div>
                                <Link :href="`/teachers?department_id=${department.id}`"
                                      class="text-xs font-medium text-blue-600 hover:text-blue-700 flex-shrink-0">
                                    Ko'rish →
                                </Link>
                            </div>
                        </div>

                        <!-- Yo'nalishlar -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-orange-100 rounded-lg flex-shrink-0">
                                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Yo'nalishlar</p>
                                        <p class="text-sm font-semibold text-gray-900">{{ directions_count }} ta</p>
                                    </div>
                                </div>
                                <Link :href="`/directions?department_id=${department.id}`"
                                      class="text-xs font-medium text-orange-600 hover:text-orange-700 flex-shrink-0">
                                    Ko'rish →
                                </Link>
                            </div>
                        </div>

                    </div>

                    <!-- Tavsif -->
                    <div v-if="department.description" class="mt-4 p-4 bg-purple-50 rounded-lg border border-purple-200">
                        <p class="text-xs font-medium text-purple-700 mb-1">Tavsif</p>
                        <p class="text-sm text-purple-800">{{ department.description }}</p>
                    </div>
                </div>
            </div>

        </div>

        <DeleteModal :show="!!deleteTarget"
                     title="Kafedrani o'chirish"
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
    department:       Object,
    teachers_count:   { type: Number, default: 0 },
    directions_count: { type: Number, default: 0 },
})

const deleting     = ref(false)
const deleteTarget = ref(null)

function doDelete() {
    deleting.value = true
    router.delete(`/departments/${props.department.id}`, {
        onSuccess: () => { toast.success("Kafedra muvaffaqiyatli o'chirildi!"); router.visit('/departments') },
        onFinish:  () => { deleting.value = false; deleteTarget.value = null },
    })
}
</script>
