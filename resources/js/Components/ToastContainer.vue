<template>
    <teleport to="body">
        <div class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 w-80 max-w-[calc(100vw-2rem)]">
            <TransitionGroup
                enter-active-class="transition-all duration-300 ease-out"
                enter-from-class="opacity-0 translate-x-full scale-95"
                enter-to-class="opacity-100 translate-x-0 scale-100"
                leave-active-class="transition-all duration-200 ease-in"
                leave-from-class="opacity-100 translate-x-0 scale-100"
                leave-to-class="opacity-0 translate-x-full scale-95"
            >
                <div
                    v-for="toast in toasts"
                    :key="toast.id"
                    :class="[
            'flex items-start gap-3 p-4 rounded-xl shadow-lg border',
            typeClass(toast.type)
          ]"
                >
                    <!-- Icon -->
                    <span class="flex-shrink-0 text-lg leading-none mt-0.5">
            {{ typeIcon(toast.type) }}
          </span>

                    <!-- Message -->
                    <p class="flex-1 text-sm font-medium leading-snug">{{ toast.message }}</p>

                    <!-- Close -->
                    <button
                        type="button"
                        @click="remove(toast.id)"
                        class="flex-shrink-0 opacity-60 hover:opacity-100 transition-opacity leading-none text-lg"
                    >
                        ×
                    </button>
                </div>
            </TransitionGroup>
        </div>
    </teleport>
</template>

<script setup>
import { useToast } from '@/Composables/useToast'

const { toasts, remove } = useToast()

function typeClass(type) {
    return {
        success: 'bg-green-50  border-green-200  text-green-800',
        error:   'bg-red-50    border-red-200    text-red-800',
        warning: 'bg-amber-50  border-amber-200  text-amber-800',
        info:    'bg-blue-50   border-blue-200   text-blue-800',
    }[type] ?? 'bg-gray-50 border-gray-200 text-gray-800'
}

function typeIcon(type) {
    return { success: '✅', error: '❌', warning: '⚠️', info: 'ℹ️' }[type] ?? '💬'
}
</script>
