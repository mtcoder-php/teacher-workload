<template>
    <Head title="Hisobotlar" />
    <AuthenticatedLayout>
        <template #header>Hisobotlar</template>

        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Fakultet hisoboti -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="bg-purple-600 px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-white">Fakultet hisoboti</h3>
                        </div>
                        <p class="text-purple-100 text-sm mt-1">To'liq yuklama jadvali</p>
                    </div>
                    <div class="p-5 space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">O'quv yili</label>
                            <select v-model="facYearId"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 outline-none">
                                <option v-for="y in academicYears" :key="y.id" :value="y.id">
                                    {{ y.name }}{{ y.is_active ? ' (joriy)' : '' }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fakultet</label>
                            <select v-model="facId"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 outline-none">
                                <option value="">Fakultet tanlang</option>
                                <option v-for="f in faculties" :key="f.id" :value="f.id">{{ f.name }}</option>
                            </select>
                        </div>
                        <Link v-if="facId" :href="route('reports.faculty', facId) + '?academic_year_id=' + facYearId"
                              class="block w-full text-center px-4 py-2 bg-purple-600 text-white rounded-lg
                                     hover:bg-purple-700 transition-colors text-sm font-medium">
                            Ko'rish
                        </Link>
                        <button v-else disabled
                                class="w-full px-4 py-2 bg-gray-100 text-gray-400 rounded-lg text-sm cursor-not-allowed">
                            Ko'rish
                        </button>
                    </div>
                </div>

                <!-- Kafedra hisoboti -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="bg-green-600 px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-white">Kafedra hisoboti</h3>
                        </div>
                        <p class="text-green-100 text-sm mt-1">O'qituvchilar yuklamasi jadvali</p>
                    </div>
                    <div class="p-5 space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">O'quv yili</label>
                            <select v-model="deptYearId"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 outline-none">
                                <option v-for="y in academicYears" :key="y.id" :value="y.id">
                                    {{ y.name }}{{ y.is_active ? ' (joriy)' : '' }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kafedra</label>
                            <select v-model="deptId"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 outline-none">
                                <option value="">Kafedra tanlang</option>
                                <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
                            </select>
                        </div>
                        <Link v-if="deptId" :href="route('reports.department', deptId) + '?academic_year_id=' + deptYearId"
                              class="block w-full text-center px-4 py-2 bg-green-600 text-white rounded-lg
                                     hover:bg-green-700 transition-colors text-sm font-medium">
                            Ko'rish
                        </Link>
                        <button v-else disabled
                                class="w-full px-4 py-2 bg-gray-100 text-gray-400 rounded-lg text-sm cursor-not-allowed">
                            Ko'rish
                        </button>
                    </div>
                </div>

               <!-- O'qituvchi hisoboti -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="bg-blue-600 px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-white">O'qituvchi hisoboti</h3>
                        </div>
                        <p class="text-blue-100 text-sm mt-1">Individual yuklama hisoboti</p>
                    </div>
                    <div class="p-5 space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">O'quv yili</label>
                            <select v-model="teacherYearId"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                                <option v-for="y in academicYears" :key="y.id" :value="y.id">
                                    {{ y.name }}{{ y.is_active ? ' (joriy)' : '' }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">O'qituvchi</label>
                            <select v-model="teacherId"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                                <option value="">O'qituvchi tanlang</option>
                                <option v-for="t in teachers" :key="t.id" :value="t.id">{{ t.name }}</option>
                            </select>
                        </div>
                        <Link v-if="teacherId" :href="route('reports.teacher', teacherId) + '?academic_year_id=' + teacherYearId"
                              class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg
                                     hover:bg-blue-700 transition-colors text-sm font-medium">
                            Ko'rish
                        </Link>
                        <button v-else disabled
                                class="w-full px-4 py-2 bg-gray-100 text-gray-400 rounded-lg text-sm cursor-not-allowed">
                            Ko'rish
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue'
import {Head, Link} from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const route = window.route

const props = defineProps({
    academicYears: Array,
    activeYearId:  Number,
    departments:   Array,
    faculties:     Array,
    teachers:      Array,
})

const deptYearId    = ref(props.activeYearId)
const facYearId     = ref(props.activeYearId)
const teacherYearId = ref(props.activeYearId)
const deptId        = ref('')
const facId         = ref('')
const teacherId     = ref('')
</script>
