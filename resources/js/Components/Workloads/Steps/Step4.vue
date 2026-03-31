<template>
    <div class="space-y-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">O'qituvchi tayinlash</h3>
            <p class="text-sm text-gray-500 mt-1">Bu yuklamani bajaradigan o'qituvchini tanlang</p>
        </div>

        <!-- O'qituvchi qidirish -->
        <div class="form-group">
            <label class="form-label">O'qituvchi <span class="text-red-500">*</span></label>

            <div class="relative mb-2">
                <input
                    v-model="teacherSearch"
                    type="text"
                    placeholder="Ism, familiya qidirish..."
                    class="form-input pl-9"
                />
                <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 111 11a6 6 0 0116 0z"/>
                </svg>
            </div>

            <div class="border border-gray-200 rounded-xl max-h-60 overflow-y-auto divide-y divide-gray-100">
                <div v-if="filteredTeachers.length === 0" class="p-4 text-sm text-gray-400 text-center">
                    O'qituvchi topilmadi
                </div>
                <button
                    v-for="t in filteredTeachers"
                    :key="t.id"
                    type="button"
                    @click="local.teacher_id = t.id"
                    :class="[
                        'w-full flex items-center gap-3 px-4 py-3 text-left hover:bg-blue-50 transition-colors',
                        local.teacher_id === t.id ? 'bg-blue-50 border-l-2 border-blue-500' : ''
                    ]"
                >
                    <!-- Avatar -->
                    <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-sm font-semibold flex-shrink-0">
                        {{ t.name.charAt(0).toUpperCase() }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-gray-800 truncate">{{ t.name }}</div>
                        <div class="text-xs text-gray-500 flex gap-2">
                            <span v-if="t.position">{{ t.position }}</span>
                            <span v-if="t.academic_degree" class="text-purple-600">{{ t.academic_degree }}</span>
                        </div>
                    </div>
                    <span v-if="local.teacher_id === t.id" class="text-blue-600 text-lg flex-shrink-0">✓</span>
                </button>
            </div>
        </div>

        <!-- Tanlangan o'qituvchi card -->
        <div v-if="selectedTeacher" class="p-4 bg-blue-50 border border-blue-200 rounded-xl flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-blue-600 text-white flex items-center justify-center text-lg font-bold">
                {{ selectedTeacher.name.charAt(0) }}
            </div>
            <div>
                <div class="font-semibold text-blue-900">{{ selectedTeacher.name }}</div>
                <div class="text-sm text-blue-600 flex gap-3">
                    <span v-if="selectedTeacher.position">{{ selectedTeacher.position }}</span>
                    <span v-if="selectedTeacher.academic_degree">{{ selectedTeacher.academic_degree }}</span>
                </div>
            </div>
            <button type="button" @click="local.teacher_id = null" class="ml-auto text-blue-400 hover:text-red-500 transition-colors">
                ✕ O'zgartirish
            </button>
        </div>

        <!-- Izoh -->
        <div class="form-group">
            <label class="form-label">Izoh (ixtiyoriy)</label>
            <textarea
                v-model="local.notes"
                rows="3"
                placeholder="Qo'shimcha izoh yoki tushuntirish..."
                class="form-input resize-none"
            />
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
    modelValue: { type: Object, required: true },
    teachers:   { type: Array, default: () => [] },
})
const emit = defineEmits(['update:modelValue', 'valid'])

const local = computed({
    get: () => props.modelValue,
    set: (v) => emit('update:modelValue', v),
})

const teacherSearch = ref('')

// Kafedra bo'yicha filtrlash
const relevantTeachers = computed(() =>
    props.teachers.filter(t => !local.value.department_id || t.department_id === local.value.department_id)
)

const filteredTeachers = computed(() => {
    const q = teacherSearch.value.toLowerCase()
    if (!q) return relevantTeachers.value
    return relevantTeachers.value.filter(t => t.name.toLowerCase().includes(q))
})

const selectedTeacher = computed(() =>
    props.teachers.find(t => t.id === local.value.teacher_id)
)

// Validatsiya
watch(
    () => local.value.teacher_id,
    (v) => emit('valid', !!v),
    { immediate: true }
)
</script>

<style scoped>
.form-group  { @apply flex flex-col gap-1; }
.form-label  { @apply text-sm font-medium text-gray-700; }
.form-input  { @apply w-full rounded-lg border border-gray-300 text-sm px-3 py-2 focus:ring-2 focus:ring-blue-300 focus:border-blue-400 outline-none transition; }
</style>
