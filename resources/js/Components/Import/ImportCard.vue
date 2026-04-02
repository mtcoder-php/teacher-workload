<template>
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">

        <!-- Header -->
        <div class="p-5">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">{{ icon }}</span>
                    <div>
                        <h4 class="text-base font-semibold text-gray-900">{{ title }}</h4>
                        <p class="text-sm text-gray-500 mt-0.5">{{ description }}</p>
                    </div>
                </div>

                <!-- Tugmalar -->
                <div class="flex items-center gap-2 flex-shrink-0">
                    <!-- Namuna yuklab olish -->
                    <a :href="sampleAction"
                       class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium
                              text-indigo-600 bg-indigo-50 border border-indigo-200
                              rounded-lg hover:bg-indigo-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Namuna
                    </a>

                    <!-- Import qilish -->
                    <label class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium
                                  text-white bg-indigo-600 rounded-lg hover:bg-indigo-700
                                  cursor-pointer transition-colors"
                           :class="{ 'opacity-60 cursor-not-allowed': isLoading }">
                        <svg class="w-4 h-4" :class="{ 'animate-spin': isLoading }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path v-if="!isLoading" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        {{ isLoading ? 'Yuklanmoqda...' : 'Import' }}
                        <input type="file" class="hidden" accept=".xlsx,.xls,.csv"
                               @change="handleFile" :disabled="isLoading"/>
                    </label>
                </div>
            </div>

            <!-- Tanlangan fayl -->
            <div v-if="selectedFile"
                 class="mt-3 flex items-center gap-2 text-sm text-green-700 bg-green-50
                        border border-green-200 rounded-lg px-3 py-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                </svg>
                {{ selectedFile.name }}
                <span class="text-green-500 text-xs">({{ formatSize(selectedFile.size) }})</span>
            </div>
        </div>

        <!-- Ustunlar + Reference (accordion) -->
        <div class="border-t border-gray-100">
            <button @click="open = !open"
                    class="w-full flex items-center justify-between px-5 py-3 text-sm
                           text-gray-500 hover:bg-gray-50 transition-colors">
                <span>📋 Ustun nomlari va mavjud ma'lumotlar</span>
                <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div v-if="open">

                <!-- Ustunlar jadvali -->
                <div class="overflow-x-auto border-t border-gray-100">
                    <table class="min-w-full text-xs">
                        <thead class="bg-indigo-50">
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold text-indigo-700">Ustun nomi (key)</th>
                            <th class="px-4 py-2 text-left font-semibold text-indigo-700">Tavsif</th>
                            <th class="px-4 py-2 text-left font-semibold text-indigo-700">Majburiy</th>
                            <th class="px-4 py-2 text-left font-semibold text-indigo-700">Misol</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                        <tr v-for="col in columns" :key="col.key"
                            class="hover:bg-gray-50"
                            :class="{ 'bg-red-50': col.required }">
                            <td class="px-4 py-2 font-mono font-medium text-indigo-600">{{ col.key }}</td>
                            <td class="px-4 py-2 text-gray-700">{{ col.label }}</td>
                            <td class="px-4 py-2">
                                    <span v-if="col.required"
                                          class="px-1.5 py-0.5 bg-red-100 text-red-700 rounded text-xs font-medium">
                                        Ha
                                    </span>
                                <span v-else class="text-gray-400">—</span>
                            </td>
                            <td class="px-4 py-2 text-gray-500 font-mono">{{ col.example }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Reference data -->
                <div v-if="references?.length" class="border-t border-gray-100 bg-amber-50 p-4 space-y-3">
                    <div v-for="(ref, i) in references" :key="i">
                        <p class="text-xs font-semibold text-amber-800 mb-1.5">⚠️ {{ ref.title }}</p>
                        <div class="flex flex-wrap gap-1.5">
                            <span v-for="(item, j) in ref.items" :key="j"
                                  class="px-2 py-1 bg-white border border-amber-200 rounded
                                         text-xs text-gray-700 font-mono">
                                {{ item }}
                            </span>
                        </div>
                    </div>
                    <p class="text-xs text-amber-600">
                        💡 Excel faylda yuqoridagi qiymatlarni <strong>aynan shu ko'rinishda</strong> yozing.
                        Namuna faylni yuklang — u yerda ham shu ro'yxat bor.
                    </p>
                </div>

                <!-- Eslatma -->
                <div class="px-4 py-3 bg-gray-50 border-t border-gray-100 text-xs text-gray-500">
                    ℹ️ Excel faylning <strong>birinchi qatori</strong> sarlavha bo'lishi shart.
                    2-qatordan ma'lumot boshlanadi.
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    title:         { type: String, required: true },
    description:   { type: String, default: '' },
    icon:          { type: String, default: '📄' },
    importAction:  { type: String, required: true },
    sampleAction:  { type: String, required: true },
    columns:       { type: Array,  default: () => [] },
    references:    { type: Array,  default: () => [] },
})

const selectedFile = ref(null)
const isLoading    = ref(false)
const open         = ref(false)

function handleFile(e) {
    const file = e.target.files[0]
    if (!file) return
    selectedFile.value = file

    if (!confirm(`"${file.name}" faylini import qilishni tasdiqlaysizmi?`)) {
        selectedFile.value = null
        e.target.value = ''
        return
    }

    const formData = new FormData()
    formData.append('file', file)

    isLoading.value = true
    router.post(props.importAction, formData, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => { open.value = false },
        onFinish:  () => {
            isLoading.value    = false
            selectedFile.value = null
            e.target.value     = ''
        },
    })
}

function formatSize(bytes) {
    if (bytes < 1024)    return bytes + ' B'
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB'
    return (bytes / 1048576).toFixed(1) + ' MB'
}
</script>
