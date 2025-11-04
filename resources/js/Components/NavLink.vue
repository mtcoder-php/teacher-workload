<template>
    <Link
        :href="href"
        :class="[
            'flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors',
            active
                ? 'bg-indigo-50 text-indigo-600 font-medium'
                : 'text-gray-700 hover:bg-gray-100'
        ]"
    >
        <slot />
    </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: String,
    active: Boolean,
});
</script>