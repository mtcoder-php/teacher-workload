<template>
    <div class="w-full">
        <!-- Desktop: gorizontal progress -->
        <div class="hidden sm:flex items-center justify-between relative">
            <!-- Connector line -->
            <div class="absolute top-5 left-0 right-0 h-0.5 bg-gray-200 z-0">
                <div
                    class="h-full bg-blue-500 transition-all duration-500"
                    :style="{ width: progressWidth }"
                />
            </div>

            <div
                v-for="(step, idx) in steps"
                :key="idx"
                class="flex flex-col items-center relative z-10"
            >
                <!-- Doira -->
                <div
                    :class="[
                        'w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold border-2 transition-all duration-300',
                        stepState(idx + 1) === 'done'
                            ? 'bg-blue-600 border-blue-600 text-white shadow-md shadow-blue-200'
                            : stepState(idx + 1) === 'active'
                            ? 'bg-white border-blue-500 text-blue-600 shadow-md shadow-blue-100 ring-4 ring-blue-100'
                            : 'bg-white border-gray-300 text-gray-400'
                    ]"
                >
                    <span v-if="stepState(idx + 1) === 'done'" class="text-base">✓</span>
                    <span v-else>{{ step.icon }}</span>
                </div>

                <!-- Label -->
                <span
                    :class="[
                        'mt-2 text-xs font-medium text-center max-w-[80px] leading-tight',
                        stepState(idx + 1) === 'active' ? 'text-blue-600'
                            : stepState(idx + 1) === 'done' ? 'text-gray-700'
                            : 'text-gray-400'
                    ]"
                >
                    {{ step.label }}
                </span>
            </div>
        </div>

        <!-- Mobile: compact progress -->
        <div class="flex sm:hidden items-center gap-3">
            <div class="flex gap-1.5">
                <div
                    v-for="idx in steps.length"
                    :key="idx"
                    :class="[
                        'h-1.5 rounded-full transition-all duration-300',
                        idx === currentStep ? 'w-6 bg-blue-600'
                            : idx < currentStep ? 'w-3 bg-blue-400'
                            : 'w-3 bg-gray-200'
                    ]"
                />
            </div>
            <span class="text-sm font-medium text-gray-600">
                {{ currentStep }}/{{ steps.length }} — {{ steps[currentStep - 1]?.label }}
            </span>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    currentStep: { type: Number, required: true },
    steps:       { type: Array,  required: true },
})

function stepState(step) {
    if (step < props.currentStep) return 'done'
    if (step === props.currentStep) return 'active'
    return 'upcoming'
}

// Progress chiziq kengligi
const progressWidth = computed(() => {
    const pct = ((props.currentStep - 1) / (props.steps.length - 1)) * 100
    return `${pct}%`
})
</script>
