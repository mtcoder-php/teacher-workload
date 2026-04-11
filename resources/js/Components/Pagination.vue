<template>
    <div class="px-6 py-4 border-t border-gray-200">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">

            <!-- Chap: info -->
            <p class="text-sm text-gray-600 order-2 sm:order-1">
                <template v-if="meta.total > 0">
                    <span class="font-medium text-gray-900">{{ meta.from }}</span>
                    –
                    <span class="font-medium text-gray-900">{{ meta.to }}</span>
                    /
                    <span class="font-medium text-gray-900">{{ meta.total }}</span>
                    ta
                </template>
                <template v-else>
                    Ma'lumot topilmadi
                </template>
            </p>

            <!-- O'ng: pagination tugmalari -->
            <div class="flex items-center gap-1 order-1 sm:order-2" v-if="meta.last_page > 1">

                <!-- First + Prev -->
                <Link
                    v-if="meta.current_page > 1"
                    :href="pageUrl(1)"
                    class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors"
                    title="Birinchi sahifa"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7M18 19l-7-7 7-7"/>
                    </svg>
                </Link>
                <Link
                    v-if="meta.current_page > 1"
                    :href="pageUrl(meta.current_page - 1)"
                    class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors"
                    title="Oldingi sahifa"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </Link>

                <!-- Sahifa raqamlari -->
                <template v-for="page in visiblePages" :key="page">
                    <span v-if="page === '...'" class="px-2 py-1 text-gray-400 text-sm">...</span>
                    <Link
                        v-else
                        :href="pageUrl(page)"
                        class="min-w-[36px] h-9 flex items-center justify-center rounded-lg text-sm font-medium transition-colors"
                        :class="page === meta.current_page
                            ? 'bg-indigo-600 text-white shadow-sm'
                            : 'text-gray-700 hover:bg-gray-100'"
                    >
                        {{ page }}
                    </Link>
                </template>

                <!-- Next + Last -->
                <Link
                    v-if="meta.current_page < meta.last_page"
                    :href="pageUrl(meta.current_page + 1)"
                    class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors"
                    title="Keyingi sahifa"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </Link>
                <Link
                    v-if="meta.current_page < meta.last_page"
                    :href="pageUrl(meta.last_page)"
                    class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors"
                    title="Oxirgi sahifa"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M6 5l7 7-7 7"/>
                    </svg>
                </Link>

            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    // Laravel paginator ob'ekti: { data, current_page, last_page, from, to, total, path, ... }
    meta: {
        type: Object,
        required: true,
    },
})

// Sahifa URL — mavjud query string ni saqlab, page param qo'shadi
function pageUrl(page) {
    const url = new URL(props.meta.path, window.location.origin)
    // Mavjud query params
    const current = new URL(window.location.href)
    current.searchParams.forEach((val, key) => {
        if (key !== 'page') url.searchParams.set(key, val)
    })
    url.searchParams.set('page', page)
    return url.pathname + url.search
}

// Ko'rinadigan sahifa raqamlari: 1 ... 4 5 [6] 7 8 ... 20
const visiblePages = computed(() => {
    const current = props.meta.current_page
    const last    = props.meta.last_page
    if (last <= 7) {
        return Array.from({ length: last }, (_, i) => i + 1)
    }
    const pages = []
    // Har doim 1 va last ko'rsatamiz
    // Window: current ±2
    const left  = Math.max(2, current - 2)
    const right = Math.min(last - 1, current + 2)

    pages.push(1)
    if (left > 2) pages.push('...')
    for (let i = left; i <= right; i++) pages.push(i)
    if (right < last - 1) pages.push('...')
    pages.push(last)
    return pages
})
</script>
