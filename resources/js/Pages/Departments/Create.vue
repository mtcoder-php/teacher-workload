<template>
    <Head title="Yangi Kafedra Qo'shish"/>
    <AuthenticatedLayout>
        <template #header>Yangi Kafedra Qo'shish</template>

        <div class="max-w-3xl mx-auto">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-xl font-semibold text-gray-800">Kafedra ma'lumotlari</h2>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="p-6 space-y-6">
                    <!-- Faculty Selection -->
                    <div>
                        <label for="faculty_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Fakultet <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="faculty_id"
                            v-model="form.faculty_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.faculty_id }"
                            required
                        >
                            <option :value="null">Fakultet tanlang</option>
                            <option v-for="faculty in faculties" :key="faculty.id" :value="faculty.id">
                                {{ faculty.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.faculty_id" class="mt-1 text-sm text-red-600">
                            {{ form.errors.faculty_id }}
                        </p>
                    </div>

                    <!-- Department Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Kafedra nomi <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.name }"
                            placeholder="Masalan: Dasturiy injiniring kafedrasi"
                            required
                        />
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <!-- Department Code -->
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                            Kafedra kodi <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="code"
                            v-model="form.code"
                            type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.code }"
                            placeholder="Masalan: DI"
                            required
                        />
                        <p v-if="form.errors.code" class="mt-1 text-sm text-red-600">
                            {{ form.errors.code }}
                        </p>
                    </div>

                    <!-- Head Selection -->
                    <div>
                        <label for="head_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Kafedra mudiri
                        </label>
                        <select
                            id="head_id"
                            v-model="form.head_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.head_id }"
                        >
                            <option :value="null">Mudir tanlang</option>
                            <option v-for="head in heads" :key="head.id" :value="head.id">
                                {{ head.name }} ({{ head.email }})
                            </option>
                        </select>
                        <p v-if="form.errors.head_id" class="mt-1 text-sm text-red-600">
                            {{ form.errors.head_id }}
                        </p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Tavsif
                        </label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.description }"
                            placeholder="Kafedra haqida qisqacha ma'lumot..."
                        ></textarea>
                        <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                            {{ form.errors.description }}
                        </p>
                    </div>

                    <!-- Active Status -->
                    <div class="flex items-center space-x-3 p-4 bg-gray-50 rounded-lg">
                        <input
                            v-model="form.is_active"
                            type="checkbox"
                            id="is_active"
                            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                        />
                        <label for="is_active" class="text-sm font-medium text-gray-700 cursor-pointer">
                            Kafedra faol
                        </label>
                        <span class="text-xs text-gray-500 ml-auto">
                            {{ form.is_active ? 'Faol' : 'Nofaol' }}
                        </span>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <Link
                            href="/departments"
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
                            Kafedra yaratilgandan so'ng, unga o'qituvchilar va fanlar biriktirishingiz mumkin.
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
    faculties: Array,
    heads: Array,
});

const form = useForm({
    faculty_id: null,
    name: '',
    code: '',
    head_id: null,
    description: '',
    is_active: true,
});

const submit = () => {
    form.post('/departments', {
        preserveScroll: true,
    });
};
</script>