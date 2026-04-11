<template>
    <div class="w-full">

        <!-- Desktop: gorizontal progress -->
        <div class="hidden sm:block">
            <div class="flex items-center">
                <template v-for="(step, idx) in steps" :key="idx">

                    <!-- Step item -->
                    <div class="flex flex-col items-center" :class="idx < steps.length - 1 ? 'flex-1' : ''">
                        <div class="flex items-center w-full">

                            <!-- Doira -->
                            <div
                                class="w-11 h-11 rounded-full flex items-center justify-center font-bold text-sm
                                       border-2 transition-all duration-300 flex-shrink-0"
                                :class="stepState(idx + 1) === 'done'
                                    ? 'bg-indigo-600 border-indigo-600 text-white shadow-lg shadow-indigo-200'
                                    : stepState(idx + 1) === 'active'
                                        ? 'bg-white border-indigo-500 text-indigo-600 ring-4 ring-indigo-100 shadow-md'
                                        : 'bg-gray-50 border-gray-300 text-gray-400'"
                            >
                                <!-- Done: checkmark SVG -->
                                <svg v-if="stepState(idx + 1) === 'done'"
                                     class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                <!-- Active/upcoming: raqam -->
                                <span v-else>{{ idx + 1 }}</span>
                            </div>

                            <!-- Connector chiziq (oxirgi stepdan keyin yo'q) -->
                            <div v-if="idx < steps.length - 1"
                                 class="flex-1 h-0.5 mx-3 transition-all duration-500"
                                 :class="stepState(idx + 1) === 'done' ? 'bg-indigo-500' : 'bg-gray-200'">
                            </div>
                        </div>

                        <!-- Label -->
                        <div class="mt-2 text-center" :class="idx < steps.length - 1 ? 'w-full' : ''">
                            <p class="text-xs font-semibold leading-tight"
                               :class="stepState(idx + 1) === 'active'
                                   ? 'text-indigo-600'
                                   : stepState(idx + 1) === 'done'
                                       ? 'text-gray-700'
                                       : 'text-gray-400'">
                                {{ step.label }}
                            </p>
                            <p class="text-xs mt-0.5"
                               :class="stepState(idx + 1) === 'active' ? 'text-indigo-400' : 'text-gray-300'">
                                {{ idx + 1 }}-qadam
                            </p>
                        </div>
                    </div>

                </template>
            </div>
        </div>

        <!-- Mobile: dots + matn -->
        <div class="flex sm:hidden items-center justify-between">
            <div class="flex items-center gap-2">
                <div
                    v-for="idx in steps.length"
                    :key="idx"
                    class="rounded-full transition-all duration-300"
                    :class="idx === currentStep
                        ? 'w-8 h-2.5 bg-indigo-600'
                        : idx < currentStep
                            ? 'w-2.5 h-2.5 bg-indigo-400'
                            : 'w-2.5 h-2.5 bg-gray-200'"
                />
            </div>
            <div class="text-right">
                <p class="text-sm font-semibold text-indigo-600">
                    {{ steps[currentStep - 1]?.label }}
                </p>
                <p class="text-xs text-gray-400">
                    {{ currentStep }} / {{ steps.length }}
                </p>
            </div>
        </div>

    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    currentStep: { type: Number, required: true },
    steps:       { type: Array,  required: true },  // [{ label: '...' }, ...]
})

function stepState(step) {
    if (step < props.currentStep)  return 'done'
    if (step === props.currentStep) return 'active'
    return 'upcoming'
}
</script>
