<template>
    <Teleport to="body">
        <div class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 w-80 max-w-[calc(100vw-2rem)]">
            <TransitionGroup
                enter-active-class="transition-all duration-300 ease-out"
                enter-from-class="opacity-0 translate-x-full scale-95"
                enter-to-class="opacity-100 translate-x-0 scale-100"
                leave-active-class="transition-all duration-200 ease-in"
                leave-from-class="opacity-100 translate-x-0 scale-100"
                leave-to-class="opacity-0 translate-x-full scale-95"
            >
                <div v-for="toast in toasts" :key="toast.id"
                     :class="['flex items-start gap-3 p-4 rounded-xl shadow-lg border', typeClass(toast.type)]">

                    <!-- Left bar indicator -->
                    <div :class="['flex-shrink-0 w-1 self-stretch rounded-full', barClass(toast.type)]"></div>

                    <!-- Message -->
                    <p class="flex-1 text-sm font-medium leading-snug py-0.5">{{ toast.message }}</p>

                    <!-- Close — bitta X -->
                    <button type="button" @click="remove(toast.id)"
                            class="flex-shrink-0 w-5 h-5 flex items-center justify-center
                                   rounded opacity-50 hover:opacity-100 transition-opacity">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </TransitionGroup>
        </div>
    </Teleport>
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

function barClass(type) {
    return {
        success: 'bg-green-400',
        error:   'bg-red-400',
        warning: 'bg-amber-400',
        info:    'bg-blue-400',
    }[type] ?? 'bg-gray-400'
}
</script>
