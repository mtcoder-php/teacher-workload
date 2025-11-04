<template>
    <Head :title="direction.name" />

    <AuthenticatedLayout>
        <template #header>{{ direction.name }}</template>

        <div class="space-y-6">
            <!-- Action Buttons -->
            <div class="flex items-center justify-between">
                <Link
                    href="/directions"
                    class="flex items-center space-x-2 text-gray-600 hover:text-gray-900"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span>Orqaga</span>
                </Link>

                <div class="flex items-center space-x-3">
                    <Link
                        :href="`/directions/${direction.id}/edit`"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center space-x-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>Tahrirlash</span>
                    </Link>
                </div>
            </div>

            <!-- Main Info Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-purple-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">{{ direction.name }}</h2>
                            <p class="text-sm text-gray-600 mt-1">{{ direction.code }}</p>
                        </div>
                        <span
                            :class="direction.is_active
                                ? 'bg-green-100 text-green-800'
                                : 'bg-red-100 text-red-800'"
                            class="px-3 py-1 text-sm font-semibold rounded-full"
                        >
                            {{ direction.is_active ? 'Faol' : 'Nofaol' }}
                        </span>
                    </div>
                </div>

                <!-- Body -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Kafedra -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-indigo-100 rounded-lg">
                                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Kafedra</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ direction.department?.name || '—' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Ta'lim darajasi -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-blue-100 rounded-lg">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Ta'lim darajasi</p>
                                    <p class="text-lg font-semibold text-gray-900 capitalize">{{ direction.degree_type }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Davomiyligi -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-orange-100 rounded-lg">
                                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Ta'lim davomiyligi</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ direction.duration_years }} yil</p>
                                </div>
                            </div>
                        </div>

                        <!-- Yaratilgan sana -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-green-100 rounded-lg">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Yaratilgan sana</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ formatDate(direction.created_at) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Izoh -->
                    <div v-if="direction.description" class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <p class="text-sm font-medium text-blue-900 mb-1">Izoh:</p>
                        <p class="text-sm text-blue-800">{{ direction.description }}</p>
                    </div>
                </div>
            </div>

            <!-- Groups List -->
            <div v-if="direction.groups && direction.groups.length > 0" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Guruhlar ro'yxati</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Guruh nomi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kurs</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Talabalar soni</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="group in direction.groups" :key="group.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ group.name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                        {{ group.course?.name || '—' }}
                                    </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ group.student_count || 0 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="group.is_active
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-red-100 text-red-800'"
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    >
                                        {{ group.is_active ? 'Faol' : 'Nofaol' }}
                                    </span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="!direction.groups || direction.groups.length === 0"
                 class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <p class="text-lg font-medium text-gray-900 mb-2">Guruhlar mavjud emas</p>
                <p class="text-sm text-gray-600">Bu yo'nalishda hali guruhlar qo'shilmagan</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Link, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    direction: Object,
});

const formatDate = (date) => {
    if (!date) return ''
    const [year, month, day] = date.split('T')[0].split('-')
    return `${day}.${month}.${year}`
}
</script>
