<template>
    <Head title="Yangi O'quv Yili" />
    
    <AuthenticatedLayout>
        <template #header>Yangi O'quv Yili Qo'shish</template>

        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-xl font-semibold text-gray-800">O'quv yili ma'lumotlari</h2>
                </div>

                <form @submit.prevent="submit" class="p-6 space-y-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            O'quv yili nomi <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.name }"
                            placeholder="Masalan: 2024-2025"
                            required
                        />
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                            {{ form.errors.name }}
                        </p>
                        <p class="mt-1 text-xs text-gray-500">Format: YYYY-YYYY (masalan: 2024-2025)</p>
                    </div>

                    <!-- Dates -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Boshlanish sanasi <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="start_date"
                                v-model="form.start_date"
                                type="date"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.start_date }"
                                required
                            />
                            <p v-if="form.errors.start_date" class="mt-1 text-sm text-red-600">
                                {{ form.errors.start_date }}
                            </p>
                        </div>

                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Tugash sanasi <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="end_date"
                                v-model="form.end_date"
                                type="date"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.end_date }"
                                required
                            />
                            <p v-if="form.errors.end_date" class="mt-1 text-sm text-red-600">
                                {{ form.errors.end_date }}
                            </p>
                        </div>
                    </div>

                    <!-- Is Current -->
                    <div class="flex items-center space-x-3 p-4 bg-gray-50 rounded-lg">
                        <input
                            v-model="form.is_active"
                            type="checkbox"
                            id="is_active"
                            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                        />
                        <label for="is_active" class="text-sm font-medium text-gray-700 cursor-pointer">
                            Bu o'quv yilini joriy o'quv yili sifatida belgilash
                        </label>
                    </div>

                    <!-- Info Box -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-blue-900 mb-1">Ma'lumot</h4>
                                <p class="text-sm text-blue-700">
                                    O'quv yili odatda sentyabr oyidan boshlanib, keyingi yilning iyun oyida tugaydi. 
                                    Agar bu o'quv yilini joriy deb belgilasangiz, avvalgi joriy o'quv yili avtomatik o'chiriladi.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <Link
                            href="/academic-years"
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition flex items-center space-x-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            <span>Orqaga</span>
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2"
                        >
                            <svg v-if="form.processing" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>{{ form.processing ? 'Saqlanmoqda...' : 'Saqlash' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const form = useForm({
    name: '',
    start_date: '',
    end_date: '',
    is_active: false,
});

const submit = () => {
    form.post('/academic-years', {
        preserveScroll: true,
    });
};
</script>