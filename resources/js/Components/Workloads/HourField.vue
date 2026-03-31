<template>
    <div class="flex flex-col gap-1">

        <!-- Label + qolgan soat -->
        <div class="flex items-center justify-between">
            <label class="text-xs font-medium text-gray-600 truncate">{{ label }}</label>
            <span
                v-if="subjectMax > 0"
                class="text-xs tabular-nums"
                :class="limitTextClass"
                :title="`Fan soati: ${subjectMax} | Qolgan: ${remaining}`"
            >
        {{ modelValue }}/{{ remaining }}
      </span>
        </div>

        <!-- Input -->
        <div class="relative">
            <input
                type="number"
                :value="modelValue"
                @input="onInput"
                min="0"
                step="0.5"
                :disabled="isDisabled"
                :class="[
          'w-full text-sm px-2.5 py-2 rounded-lg border transition outline-none',
          isDisabled
            ? 'bg-gray-100 text-gray-400 cursor-not-allowed border-gray-200'
            : isOver
              ? 'border-red-400 bg-red-50 text-red-700 focus:ring-2 focus:ring-red-100'
              : isFull
                ? 'border-green-400 bg-green-50 text-green-700 focus:ring-2 focus:ring-green-100'
                : 'border-gray-300 bg-white text-gray-800 focus:ring-2 focus:ring-blue-100 focus:border-blue-400',
        ]"
            />
            <!-- 0 soat belgisi -->
            <span
                v-if="isDisabled"
                class="absolute inset-0 flex items-center justify-center text-xs text-gray-300 pointer-events-none"
            >
        —
      </span>
        </div>

        <!-- Mini progress bar -->
        <div v-if="subjectMax > 0 && !isDisabled" class="h-1 bg-gray-100 rounded-full overflow-hidden">
            <div
                class="h-full rounded-full transition-all duration-200"
                :class="barClass"
                :style="{ width: Math.min(fillPct, 100) + '%' }"
            />
        </div>

        <!-- Over limit xabar -->
        <p v-if="isOver" class="text-xs text-red-500">
            Limit: {{ remaining }} soat
        </p>

    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    modelValue: { type: Number, default: 0 },
    label:      { type: String, required: true },
    subjectMax: { type: Number, default: 0 },  // Fan modelidagi max soat
    remaining:  { type: Number, default: 0 },  // Bu o'quv yilida qolgan soat
    disabled:   { type: Boolean, default: false },
})
const emit = defineEmits(['update:modelValue'])

function onInput(e) {
    const val = parseFloat(e.target.value) || 0
    emit('update:modelValue', Math.max(0, val))
}

// Disabled holat:
// 1. Tashqaridan disabled prop kelsa
// 2. Fan uchun bu soat belgilanmagan (subjectMax = 0)
// 3. Bu soat to'liq taqsimlangan (remaining = 0, lekin subjectMax > 0)
const isDisabled = computed(() =>
    props.disabled ||
    props.subjectMax === 0 ||
    (props.subjectMax > 0 && props.remaining === 0)
)

// Limitdan oshganmi:
// subjectMax > 0 va remaining > 0 bo'lsa, entered > remaining
// subjectMax > 0 va remaining = 0 bo'lsa — disabled, kirita olmaydi
const isOver = computed(() =>
    !isDisabled.value &&
    props.subjectMax > 0 &&
    (props.modelValue ?? 0) > props.remaining
)

const isFull = computed(() =>
    !isDisabled.value &&
    props.remaining > 0 &&
    (props.modelValue ?? 0) === props.remaining
)

const fillPct = computed(() =>
    props.subjectMax > 0
        ? Math.round(((props.modelValue ?? 0) / props.subjectMax) * 100)
        : 0
)

const barClass = computed(() => {
    if (isOver.value) return 'bg-red-500'
    if (isFull.value) return 'bg-green-500'
    if (fillPct.value > 80) return 'bg-amber-400'
    return 'bg-blue-500'
})

const limitTextClass = computed(() => {
    if (isOver.value) return 'text-red-500 font-semibold'
    if (isFull.value) return 'text-green-600'
    if (props.remaining === 0 && props.subjectMax > 0) return 'text-red-400'
    return 'text-gray-400'
})
</script>
