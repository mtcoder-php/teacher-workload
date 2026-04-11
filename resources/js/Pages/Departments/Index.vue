<template>
    <AuthenticatedLayout>
        <template #header>Kafedralar</template>

        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Kafedralar</h3>
                        <p class="text-sm text-gray-500 mt-1">Jami {{ departments.total || 0 }} ta kafedra</p>
                    </div>
                    <Link href="/departments/create"
                          class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white
                                 rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Yangi Kafedra
                    </Link>
                </div>
            </div>

            <!-- Filter -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Qidiruv</label>
                        <input v-model="search" @input="filter" type="text" placeholder="Kafedra nomi..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none"/>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fakultet</label>
                        <select v-model="facultyId" @change="filter"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                            <option value="">Barchasi</option>
                            <option v-for="f in faculties" :key="f.id" :value="f.id">{{ f.name }}</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button @click="reset" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                            Tozalash
                        </button>
                    </div>
                </div>
            </div>

            <!-- Jadval -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kafedra</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Kod</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Fakultet</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Mudir</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">O'qituvchilar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Holat</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amallar</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="dept in departments.data" :key="dept.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 font-semibold flex-shrink-0">
                                        {{ dept.name?.charAt(0)?.toUpperCase() }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ dept.name }}</div>
                                        <div class="text-sm text-gray-500 md:hidden">{{ dept.faculty?.name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">{{ dept.code }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">{{ dept.faculty?.name || '—' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">{{ dept.head?.name || '—' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">{{ dept.teachers_count || 0 }} ta</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full"
                                          :class="dept.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                        {{ dept.is_active ? 'Faol' : 'Nofaol' }}
                                    </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <Link :href="`/departments/${dept.id}`"
                                          class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Ko'rish">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </Link>
                                    <Link :href="`/departments/${dept.id}/edit`"
                                          class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Tahrirlash">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </Link>
                                    <button @click="askDelete(dept)"
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="O'chirish">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!departments.data?.length">
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">Kafedra topilmadi</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="departments.data?.length" class="px-6 py-4 border-t border-gray-200">
                    <Pagination v-if="departments.total > 0" :meta="departments" />
                </div>
            </div>
        </div>

        <DeleteModal :show="!!deleteTarget" title="Kafedrani o'chirish" :item-name="deleteTarget?.name"
                     :loading="deleting" @confirm="doDelete" @cancel="deleteTarget = null"/>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import DeleteModal from '@/Components/DeleteModal.vue'

const props = defineProps({ departments: Object, faculties: Array, filters: Object })

const search    = ref(props.filters?.search || '')
const facultyId = ref(props.filters?.faculty_id || '')
const deleting  = ref(false)
const deleteTarget = ref(null)

function filter() {
    router.get('/departments', { search: search.value || undefined, faculty_id: facultyId.value || undefined },
        { preserveState: true, replace: true })
}
function reset() { search.value = ''; facultyId.value = ''; filter() }
function askDelete(item) { deleteTarget.value = item }
function doDelete() {
    deleting.value = true
    router.delete(`/departments/${deleteTarget.value.id}`, {
        preserveScroll: true,
        onFinish: () => { deleting.value = false; deleteTarget.value = null },
    })
}
</script>
