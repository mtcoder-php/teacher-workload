<template>
    <div class="space-y-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Guruhlarni tanlang</h3>
            <p class="text-sm text-gray-500 mt-1">
                <span v-if="local.is_potok">Potok uchun 2–8 ta guruh tanlang (bir xil kurs)</span>
                <span v-else>Bir ta guruh tanlang</span>
            </p>
        </div>

        <!-- Xato xabarlar -->
        <div v-if="validationError" class="p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-600 flex items-start gap-2">
            <span>⚠️</span>
            <span>{{ validationError }}</span>
        </div>

        <!-- Statistika -->
        <div v-if="local.group_ids.length" class="flex flex-wrap gap-3">
            <div class="stat-pill bg-blue-50 text-blue-700">
                <span>👥</span>
                <span>{{ local.group_ids.length }} ta guruh</span>
            </div>
            <div class="stat-pill bg-green-50 text-green-700">
                <span>🎓</span>
                <span>Jami: {{ totalStudents }} talaba</span>
            </div>
        </div>

        <!-- Qidiruv -->
        <div class="relative">
            <input
                v-model="groupSearch"
                type="text"
                placeholder="Guruh qidirish (nomi yoki kodi)..."
                class="form-input pl-9"
            />
            <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 111 11a6 6 0 0116 0z"/>
            </svg>
        </div>

        <!-- Guruhlar soni haqida ogohlantirish (potok) -->
        <div v-if="local.is_potok" class="flex gap-3 text-xs text-gray-500 px-1">
            <span>Min: 2 ta</span>
            <span class="text-gray-300">|</span>
            <span>Max: 8 ta</span>
            <span class="text-gray-300">|</span>
            <span>Tanlangan: <strong :class="countColor">{{ local.group_ids.length }}</strong> ta</span>
        </div>

        <!-- Guruhlar jadvali -->
        <div class="border border-gray-200 rounded-xl overflow-hidden">
            <div class="max-h-64 overflow-y-auto divide-y divide-gray-100">
                <div v-if="filteredGroups.length === 0" class="p-6 text-sm text-gray-400 text-center">
                    <span>Guruh topilmadi</span>
                </div>

                <label
                    v-for="g in filteredGroups"
                    :key="g.id"
                    class="flex items-center gap-3 px-4 py-3 cursor-pointer hover:bg-gray-50 transition-colors"
                    :class="{
                        'bg-blue-50 hover:bg-blue-50': local.group_ids.includes(g.id),
                        'opacity-50 cursor-not-allowed': isGroupDisabled(g),
                    }"
                >
                    <input
                        type="checkbox"
                        :value="g.id"
                        :disabled="isGroupDisabled(g)"
                        v-model="local.group_ids"
                        class="w-4 h-4 text-blue-600 rounded border-gray-300"
                    />
                    <div class="flex-1 flex items-center justify-between">
                        <div>
                            <span class="text-sm font-medium text-gray-800">{{ g.name }}</span>
                            <span class="ml-2 text-xs text-gray-400">{{ g.code }}</span>
                            <span class="ml-2 text-xs px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full">
                                {{ g.course }}-kurs
                            </span>
                        </div>
                        <div class="text-xs text-gray-500 flex items-center gap-1">
                            <span>👤</span>
                            <span>{{ g.student_count }}</span>
                        </div>
                    </div>
                </label>
            </div>
        </div>

        <!-- Tanlangan guruhlar (card view) -->
        <div v-if="local.group_ids.length" class="space-y-2">
            <h4 class="text-sm font-medium text-gray-700">Tanlangan guruhlar:</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <div
                    v-for="id in local.group_ids"
                    :key="id"
                    class="flex items-center justify-between px-3 py-2 bg-blue-50 border border-blue-100 rounded-lg"
                >
                    <div>
                        <span class="text-sm font-medium text-blue-800">{{ groupName(id) }}</span>
                        <span class="ml-2 text-xs text-blue-500">{{ groupStudents(id) }} talaba</span>
                    </div>
                    <button type="button" @click="removeGroup(id)" class="text-blue-400 hover:text-red-500 transition-colors text-lg leading-none">
                        ×
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
    modelValue: { type: Object, required: true },
    groups:     { type: Array, default: () => [] },
})
const emit = defineEmits(['update:modelValue', 'valid'])

const local = computed({
    get: () => props.modelValue,
    set: (v) => emit('update:modelValue', v),
})

const groupSearch     = ref('')
const validationError = ref('')

// Yo'nalish va kursga mos guruhlar
const relevantGroups = computed(() => {
    const dirIds  = local.value.direction_ids ?? []
    const course  = local.value.course
    return props.groups.filter(g =>
        dirIds.includes(g.direction_id) && g.course === course
    )
})

const filteredGroups = computed(() => {
    const q = groupSearch.value.toLowerCase()
    if (!q) return relevantGroups.value
    return relevantGroups.value.filter(g =>
        g.name.toLowerCase().includes(q) || (g.code ?? '').toLowerCase().includes(q)
    )
})

const totalStudents = computed(() =>
    local.value.group_ids.reduce((sum, id) => {
        const g = props.groups.find(g => g.id === id)
        return sum + (g?.student_count ?? 0)
    }, 0)
)

const countColor = computed(() => {
    if (!local.value.is_potok) return 'text-gray-700'
    const n = local.value.group_ids.length
    if (n < 2) return 'text-red-600'
    if (n > 8) return 'text-red-600'
    return 'text-green-600'
})

// Potoksiz holatda bitta tanlangandan keyin qolganlarni disable qilish
function isGroupDisabled(g) {
    if (local.value.is_potok) return local.value.group_ids.length >= 8 && !local.value.group_ids.includes(g.id)
    return local.value.group_ids.length >= 1 && !local.value.group_ids.includes(g.id)
}

function groupName(id) {
    return props.groups.find(g => g.id === id)?.name ?? id
}
function groupStudents(id) {
    return props.groups.find(g => g.id === id)?.student_count ?? 0
}
function removeGroup(id) {
    local.value.group_ids = local.value.group_ids.filter(x => x !== id)
}

// Validatsiya
watch(
    () => [local.value.group_ids, local.value.is_potok],
    () => {
        validationError.value = ''
        const n = local.value.group_ids.length

        if (n === 0) {
            validationError.value = 'Kamida 1 ta guruh tanlash shart'
            emit('valid', false); return
        }
        if (!local.value.is_potok && n > 1) {
            validationError.value = 'Potoksiz holatda faqat 1 ta guruh tanlash mumkin'
            emit('valid', false); return
        }
        if (local.value.is_potok && n < 2) {
            validationError.value = 'Potokli holatda kamida 2 ta guruh tanlang'
            emit('valid', false); return
        }
        if (local.value.is_potok && n > 8) {
            validationError.value = 'Maksimal 8 ta guruh tanlash mumkin'
            emit('valid', false); return
        }

        emit('valid', true)
    },
    { immediate: true, deep: true }
)
</script>

<style scoped>
.form-input { @apply w-full rounded-lg border border-gray-300 text-sm px-3 py-2 focus:ring-2 focus:ring-blue-300 focus:border-blue-400 outline-none transition; }
.stat-pill  { @apply flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-full; }
</style>
