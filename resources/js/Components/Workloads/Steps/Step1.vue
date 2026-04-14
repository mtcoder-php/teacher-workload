<template>
    <div class="space-y-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Kafedra, Yo'nalish va Guruhlar</h3>
            <p class="text-sm text-gray-500 mt-1">Kafedrani tanlang, so'ng yo'nalish va guruhlarni belgilang</p>
        </div>

        <!-- ① Kafedra -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Kafedra <span class="text-red-500">*</span>
            </label>
            <select
                v-model="local.department_id"
                :disabled="isKafedraMudiri"
                class="w-full rounded-lg border border-gray-300 text-sm px-3 py-2 outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400 bg-white transition disabled:bg-gray-50 disabled:text-gray-500"
                :class="{ 'border-red-400': errors.department_id }"
            >
                <option :value="null" disabled>— Kafedrani tanlang —</option>
                <option v-for="d in departments" :key="d.id" :value="d.id">
                    {{ d.faculty?.name ? d.faculty.name + ' / ' : '' }}{{ d.name }}
                </option>
            </select>
            <p v-if="isKafedraMudiri" class="text-xs text-amber-600 mt-1">
                🔒 Siz faqat o'z kafedrangizga yuklama qo'sha olasiz
            </p>
            <p v-if="errors.department_id" class="text-xs text-red-500 mt-1">{{ errors.department_id }}</p>
        </div>

        <!-- ② Yuklama turi -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Yuklama turi <span class="text-red-500">*</span>
            </label>
            <div class="grid grid-cols-2 gap-3">
                <button
                    type="button"
                    @click="setPotok(false)"
                    :class="cardClass(!local.is_potok)"
                >
                    <span class="text-xl">👤</span>
                    <div class="text-left">
                        <div class="font-medium text-sm">Potoksiz</div>
                        <div class="text-xs text-gray-500 mt-0.5">Bitta guruh</div>
                    </div>
                </button>
                <button
                    type="button"
                    @click="setPotok(true)"
                    :class="cardClass(local.is_potok)"
                >
                    <span class="text-xl">👥</span>
                    <div class="text-left">
                        <div class="font-medium text-sm">Potokli</div>
                        <div class="text-xs text-gray-500 mt-0.5">2–8 guruh</div>
                    </div>
                </button>
            </div>
        </div>

        <!-- ③ Yo'nalishlar — CheckboxSelect -->
        <div v-if="local.department_id">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Yo'nalish(lar) <span class="text-red-500">*</span>
            </label>
            <p class="text-xs text-gray-500 mb-2">
                Tanlangan yo'nalishga doir guruhlar avtomatik chiqadi
            </p>
            <CheckboxSelect
                v-model="local.direction_ids"
                :items="directionItems"
                placeholder="Yo'nalishni tanlang..."
                search-placeholder="Yo'nalish qidirish..."
                tag-class="bg-indigo-100 text-indigo-700"
                :has-error="!!errors.direction_ids"
                :show-select-all="local.is_potok"
            />
            <p v-if="errors.direction_ids" class="text-xs text-red-500 mt-1">{{ errors.direction_ids }}</p>
        </div>

        <!-- ④ Kurs — yo'nalish tanlangandan keyin -->
        <div v-if="local.direction_ids.length">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Kurs <span class="text-red-500">*</span>
            </label>

            <!-- Kurs tugmalari -->
            <div class="flex flex-wrap gap-2">
                <button
                    v-for="c in availableCourses"
                    :key="c.value"
                    type="button"
                    @click="local.course = c.value"
                    :class="[
            'px-4 py-2 rounded-lg text-sm font-medium border-2 transition-colors',
            local.course === c.value
              ? 'border-blue-500 bg-blue-50 text-blue-700'
              : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300'
          ]"
                >
                    {{ c.label }}
                </button>
            </div>

            <p v-if="errors.course" class="text-xs text-red-500 mt-1">{{ errors.course }}</p>
        </div>

        <!-- ⑤ Guruhlar — kurs tanlangandan keyin, yo'nalishga doir avtomatik -->
        <div v-if="local.course && local.direction_ids.length">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Guruh(lar) <span class="text-red-500">*</span>
            </label>
            <p class="text-xs text-gray-500 mb-2">
                <span v-if="local.is_potok">Potokli: 2–8 ta guruh tanlang</span>
                <span v-else>Potoksiz: 1 ta guruh tanlang</span>
                <span v-if="availableGroupItems.length" class="ml-2 text-gray-400">({{ availableGroupItems.length }} ta guruh mavjud)</span>
            </p>

            <!-- Guruh topilmadi -->
            <div v-if="availableGroupItems.length === 0" class="p-4 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-700">
                ⚠️ Tanlangan yo'nalish va kurs uchun guruh topilmadi. Avval guruhlar qo'shing.
            </div>

            <CheckboxSelect
                v-else
                v-model="local.group_ids"
                :items="availableGroupItems"
                :placeholder="local.is_potok ? '2 dan 8 tagacha guruh tanlang...' : '1 ta guruh tanlang...'"
                search-placeholder="Guruh qidirish..."
                tag-class="bg-green-100 text-green-700"
                :has-error="!!errors.group_ids"
                :max-select="local.is_potok ? 8 : 1"
                :show-select-all="false"
            />

            <!-- Validatsiya xatosi -->
            <p v-if="errors.group_ids" class="text-xs text-red-500 mt-1">{{ errors.group_ids }}</p>

            <!-- Tanlangan guruhlar statistikasi -->
            <div v-if="local.group_ids.length" class="mt-3 flex flex-wrap gap-3">
                <div class="flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-full bg-blue-50 text-blue-700">
                    <span>👥</span>
                    <span>{{ local.group_ids.length }} ta guruh</span>
                </div>
                <div class="flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-full bg-green-50 text-green-700">
                    <span>🎓</span>
                    <span>Jami {{ totalStudents }} talaba</span>
                </div>
            </div>
        </div>

    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import CheckboxSelect from '@/Components/CheckboxSelect.vue'

const props = defineProps({
    modelValue:  { type: Object, required: true },
    departments: { type: Array,  default: () => [] },
    directions:  { type: Array,  default: () => [] },
    groups:      { type: Array,  default: () => [] },
    currentUser: { type: Object, default: () => ({}) },
})
const emit = defineEmits(['update:modelValue', 'valid'])

// Two-way binding holat ob'ekt orqali
const local = computed({
    get: () => props.modelValue,
    set: v  => emit('update:modelValue', v),
})

const errors = ref({})
const isKafedraMudiri = computed(() => !!props.currentUser?.is_kafedra_mudiri)

// ─── Kurs tugmalar ────────────────────────────────────────────────────────────
// degree_type: 'bakalavr' → 4 kurs, 'magistratura' → 2 kurs
const availableCourses = computed(() => {
    const selected = props.directions.filter(d => local.value.direction_ids.includes(d.id))
    if (!selected.length) return []

    const hasMagistratura = selected.some(d =>
        d.degree_type === 'magistratura' || d.level === 'magistratura' || d.level === 'magistr'
    )
    const hasBakalavr = selected.some(d =>
        !d.degree_type || d.degree_type === 'bakalavr' || d.level === 'bakalavr'
    )

    // Agar faqat magistratura
    if (hasMagistratura && !hasBakalavr) {
        return [
            { value: 1, label: '1-kurs (Magistratura)' },
            { value: 2, label: '2-kurs (Magistratura)' },
        ]
    }

    // Agar bakalavr (yoki aralash)
    return [
        { value: 1, label: '1-kurs' },
        { value: 2, label: '2-kurs' },
        { value: 3, label: '3-kurs' },
        { value: 4, label: '4-kurs' },
    ]
})

// ─── Yo'nalish items (CheckboxSelect uchun) ───────────────────────────────────
const directionItems = computed(() => {
    const deptId = local.value.department_id

    // Kafedra mudiri barcha yo'nalishlarni ko'rishi kerak
    // (boshqa yo'nalish guruhlariga ham o'z kafedrasi o'qituvchisi yuklama olishi mumkin)
    // O'z kafedrasi yo'nalishlari birinchi, qolganlari keyingisi
    const sorted = [...props.directions].sort((a, b) => {
        const aOwn = a.department_id === deptId ? 0 : 1
        const bOwn = b.department_id === deptId ? 0 : 1
        return aOwn - bOwn || a.name.localeCompare(b.name)
    })

    return sorted.map(d => {
        const isMag = d.degree_type === 'magistratura' || d.level === 'magistratura' || d.level === 'magistr'
        const isOwn = d.department_id === deptId
        return {
            id:         d.id,
            label:      d.name,
            sublabel:   d.code ?? '',
            badge:      isMag ? 'Magistratura' : 'Bakalavr',
            badgeClass: isMag ? 'bg-purple-100 text-purple-600' : 'bg-blue-100 text-blue-600',
            group:      isOwn ? 'O\'z kafedrasi yo\'nalishlari' : 'Boshqa yo\'nalishlar',
        }
    })
})

// ─── Guruh items — faqat tanlangan yo'nalish + kursga mos ───────────────────
const availableGroupItems = computed(() => {
    const dirIds = local.value.direction_ids
    const course = local.value.course
    if (!dirIds.length || !course) return []

    return props.groups
        .filter(g => dirIds.includes(g.direction_id) && g.course === course)
        .map(g => {
            const dirName = props.directions.find(d => d.id === g.direction_id)?.name ?? ''
            return {
                id:        g.id,
                label:     g.name,
                sublabel:  g.code ?? '',
                badge:     `${g.student_count ?? 0} talaba`,
                badgeClass:'bg-gray-100 text-gray-500',
                group:     dirName,            // optgroup sarlavhasi
            }
        })
})

// ─── Talabalar soni ───────────────────────────────────────────────────────────
const totalStudents = computed(() =>
    local.value.group_ids.reduce((sum, id) => {
        const g = props.groups.find(g => g.id === id)
        return sum + (g?.student_count ?? 0)
    }, 0)
)

// ─── Potok o'zgarganda guruhlarni tozalash ───────────────────────────────────
function setPotok(val) {
    local.value.is_potok = val
    local.value.group_ids = []
}

// ─── Kafedra o'zgarganda ─────────────────────────────────────────────────────
watch(() => local.value.department_id, () => {
    local.value.direction_ids = []
    local.value.course = null
    local.value.group_ids = []
})

// ─── Yo'nalish o'zgarganda ───────────────────────────────────────────────────
watch(() => local.value.direction_ids, (newDirs, oldDirs) => {
    // Kursni reset qilish faqat yo'nalish o'zgarganda
    if (JSON.stringify(newDirs) !== JSON.stringify(oldDirs)) {
        local.value.course = null
        local.value.group_ids = []
    }
}, { deep: true })

// ─── Kurs o'zgarganda guruhlarni tozalash ────────────────────────────────────
watch(() => local.value.course, () => {
    local.value.group_ids = []
})

// ─── Validatsiya ─────────────────────────────────────────────────────────────
watch(
    () => ({
        dept:  local.value.department_id,
        dirs:  local.value.direction_ids,
        course:local.value.course,
        groups:local.value.group_ids,
        potok: local.value.is_potok,
    }),
    ({ dept, dirs, course, groups, potok }) => {
        errors.value = {}

        if (!dept)
            errors.value.department_id = 'Kafedra tanlanishi shart'

        if (!dirs?.length)
            errors.value.direction_ids = 'Kamida 1 ta yo\'nalish tanlang'

        if (!course)
            errors.value.course = 'Kurs tanlanishi shart'

        if (!groups?.length) {
            errors.value.group_ids = 'Kamida 1 ta guruh tanlang'
        } else if (!potok && groups.length > 1) {
            errors.value.group_ids = 'Potoksiz holatda faqat 1 ta guruh tanlash mumkin'
        } else if (potok && groups.length < 2) {
            errors.value.group_ids = 'Potokli holatda kamida 2 ta guruh tanlang'
        } else if (potok && groups.length > 8) {
            errors.value.group_ids = 'Maksimal 8 ta guruh tanlash mumkin'
        }

        emit('valid', Object.keys(errors.value).length === 0)
    },
    { immediate: true, deep: true }
)

// ─── Style helpers ────────────────────────────────────────────────────────────
function cardClass(active) {
    return [
        'flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all text-left w-full',
        active
            ? 'border-blue-500 bg-blue-50 shadow-sm'
            : 'border-gray-200 bg-white hover:border-gray-300',
    ]
}
</script>
