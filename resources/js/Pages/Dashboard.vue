<template>
    <AuthenticatedLayout>
        <template #header>Dashboard</template>

        <div class="max-w-7xl mx-auto space-y-6">

            <!-- ── Salomlashuv ────────────────────────────────────────────── -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-indigo-600 rounded-full flex items-center justify-center
                                    text-white text-2xl font-bold flex-shrink-0">
                            {{ user.name?.charAt(0)?.toUpperCase() }}
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900">
                                {{ greeting }},
                                <span class="text-indigo-600">{{ user.name }}</span>
                            </h2>
                            <p class="text-sm text-gray-500 mt-0.5">
                                {{ roleLabel }}
                                <span v-if="context" class="text-gray-400"> · {{ context }}</span>
                            </p>
                        </div>
                    </div>
                    <div v-if="activeYear"
                         class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-50
                                border border-indigo-200 rounded-lg self-start sm:self-auto">
                        <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse flex-shrink-0"></span>
                        <span class="text-sm font-medium text-indigo-700">{{ activeYear }} o'quv yili</span>
                    </div>
                </div>
            </div>

            <!-- ── Statistika kartalar ─────────────────────────────────────── -->
            <div :class="[
                'grid gap-4',
                role === 'admin' ? 'grid-cols-2 sm:grid-cols-3 lg:grid-cols-5' :
                role === 'dekan' ? 'grid-cols-2 sm:grid-cols-3' :
                'grid-cols-2 sm:grid-cols-4'
            ]">
                <div v-for="card in currentCards" :key="card.label"
                     class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">{{ card.label }}</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ card.value }}</p>
                        </div>
                        <Icon :size="44" :class="card.color">
                            <component :is="card.icon" />
                        </Icon>
                    </div>
                </div>
            </div>

            <!-- ── Yuklama holati (admin, dekan, mudир) ───────────────────── -->
            <div v-if="role !== 'oqituvchi'" class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <Icon :size="22" class="text-indigo-600"><BarChartOutline /></Icon>
                        Yuklama holati
                    </div>
                    <span class="text-sm font-normal text-gray-500">
                        Jami: {{ stats.workloads_total ?? 0 }} ta
                    </span>
                </h3>
                <!-- Progress bar -->
                <div class="w-full h-3 bg-gray-200 rounded-full overflow-hidden mb-5">
                    <div class="h-full flex">
                        <div :style="{ width: pct(stats.workloads_confirmed) + '%' }"
                             class="bg-green-500 transition-all duration-700"></div>
                        <div :style="{ width: pct(stats.workloads_pending) + '%' }"
                             class="bg-amber-400 transition-all duration-700"></div>
                        <div :style="{ width: pct(stats.workloads_draft) + '%' }"
                             class="bg-gray-300 transition-all duration-700"></div>
                    </div>
                </div>
                <!-- Legend -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-green-500 flex-shrink-0"></span>
                            <span class="text-sm text-gray-700">Tasdiqlangan</span>
                        </div>
                        <span class="text-lg font-bold text-gray-900">{{ stats.workloads_confirmed ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-amber-400 flex-shrink-0"></span>
                            <span class="text-sm text-gray-700">Tekshiruvda</span>
                        </div>
                        <span class="text-lg font-bold text-gray-900">{{ stats.workloads_pending ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-gray-300 flex-shrink-0"></span>
                            <span class="text-sm text-gray-700">Qoralama</span>
                        </div>
                        <span class="text-lg font-bold text-gray-900">{{ stats.workloads_draft ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <!-- ── So'nggi yuklamalar — karta grid ────────────────────────── -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <Icon :size="22" class="text-indigo-600"><DocumentTextOutline /></Icon>
                        So'nggi yuklamalar
                    </h3>
                    <Link href="/workloads"
                          class="text-sm font-medium text-indigo-600 hover:text-indigo-700">
                        Barchasini ko'rish →
                    </Link>
                </div>

                <!-- 5 ta karta grid -->
                <div v-if="recentActivities.length"
                     class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    <Link v-for="item in recentActivities.slice(0, 5)" :key="item.id"
                          :href="`/workloads/${item.id}`"
                          class="bg-white rounded-lg shadow-sm p-5 hover:shadow-md
                                 transition-shadow block group">
                        <!-- Status strip top -->
                        <div class="h-1 rounded-full mb-4"
                             :class="{
                                 'bg-green-400': item.status === 'confirmed',
                                 'bg-amber-400': item.status === 'pending',
                                 'bg-gray-300':  item.status === 'draft',
                                 'bg-red-400':   item.status === 'rejected',
                             }"></div>
                        <!-- Avatar + ism -->
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-9 h-9 bg-indigo-100 rounded-full flex items-center justify-center
                                        text-indigo-600 font-semibold text-sm flex-shrink-0
                                        group-hover:bg-indigo-200 transition-colors">
                                {{ item.teacher?.charAt(0)?.toUpperCase() ?? '?' }}
                            </div>
                            <p class="text-xs text-gray-500 truncate">{{ item.teacher }}</p>
                        </div>
                        <!-- Fan nomi -->
                        <p class="text-sm font-semibold text-gray-900 line-clamp-2 mb-3 leading-snug">
                            {{ item.subject }}
                        </p>
                        <!-- Pastki qator: soat + status -->
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400">{{ item.time }}</span>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium"
                                  :class="{
                                      'bg-green-100 text-green-700': item.status === 'confirmed',
                                      'bg-amber-100 text-amber-700': item.status === 'pending',
                                      'bg-gray-100  text-gray-600':  item.status === 'draft',
                                      'bg-red-100   text-red-700':   item.status === 'rejected',
                                  }">
                                {{ statusLabels[item.status] ?? item.status }}
                            </span>
                        </div>
                        <!-- Soat -->
                        <p class="text-xl font-bold text-gray-900 mt-3">
                            {{ item.total_hours }}
                            <span class="text-xs font-normal text-gray-400">soat</span>
                        </p>
                    </Link>
                </div>

                <!-- Bo'sh holat -->
                <div v-else class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <Icon :size="56" class="text-gray-300 mx-auto mb-3"><DocumentOutline /></Icon>
                    <p class="text-sm text-gray-500">Hozircha yuklama yo'q</p>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { Icon } from '@vicons/utils'
import {
    BusinessOutline,
    LibraryOutline,
    PeopleOutline,
    BookOutline,
    DocumentTextOutline,
    DocumentOutline,
    TimeOutline,
    CheckmarkCircleOutline,
    CompassOutline,
    GridOutline,
    BarChartOutline,
} from '@vicons/ionicons5'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    stats:            { type: Object, default: () => ({}) },
    recentActivities: { type: Array,  default: () => [] },
    activeYear:       { type: String, default: null },
    role:             { type: String, default: 'default' },
    context:          { type: String, default: null },
})

const user = usePage().props.auth.user

// ── Salomlashuv ───────────────────────────────────────────────────────────────
const greeting = computed(() => {
    const h = new Date().getHours()
    if (h < 6)  return 'Xayrli tun'
    if (h < 12) return 'Xayrli tong'
    if (h < 18) return 'Xayrli kun'
    return 'Xayrli kech'
})

const roleLabel = computed(() => ({
    admin:          'Administrator',
    dekan:          'Dekan',
    kafedra_mudiri: 'Kafedra mudiri',
    oqituvchi:      "O'qituvchi",
})[props.role] ?? props.role)

// ── Kartalar ──────────────────────────────────────────────────────────────────
const allCards = {
    admin: [
        { label: 'Fakultetlar',   key: 'faculties_count',   icon: BusinessOutline,     color: 'text-indigo-500' },
        { label: 'Kafedralar',    key: 'departments_count', icon: LibraryOutline,       color: 'text-purple-500' },
        { label: "O'qituvchilar", key: 'teachers_count',    icon: PeopleOutline,        color: 'text-blue-500'   },
        { label: 'Guruhlar',      key: 'groups_count',      icon: GridOutline,          color: 'text-green-500'  },
        { label: 'Fanlar',        key: 'subjects_count',    icon: BookOutline,          color: 'text-amber-500'  },
    ],
    dekan: [
        { label: 'Kafedralar',    key: 'departments_count', icon: LibraryOutline,  color: 'text-purple-500' },
        { label: "O'qituvchilar", key: 'teachers_count',    icon: PeopleOutline,   color: 'text-blue-500'   },
        { label: 'Fanlar',        key: 'subjects_count',    icon: BookOutline,     color: 'text-amber-500'  },
    ],
    kafedra_mudiri: [
        { label: "O'qituvchilar", key: 'teachers_count',    icon: PeopleOutline,       color: 'text-blue-500'   },
        { label: "Yo'nalishlar",  key: 'directions_count',  icon: CompassOutline,       color: 'text-indigo-500' },
        { label: 'Fanlar',        key: 'subjects_count',    icon: BookOutline,          color: 'text-amber-500'  },
        { label: 'Jami yuklama',  key: 'workloads_total',   icon: DocumentTextOutline,  color: 'text-purple-500' },
    ],
    oqituvchi: [
        { label: 'Jami soat',    key: 'total_hours',         icon: TimeOutline,            color: 'text-blue-500'  },
        { label: 'Yuklama',      key: 'workloads_total',     icon: DocumentTextOutline,    color: 'text-indigo-500'},
        { label: 'Tasdiqlangan', key: 'workloads_confirmed', icon: CheckmarkCircleOutline, color: 'text-green-500' },
        { label: 'Tekshiruvda',  key: 'workloads_pending',   icon: TimeOutline,            color: 'text-amber-500' },
    ],
}

const currentCards = computed(() =>
    (allCards[props.role] ?? []).map(c => ({ ...c, value: props.stats[c.key] ?? 0 }))
)

// ── Progress bar ──────────────────────────────────────────────────────────────
const pct = (n) => {
    const total = Math.max(props.stats?.workloads_total || 0, 1)
    return Math.min(Math.round(((n || 0) / total) * 100), 100)
}

// ── Status ────────────────────────────────────────────────────────────────────
const statusLabels = {
    confirmed: 'Tasdiqlangan',
    pending:   'Tekshiruvda',
    draft:     'Qoralama',
    rejected:  'Rad etilgan',
}
</script>
