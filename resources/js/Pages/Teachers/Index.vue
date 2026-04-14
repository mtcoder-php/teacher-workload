<template>
    <AuthenticatedLayout>
        <template #header>O'qituvchilar</template>

        <div class="max-w-7xl mx-auto">
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">O'qituvchilar</h3>
                        <p class="text-sm text-gray-500 mt-1">Jami {{ teachers.total || 0 }} ta o'qituvchi</p>
                    </div>
                    <Link href="/teachers/create"
                          class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white
                                 rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Yangi O'qituvchi
                    </Link>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Qidiruv</label>
                        <input v-model="search" @input="filter" type="text" placeholder="F.I.Sh yoki email..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none"/>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kafedra</label>
                        <select v-model="deptId" @change="filter"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                            <option value="">Barchasi</option>
                            <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ish turi</label>
                        <select v-model="empType" @change="filter"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                            <option value="">Barchasi</option>
                            <option value="main_job">Asosiy</option>
                            <option value="internal_part_time">Ichki o'rindosh</option>
                            <option value="internal_additional">Ichki qo'shimcha</option>
                            <option value="external_part_time">Tashqi o'rindosh</option>
                            <option value="hourly">Soatbay</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button @click="reset" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm">Tozalash</button>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">O'qituvchi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Kafedra</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Lavozim</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Daraja</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Ish turi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Holat</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amallar</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="teacher in teachers.data" :key="teacher.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-semibold flex-shrink-0">
                                        {{ teacher.user?.name?.charAt(0)?.toUpperCase() ?? '?' }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ teacher.user?.name }}</div>
                                        <div class="text-sm text-gray-500">{{ teacher.user?.email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">{{ teacher.department?.name || '—' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">{{ teacher.position || '—' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">{{ teacher.academic_degree || '—' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full"
                                          :class="empBadge(teacher.employment_type)">
                                        {{ empLabel(teacher.employment_type) }}
                                    </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full"
                                          :class="teacher.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                        {{ teacher.is_active ? 'Faol' : 'Nofaol' }}
                                    </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <Link :href="`/teachers/${teacher.id}`" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Ko'rish">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </Link>
                                    <Link :href="`/teachers/${teacher.id}/edit`" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Tahrirlash">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </Link>
                                    <button @click="askDelete(teacher)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="O'chirish">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!teachers.data?.length">
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">O'qituvchi topilmadi</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="teachers.data?.length" class="px-6 py-4 border-t border-gray-200">
                    <Pagination v-if="teachers.total > 0" :meta="teachers" />
                </div>
            </div>
        </div>

        <DeleteModal :show="!!deleteTarget" title="O'qituvchini o'chirish"
                     :item-name="deleteTarget?.user?.name"
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

const props = defineProps({ teachers: Object, departments: Array, filters: Object })

const search   = ref(props.filters?.search || '')
const deptId   = ref(props.filters?.department_id || '')
const empType  = ref(props.filters?.employment_type || '')
const deleting = ref(false)
const deleteTarget = ref(null)

function filter() {
    router.get('/teachers', { search: search.value||undefined, department_id: deptId.value||undefined, employment_type: empType.value||undefined },
        { preserveState: true, replace: true })
}
function reset() { search.value=''; deptId.value=''; empType.value=''; filter() }
function askDelete(item) { deleteTarget.value = item }
function doDelete() {
    deleting.value = true
    router.delete(`/teachers/${deleteTarget.value.id}`, {
        onSuccess: () => {
            const flash = usePage().props.flash
            if (flash?.error) toast.error(flash.error)
            else toast.success("O'qituvchi muvaffaqiyatli o'chirildi!")
        },
        onError: () => toast.error("O'chirishda xatolik!"),
        onFinish: () => { deleting.value = false; deleteTarget.value = null },
    })
}
function empLabel(t) {
    return { main_job:"Asosiy", internal_part_time:"Ichki o'rindosh",
        internal_additional:"Ichki qo'sh.", external_part_time:"Tashqi", hourly:"Soatbay" }[t] ?? t
}
function empBadge(t) {
    return { main_job:"bg-green-100 text-green-800", internal_part_time:"bg-blue-100 text-blue-800",
        internal_additional:"bg-purple-100 text-purple-800", external_part_time:"bg-yellow-100 text-yellow-800",
        hourly:"bg-orange-100 text-orange-800" }[t] ?? "bg-gray-100 text-gray-700"
}
</script>
