<template>
    <AuthenticatedLayout>
        <template #header>O'quv Yillari</template>

        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">O'quv yillari</h3>
                        <p class="text-sm text-gray-500 mt-1">Jami {{ academicYears.total || 0 }} ta o'quv yili</p>
                    </div>
                    <Link href="/academic-years/create"
                          class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white
                                 rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Yangi O'quv Yili
                    </Link>
                </div>
            </div>

            <!-- Kartalar -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="year in academicYears.data" :key="year.id"
                     class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                    <!-- Karta header -->
                    <div class="px-6 py-4 text-white"
                         :class="year.is_active ? 'bg-indigo-600' : 'bg-gray-500'">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-bold">{{ year.name }}</h3>
                            <span v-if="year.is_active"
                                  class="px-2.5 py-1 bg-white/20 rounded-full text-xs font-semibold">
                                Joriy
                            </span>
                        </div>
                    </div>

                    <!-- Karta body -->
                    <div class="p-5 space-y-3">
                        <div class="flex items-center text-sm text-gray-600 gap-2">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ formatDate(year.start_date) }} — {{ formatDate(year.end_date) }}
                        </div>

                        <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                            <Link :href="`/academic-years/${year.id}`"
                                  class="text-sm font-medium text-indigo-600 hover:text-indigo-700">
                                Batafsil →
                            </Link>
                            <div class="flex items-center gap-1">
                                <Link :href="`/academic-years/${year.id}/edit`"
                                      class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                      title="Tahrirlash">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </Link>
                                <button v-if="!year.is_active"
                                        @click="setActive(year)"
                                        class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                        title="Joriy qilish">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </button>
                                <button v-if="!year.is_active"
                                        @click="askDelete(year)"
                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                        title="O'chirish">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bo'sh holat -->
                <div v-if="!academicYears.data?.length" class="col-span-full">
                    <p class="text-center text-sm text-gray-400 py-8">O'quv yili topilmadi</p>
                </div>
            </div>

            <!-- Pagination -->
            <Pagination v-if="academicYears.total > 0" :meta="academicYears" />
        </div>

        <DeleteModal :show="!!deleteTarget" title="O'quv yilini o'chirish"
                     :item-name="deleteTarget?.name"
                     :loading="deleting" @confirm="doDelete" @cancel="deleteTarget = null"/>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import { useToast } from '@/Composables/useToast'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import DeleteModal from '@/Components/DeleteModal.vue'

const toast = useToast()

const props = defineProps({ academicYears: Object })

const deleting     = ref(false)
const deleteTarget = ref(null)

function formatDate(date) {
    if (!date) return ''
    const [y, m, d] = date.split('T')[0].split('-')
    return `${d}.${m}.${y}`
}
function askDelete(item) { deleteTarget.value = item }
function doDelete() {
    deleting.value = true
    router.delete(`/academic-years/${deleteTarget.value.id}`, {
        onSuccess: () => {
            const flash = usePage().props.flash
            if (flash?.error) toast.error(flash.error)
            else toast.success("O'quv yili muvaffaqiyatli o'chirildi!")
        },
        onError: () => toast.error("O'chirishda xatolik!"),
        onFinish: () => { deleting.value = false; deleteTarget.value = null },
    })
}
function setActive(year) {
    router.post(`/academic-years/${year.id}/set-current`, {}, {
        preserveScroll: true,
    })
}
</script>
