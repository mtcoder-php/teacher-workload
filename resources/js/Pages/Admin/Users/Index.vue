<template>
    <AuthenticatedLayout>
        <template #header>Foydalanuvchilar Boshqaruvi</template>

        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Foydalanuvchilar</h3>
                        <p class="text-sm text-gray-500 mt-1">Tizim foydalanuvchilarini boshqarish</p>
                    </div>
                    <Link :href="route('admin.users.create')"
                          class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white
                                 rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Yangi Foydalanuvchi
                    </Link>
                </div>
            </div>

            <!-- Filterlar -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Qidiruv</label>
                        <input v-model="f.search" @input="applyFilters" type="text"
                               placeholder="Ism, email, telefon..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                                      focus:ring-2 focus:ring-indigo-500 outline-none"/>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
                        <select v-model="f.role_id" @change="applyFilters"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                                       focus:ring-2 focus:ring-indigo-500 outline-none">
                            <option value="">Barcha rollar</option>
                            <option v-for="role in roles" :key="role.id" :value="role.id">
                                {{ role.display_name }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Holat</label>
                        <select v-model="f.is_active" @change="applyFilters"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                                       focus:ring-2 focus:ring-indigo-500 outline-none">
                            <option value="">Barchasi</option>
                            <option value="true">Faol</option>
                            <option value="false">Nofaol</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button @click="resetFilters"
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm">
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Foydalanuvchi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Rol</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Telefon</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Holat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Qo'shilgan</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amallar</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center
                                                    justify-center text-indigo-600 font-semibold flex-shrink-0">
                                        {{ user.name?.charAt(0)?.toUpperCase() }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                                        <div class="text-sm text-gray-500">{{ user.email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full"
                                          :class="roleBadge(user.role?.name)">
                                        {{ user.role?.display_name || 'N/A' }}
                                    </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">
                                {{ user.phone || '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full"
                                          :class="user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                        {{ user.is_active ? 'Faol' : 'Nofaol' }}
                                    </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">
                                {{ formatDate(user.created_at) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <!-- Toggle holat -->
                                    <button @click="toggleStatus(user)"
                                            :disabled="user.id === $page.props.auth.user.id"
                                            class="p-2 rounded-lg transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
                                            :class="user.is_active ? 'text-yellow-600 hover:bg-yellow-50' : 'text-green-600 hover:bg-green-50'"
                                            :title="user.is_active ? 'Bloklash' : 'Faollashtirish'">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path v-if="user.is_active" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                        </svg>
                                    </button>
                                    <!-- Tahrirlash -->
                                    <Link :href="route('admin.users.edit', user.id)"
                                          class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                          title="Tahrirlash">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </Link>
                                    <!-- O'chirish -->
                                    <button @click="askDelete(user)"
                                            :disabled="user.id === $page.props.auth.user.id"
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
                                            title="O'chirish">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!users.data?.length">
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">
                                Foydalanuvchi topilmadi
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div v-if="users.data?.length" class="px-6 py-4 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-sm text-gray-600">
                            {{ users.from }}-{{ users.to }} / {{ users.total }} ta natija
                        </p>
                        <div class="flex gap-1">
                            <template v-for="(link, i) in users.links" :key="i">
                                <Link v-if="link.url" :href="link.url"
                                      :class="['px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                                               link.active ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200']"
                                      v-html="link.label"/>
                                <span v-else class="px-3 py-2 rounded-lg text-sm bg-gray-50 text-gray-400 cursor-not-allowed" v-html="link.label"/>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <DeleteModal :show="!!deleteTarget"
                     title="Foydalanuvchini o'chirish"
                     :item-name="deleteTarget?.name"
                     :loading="deleting"
                     @confirm="doDelete"
                     @cancel="deleteTarget = null"/>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import DeleteModal from '@/Components/DeleteModal.vue'

const route = window.route

const props = defineProps({ users: Object, roles: Array, filters: Object })

const f = reactive({
    search:    props.filters?.search    || '',
    role_id:   props.filters?.role_id   || '',
    is_active: props.filters?.is_active || '',
})

const deleting     = ref(false)
const deleteTarget = ref(null)

function applyFilters() {
    router.get(route('admin.users.index'), {
        search:    f.search    || undefined,
        role_id:   f.role_id   || undefined,
        is_active: f.is_active || undefined,
    }, { preserveState: true, preserveScroll: true })
}
function resetFilters() { f.search = ''; f.role_id = ''; f.is_active = ''; applyFilters() }

function toggleStatus(user) {
    router.post(route('admin.users.toggle-status', user.id), {}, { preserveScroll: true })
}

function askDelete(user) {
    if (user.id === usePage().props.auth.user.id) return
    deleteTarget.value = user
}
function doDelete() {
    deleting.value = true
    router.delete(route('admin.users.destroy', deleteTarget.value.id), {
        preserveScroll: true,
        onFinish: () => { deleting.value = false; deleteTarget.value = null },
    })
}

function roleBadge(name) {
    return {
        admin:           'bg-purple-100 text-purple-800',
        dekan:           'bg-blue-100 text-blue-800',
        'kafedra-mudiri':'bg-indigo-100 text-indigo-800',
        oqituvchi:       'bg-green-100 text-green-800',
        nazoratchi:      'bg-yellow-100 text-yellow-800',
    }[name] ?? 'bg-gray-100 text-gray-800'
}

function formatDate(d) {
    if (!d) return ''
    return new Date(d).toLocaleDateString('uz-UZ', { year:'numeric', month:'short', day:'numeric' })
}
</script>
