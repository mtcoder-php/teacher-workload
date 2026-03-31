<template>
    <div class="relative" ref="wrapRef">

        <!-- Trigger button -->
        <button
            type="button"
            @click="toggle"
            :disabled="disabled"
            :class="[
        'w-full flex items-center justify-between px-3 py-2 rounded-lg border text-sm transition-all',
        isOpen
          ? 'border-blue-400 ring-2 ring-blue-100 bg-white'
          : 'border-gray-300 bg-white hover:border-gray-400',
        disabled ? 'bg-gray-50 text-gray-400 cursor-not-allowed' : 'cursor-pointer',
        hasError ? 'border-red-400' : '',
      ]"
        >
            <!-- Tanlangan teglar yoki placeholder -->
            <div class="flex flex-wrap gap-1 flex-1 min-w-0 mr-2">
                <template v-if="selectedItems.length">
          <span
              v-for="item in selectedItems"
              :key="item.id"
              class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full font-medium"
              :class="tagClass"
          >
            {{ item.label }}
            <button
                v-if="!disabled"
                type="button"
                @click.stop="deselect(item.id)"
                class="opacity-60 hover:opacity-100 leading-none"
            >×</button>
          </span>
                </template>
                <span v-else class="text-gray-400">{{ placeholder }}</span>
            </div>
            <!-- Arrow -->
            <svg
                class="w-4 h-4 text-gray-400 flex-shrink-0 transition-transform"
                :class="isOpen ? 'rotate-180' : ''"
                fill="none" stroke="currentColor" viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <!-- Dropdown panel -->
        <Transition
            enter-active-class="transition ease-out duration-150"
            enter-from-class="opacity-0 scale-95 -translate-y-1"
            enter-to-class="opacity-100 scale-100 translate-y-0"
            leave-active-class="transition ease-in duration-100"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-if="isOpen"
                class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden"
                style="min-width: 260px"
            >
                <!-- Search -->
                <div class="p-2 border-b border-gray-100">
                    <div class="relative">
                        <input
                            ref="searchRef"
                            v-model="query"
                            type="text"
                            :placeholder="searchPlaceholder"
                            class="w-full text-sm rounded-lg border border-gray-200 py-1.5 pl-8 pr-3 focus:ring-2 focus:ring-blue-200 focus:border-blue-400 outline-none"
                        />
                        <svg class="absolute left-2.5 top-2 w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 111 11a6 6 0 0116 0z"/>
                        </svg>
                    </div>
                </div>

                <!-- "Barchasini tanlash" (ixtiyoriy) -->
                <div v-if="showSelectAll && flatFiltered.length > 1" class="px-3 py-2 border-b border-gray-100">
                    <label class="flex items-center gap-2 cursor-pointer select-none text-xs font-medium text-gray-500">
                        <input
                            type="checkbox"
                            :checked="allSelected"
                            :indeterminate="someSelected"
                            @change="toggleAll"
                            class="w-3.5 h-3.5 rounded border-gray-300 text-blue-600"
                        />
                        Barchasini tanlash ({{ flatFiltered.length }})
                    </label>
                </div>

                <!-- List -->
                <div class="max-h-56 overflow-y-auto">
                    <div v-if="flatFiltered.length === 0" class="px-4 py-6 text-sm text-gray-400 text-center">
                        Natija topilmadi
                    </div>

                    <template v-for="group in groupedFiltered" :key="group.label ?? '__nogroup'">
                        <!-- Group header -->
                        <div
                            v-if="group.label"
                            class="px-3 pt-2 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wide sticky top-0 bg-white border-b border-gray-50"
                        >
                            {{ group.label }}
                        </div>
                        <!-- Items -->
                        <label
                            v-for="item in group.items"
                            :key="item.id"
                            class="flex items-center gap-3 px-4 py-2.5 cursor-pointer hover:bg-blue-50 transition-colors"
                            :class="{
                'bg-blue-50': modelValue.includes(item.id),
                'opacity-50 cursor-not-allowed pointer-events-none': item.disabled,
              }"
                        >
                            <input
                                type="checkbox"
                                :value="item.id"
                                :checked="modelValue.includes(item.id)"
                                :disabled="item.disabled"
                                @change="onCheck(item.id, $event.target.checked)"
                                class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-400"
                            />
                            <div class="flex-1 min-w-0">
                                <span class="text-sm text-gray-800">{{ item.label }}</span>
                                <span v-if="item.sublabel" class="ml-2 text-xs text-gray-400">{{ item.sublabel }}</span>
                            </div>
                            <span v-if="item.badge" class="text-xs px-2 py-0.5 rounded-full" :class="item.badgeClass ?? 'bg-gray-100 text-gray-500'">
                {{ item.badge }}
              </span>
                        </label>
                    </template>
                </div>

                <!-- Footer: tanlangan soni -->
                <div v-if="modelValue.length" class="px-3 py-2 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
                    <span class="text-xs text-gray-500">{{ modelValue.length }} ta tanlandi</span>
                    <button type="button" @click="clearAll" class="text-xs text-red-500 hover:text-red-700">Tozalash</button>
                </div>
            </div>
        </Transition>
    </div>
</template>

<script setup>
import { ref, computed, watch, nextTick, onMounted, onBeforeUnmount } from 'vue'

/**
 * items prop formati:
 * [
 *   { id, label, sublabel?, badge?, badgeClass?, disabled?, group? }
 * ]
 * group field bo'lsa, dropdown ichida section sarlavhalari chiqadi.
 */
const props = defineProps({
    modelValue:       { type: Array,   default: () => [] },
    items:            { type: Array,   default: () => [] },   // { id, label, sublabel?, badge?, group? }
    placeholder:      { type: String,  default: 'Tanlang...' },
    searchPlaceholder:{ type: String,  default: 'Qidirish...' },
    disabled:         { type: Boolean, default: false },
    hasError:         { type: Boolean, default: false },
    showSelectAll:    { type: Boolean, default: false },
    tagClass:         { type: String,  default: 'bg-blue-100 text-blue-700' },
    maxSelect:        { type: Number,  default: 0 }, // 0 = cheksiz
})
const emit = defineEmits(['update:modelValue'])

const isOpen    = ref(false)
const query     = ref('')
const wrapRef   = ref(null)
const searchRef = ref(null)

function toggle() {
    if (props.disabled) return
    isOpen.value = !isOpen.value
    if (isOpen.value) nextTick(() => searchRef.value?.focus())
}

// Outside click
function onOutside(e) {
    if (wrapRef.value && !wrapRef.value.contains(e.target)) isOpen.value = false
}
onMounted(()   => document.addEventListener('mousedown', onOutside))
onBeforeUnmount(() => document.removeEventListener('mousedown', onOutside))

// Filter
const flatFiltered = computed(() => {
    const q = query.value.toLowerCase()
    return props.items.filter(i =>
        i.label.toLowerCase().includes(q) ||
        (i.sublabel ?? '').toLowerCase().includes(q)
    )
})

// Group items bo'yicha
const groupedFiltered = computed(() => {
    const groups = {}
    flatFiltered.value.forEach(item => {
        const g = item.group ?? ''
        if (!groups[g]) groups[g] = { label: g || null, items: [] }
        groups[g].items.push(item)
    })
    return Object.values(groups)
})

// Tanlangan items (label uchun)
const selectedItems = computed(() =>
    props.modelValue.map(id => {
        const found = props.items.find(i => i.id === id)
        return found ? { id, label: found.label } : { id, label: String(id) }
    })
)

// Check / deselect
function onCheck(id, checked) {
    let next = [...props.modelValue]
    if (checked) {
        if (props.maxSelect > 0 && next.length >= props.maxSelect) return
        next.push(id)
    } else {
        next = next.filter(x => x !== id)
    }
    emit('update:modelValue', next)
}
function deselect(id) {
    emit('update:modelValue', props.modelValue.filter(x => x !== id))
}
function clearAll() {
    emit('update:modelValue', [])
}

// Select all
const allSelected  = computed(() => flatFiltered.value.length > 0 && flatFiltered.value.every(i => props.modelValue.includes(i.id)))
const someSelected = computed(() => flatFiltered.value.some(i => props.modelValue.includes(i.id)) && !allSelected.value)
function toggleAll() {
    if (allSelected.value) {
        emit('update:modelValue', props.modelValue.filter(id => !flatFiltered.value.some(i => i.id === id)))
    } else {
        const toAdd = flatFiltered.value.filter(i => !props.modelValue.includes(i.id)).map(i => i.id)
        emit('update:modelValue', [...props.modelValue, ...toAdd])
    }
}

// Query reset when closed
watch(isOpen, v => { if (!v) query.value = '' })
</script>
