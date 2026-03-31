// resources/js/Composables/useToast.js
//
// Global toast manager.
// Ishlatish:
//   import { useToast } from '@/Composables/useToast'
//   const toast = useToast()
//   toast.success('Muvaffaqiyatli saqlandi!')
//   toast.error('Xatolik yuz berdi')
//   toast.warning('Diqqat!')
//   toast.info('Ma\'lumot')

import { reactive } from 'vue'

const toasts = reactive([])
let nextId = 1

function add(message, type = 'info', duration = 4000) {
    const id = nextId++
    toasts.push({ id, message, type, duration })

    setTimeout(() => remove(id), duration + 300)
}

function remove(id) {
    const idx = toasts.findIndex(t => t.id === id)
    if (idx !== -1) toasts.splice(idx, 1)
}

export function useToast() {
    return {
        toasts,
        remove,
        success: (msg, duration) => add(msg, 'success', duration),
        error:   (msg, duration) => add(msg, 'error',   duration ?? 6000),
        warning: (msg, duration) => add(msg, 'warning', duration),
        info:    (msg, duration) => add(msg, 'info',    duration),
    }
}
