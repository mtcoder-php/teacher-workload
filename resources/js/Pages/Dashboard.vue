<template>
    <AuthenticatedLayout>
        <template #header>Dashboard</template>

        <div class="space-y-6">

            <!-- ── Yuqori qator: o'quv yili + foydalanuvchi ────────────────── -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">
                        {{ greeting }}, <span class="text-indigo-600">{{ user.name }}</span>
                    </h2>
                    <p class="text-sm text-gray-500 mt-0.5">
                        {{ roleLabel }}
                        <span v-if="context" class="text-gray-400"> · {{ context }}</span>
                    </p>
                </div>
                <div v-if="activeYear"
                     class="inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-50
                            border border-indigo-200 rounded-lg text-sm text-indigo-700 font-medium self-start">
                    <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                    {{ activeYear }} o'quv yili
                </div>
            </div>

            <!-- ── Admin/Dekan uchun umumiy statistika ─────────────────────── -->
            <template v-if="role === 'admin'">
                <!-- Asosiy raqamlar -->
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                    <StatCard label="Fakultetlar"   :value="stats.faculties_count"   color="indigo" icon="faculty"/>
                    <StatCard label="Kafedralar"    :value="stats.departments_count" color="purple" icon="dept"/>
                    <StatCard label="O'qituvchilar" :value="stats.teachers_count"    color="blue"   icon="teacher"/>
                    <StatCard label="Guruhlar"      :value="stats.groups_count"      color="green"  icon="group"/>
                    <StatCard label="Fanlar"        :value="stats.subjects_count"    color="amber"  icon="subject"/>
                </div>
                <!-- Yuklama holati -->
                <WorkloadStatusBar :stats="stats"/>
            </template>

            <template v-else-if="role === 'dekan'">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <StatCard label="Kafedralar"    :value="stats.departments_count" color="purple" icon="dept"/>
                    <StatCard label="O'qituvchilar" :value="stats.teachers_count"    color="blue"   icon="teacher"/>
                    <StatCard label="Fanlar"        :value="stats.subjects_count"    color="amber"  icon="subject"/>
                </div>
                <WorkloadStatusBar :stats="stats"/>
            </template>

            <template v-else-if="role === 'kafedra_mudiri'">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <StatCard label="O'qituvchilar" :value="stats.teachers_count"   color="blue"   icon="teacher"/>
                    <StatCard label="Yo'nalishlar"  :value="stats.directions_count" color="indigo" icon="direction"/>
                    <StatCard label="Fanlar"        :value="stats.subjects_count"   color="amber"  icon="subject"/>
                    <StatCard label="Jami yuklama"  :value="stats.workloads_total"  color="purple" icon="workload"/>
                </div>
                <WorkloadStatusBar :stats="stats"/>
            </template>

            <template v-else-if="role === 'oqituvchi'">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <StatCard label="Jami soat"     :value="stats.total_hours"       color="blue"   icon="hours"/>
                    <StatCard label="Yuklama"        :value="stats.workloads_total"   color="indigo" icon="workload"/>
                    <StatCard label="Tasdiqlangan"   :value="stats.workloads_confirmed" color="green" icon="confirmed"/>
                    <StatCard label="Tekshiruvda"    :value="stats.workloads_pending"   color="amber" icon="pending"/>
                </div>
            </template>

            <!-- ── So'nggi yuklamalar ───────────────────────────────────────── -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900">So'nggi yuklamalar</h3>
                    <Link href="/workloads"
                          class="text-xs font-medium text-indigo-600 hover:text-indigo-700">
                        Barchasi →
                    </Link>
                </div>

                <div v-if="recentActivities.length" class="divide-y divide-gray-50">
                    <div v-for="item in recentActivities" :key="item.id"
                         class="flex items-center gap-4 px-6 py-3.5 hover:bg-gray-50 transition-colors">
                        <!-- Avatar -->
                        <div class="h-9 w-9 flex-shrink-0 rounded-full bg-indigo-100
                                    flex items-center justify-center text-indigo-600 font-semibold text-sm">
                            {{ item.teacher?.charAt(0)?.toUpperCase() ?? '?' }}
                        </div>
                        <!-- Ma'lumot -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ item.subject }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ item.teacher }}</p>
                        </div>
                        <!-- Soat -->
                        <div class="text-right flex-shrink-0 hidden sm:block">
                            <p class="text-sm font-semibold text-gray-700">{{ item.total_hours }} soat</p>
                            <p class="text-xs text-gray-400">{{ item.time }}</p>
                        </div>
                        <!-- Status badge -->
                        <span class="flex-shrink-0 px-2 py-1 text-xs font-medium rounded-full"
                              :class="statusClass(item.status)">
                            {{ statusLabel(item.status) }}
                        </span>
                    </div>
                </div>

                <div v-else class="px-6 py-12 text-center text-sm text-gray-400">
                    Hozircha yuklama yo'q
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

// ─── Inline komponentlar ────────────────────────────────────────────────────

// StatCard
const StatCard = {
    props: { label: String, value: [Number, String], color: String, icon: String },
    setup(props) {
        const colors = {
            indigo: 'bg-indigo-50 text-indigo-600',
            purple: 'bg-purple-50 text-purple-600',
            blue:   'bg-blue-50   text-blue-600',
            green:  'bg-green-50  text-green-600',
            amber:  'bg-amber-50  text-amber-600',
        }
        const iconPaths = {
            faculty:   'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
            dept:      'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5',
            teacher:   'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
            group:     'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
            subject:   'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253',
            workload:  'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
            direction: 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276',
            hours:     'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
            confirmed: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            pending:   'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        }
        return { colorClass: colors[props.color] ?? colors.indigo, path: iconPaths[props.icon] ?? iconPaths.workload }
    },
    template: `
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 mb-1">{{ label }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ value ?? 0 }}</p>
                </div>
                <div :class="['w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0', colorClass]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="path"/>
                    </svg>
                </div>
            </div>
        </div>
    `
}

// WorkloadStatusBar
const WorkloadStatusBar = {
    props: { stats: Object },
    setup(props) {
        const total = computed(() => props.stats.workloads_total || 1)
        const pct   = (n) => Math.round(((n || 0) / total.value) * 100)
        return { total, pct }
    },
    template: `
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-900">Yuklama holati</h3>
                <span class="text-sm font-bold text-gray-700">{{ stats.workloads_total ?? 0 }} ta</span>
            </div>
            <!-- Progress bar -->
            <div class="flex h-3 rounded-full overflow-hidden bg-gray-100 mb-4">
                <div :style="{ width: pct(stats.workloads_confirmed) + '%' }"
                     class="bg-green-500 transition-all duration-700"></div>
                <div :style="{ width: pct(stats.workloads_pending) + '%' }"
                     class="bg-amber-400 transition-all duration-700"></div>
                <div :style="{ width: pct(stats.workloads_draft) + '%' }"
                     class="bg-gray-300 transition-all duration-700"></div>
            </div>
            <!-- Legend -->
            <div class="flex flex-wrap gap-4 text-xs">
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-green-500 flex-shrink-0"></span>
                    <span class="text-gray-600">Tasdiqlangan</span>
                    <span class="font-semibold text-gray-800">{{ stats.workloads_confirmed ?? 0 }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400 flex-shrink-0"></span>
                    <span class="text-gray-600">Tekshiruvda</span>
                    <span class="font-semibold text-gray-800">{{ stats.workloads_pending ?? 0 }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-gray-300 flex-shrink-0"></span>
                    <span class="text-gray-600">Qoralama</span>
                    <span class="font-semibold text-gray-800">{{ stats.workloads_draft ?? 0 }}</span>
                </div>
            </div>
        </div>
    `
}

// ─── Props ───────────────────────────────────────────────────────────────────
const props = defineProps({
    stats:             { type: Object, default: () => ({}) },
    recentActivities:  { type: Array,  default: () => [] },
    activeYear:        { type: String, default: null },
    role:              { type: String, default: 'default' },
    context:           { type: String, default: null },
})

const user = usePage().props.auth.user

// Salomlashuv vaqtga qarab
const greeting = computed(() => {
    const h = new Date().getHours()
    if (h < 6)  return 'Xayrli tun'
    if (h < 12) return 'Xayrli tong'
    if (h < 18) return 'Xayrli kun'
    return 'Xayrli kech'
})

const roleLabel = computed(() => ({
    admin:           'Administrator',
    dekan:           'Dekan',
    kafedra_mudiri:  'Kafedra mudiri',
    oqituvchi:       "O'qituvchi",
    default:         'Foydalanuvchi',
})[props.role] ?? props.role)

// Status
function statusClass(s) {
    return {
        confirmed: 'bg-green-100 text-green-700',
        pending:   'bg-amber-100 text-amber-700',
        draft:     'bg-gray-100  text-gray-600',
        rejected:  'bg-red-100   text-red-700',
    }[s] ?? 'bg-gray-100 text-gray-600'
}
function statusLabel(s) {
    return { confirmed:'Tasdiqlangan', pending:'Tekshiruvda', draft:'Qoralama', rejected:"Rad etilgan" }[s] ?? s
}
</script>
