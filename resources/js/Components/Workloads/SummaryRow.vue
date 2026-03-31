<template>
    <div class="flex items-start justify-between py-1 gap-2">
        <span class="text-xs text-gray-500 flex-shrink-0">{{ label }}</span>
        <span
            v-if="badge"
            :class="['text-xs font-medium px-2 py-0.5 rounded-full', badgeClass]"
        >{{ value }}</span>
        <span v-else class="text-xs font-medium text-gray-800 text-right">{{ value }}</span>
    </div>
</template>

<script setup>
import { computed } from 'vue'
const props = defineProps({
    label: String,
    value: [String, Number],
    badge: { type: String, default: null }, // 'blue' | 'purple' | 'green' | ...
})
const badgeClass = computed(() => ({
    blue:   'bg-blue-100 text-blue-700',
    purple: 'bg-purple-100 text-purple-700',
    green:  'bg-green-100 text-green-700',
    amber:  'bg-amber-100 text-amber-700',
    red:    'bg-red-100 text-red-700',
}[props.badge] ?? 'bg-gray-100 text-gray-700'))
</script>
