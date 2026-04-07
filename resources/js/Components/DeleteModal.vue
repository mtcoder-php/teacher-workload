<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition-opacity duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="show"
                 class="fixed inset-0 z-50 flex items-center justify-center p-4"
                 @click.self="$emit('cancel')">

                <!-- Backdrop -->
                <div class="absolute inset-0 bg-black/50"></div>

                <!-- Modal -->
                <Transition
                    enter-active-class="transition-all duration-200"
                    enter-from-class="opacity-0 scale-95"
                    enter-to-class="opacity-100 scale-100"
                    leave-active-class="transition-all duration-150"
                    leave-from-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-95"
                >
                    <div v-if="show"
                         class="relative bg-white rounded-xl shadow-xl w-full max-w-md mx-auto">

                        <!-- Header -->
                        <div class="flex items-center gap-3 p-5 border-b border-gray-100">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-red-100
                                        flex items-center justify-center">
                                <svg class="w-5 h-5 text-red-600" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0
                                             2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464
                                             0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-gray-900">
                                    {{ title }}
                                </h3>
                                <p class="text-sm text-gray-500 mt-0.5">
                                    Bu amalni qaytarib bo'lmaydi
                                </p>
                            </div>
                        </div>

                        <!-- Body -->
                        <div class="p-5">
                            <p class="text-sm text-gray-600">
                                <slot>
                                    <span class="font-semibold text-gray-900">{{ itemName }}</span>
                                    ni o'chirmoqchimisiz?
                                </slot>
                            </p>
                        </div>

                        <!-- Footer -->
                        <div class="flex items-center justify-end gap-3 px-5 py-4
                                    bg-gray-50 rounded-b-xl border-t border-gray-100">
                            <button @click="$emit('cancel')"
                                    class="px-4 py-2 text-sm font-medium text-gray-700
                                           bg-white border border-gray-300 rounded-lg
                                           hover:bg-gray-50 transition-colors">
                                Bekor qilish
                            </button>
                            <button @click="$emit('confirm')"
                                    :disabled="loading"
                                    class="inline-flex items-center gap-2 px-4 py-2 text-sm
                                           font-medium text-white bg-red-600 rounded-lg
                                           hover:bg-red-700 disabled:opacity-60
                                           disabled:cursor-not-allowed transition-colors">
                                <svg v-if="loading" class="w-4 h-4 animate-spin"
                                     fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor"
                                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                <svg v-else class="w-4 h-4" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0
                                             01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0
                                             00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                {{ loading ? 'O\'chirilmoqda...' : 'O\'chirish' }}
                            </button>
                        </div>

                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
defineProps({
    show:     { type: Boolean, default: false },
    title:    { type: String,  default: "O'chirishni tasdiqlash" },
    itemName: { type: String,  default: '' },
    loading:  { type: Boolean, default: false },
})

defineEmits(['confirm', 'cancel'])
</script>
