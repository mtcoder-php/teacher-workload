<template>
    <Head title="Yangi Fakultet Qo'shish" />
    <AuthenticatedLayout>
        <template #header>Yangi Fakultet Qo'shish</template>

        <!-- ✅ Markazga surish uchun mx-auto qo'shildi -->
        <div class="max-w-3xl mx-auto px-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <form @submit.prevent="submit">
                    <!-- Fakultet nomi -->
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Fakultet nomi <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.name }"
                            placeholder="Masalan: Informatika fakulteti"
                            required
                        />
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <!-- Kod -->
                    <div class="mb-6">
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                            Fakultet kodi <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="code"
                            v-model="form.code"
                            type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.code }"
                            placeholder="Masalan: IF"
                            maxlength="50"
                            required
                        />
                        <p v-if="form.errors.code" class="mt-1 text-sm text-red-600">
                            {{ form.errors.code }}
                        </p>
                    </div>

                    <!-- Dekan -->
                    <div class="mb-6">
                        <label for="dean_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Dekan
                        </label>
                        <select
                            id="dean_id"
                            v-model="form.dean_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.dean_id }"
                        >
                            <option :value="null">Dekan tanlanmagan</option>
                            <option v-for="dean in deans" :key="dean.id" :value="dean.id">
                                {{ dean.name }} ({{ dean.email }})
                            </option>
                        </select>
                        <p v-if="form.errors.dean_id" class="mt-1 text-sm text-red-600">
                            {{ form.errors.dean_id }}
                        </p>
                    </div>

                    <!-- Tavsif -->
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Tavsif
                        </label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.description }"
                            placeholder="Fakultet haqida qisqacha ma'lumot..."
                        ></textarea>
                        <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                            {{ form.errors.description }}
                        </p>
                    </div>

                    <!-- Status -->
                    <div class="mb-6 flex items-center space-x-3 p-4 bg-gray-50 rounded-lg">
                        <input
                            v-model="form.is_active"
                            type="checkbox"
                            id="is_active"
                            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                        />
                        <label for="is_active" class="text-sm font-medium text-gray-700 cursor-pointer">
                            Fakultet faol
                        </label>
                        <span class="text-xs text-gray-500 ml-auto">
                            {{ form.is_active ? 'Faol' : 'Nofaol' }}
                        </span>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <Link
                            href="/faculties"
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition flex items-center space-x-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            <span>Bekor qilish</span>
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2"
                        >
                            <svg v-if="form.processing" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>{{ form.processing ? 'Saqlanmoqda...' : 'Saqlash' }}</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Info Card -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="flex-1">
                        <h4 class="text-sm font-medium text-blue-900 mb-1">Ma'lumot</h4>
                        <p class="text-sm text-blue-700">
                            Fakultet yaratilgandan so'ng, unga kafedralar va yo'nalishlar biriktirishingiz mumkin.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { useForm, Link, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    deans: Array,
});

const form = useForm({
    name: '',
    code: '',
    dean_id: null,
    description: '',
    is_active: true,
});

const submit = () => {
    form.post('/faculties', {
        preserveScroll: true,
    });
};
</script>