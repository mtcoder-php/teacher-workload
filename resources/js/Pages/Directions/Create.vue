<template>
    <Head title="Yangi yo'nalish qo'shish" />

    <AuthenticatedLayout>
        <template #header>Yangi yo'nalish qo'shish</template>

        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <!-- Form Header -->
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Yo'nalish ma'lumotlari</h3>
                    <p class="text-sm text-gray-600 mt-1">Yangi ta'lim yo'nalishi qo'shish uchun quyidagi ma'lumotlarni to'ldiring</p>
                </div>

                <!-- Form Body -->
                <form @submit.prevent="submit" class="p-6 space-y-6">
                    <!-- Yo'nalish nomi -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Yo'nalish nomi <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.name }"
                            placeholder="Masalan: Dasturiy injiniring"
                        />
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                    </div>

                    <!-- Kod -->
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                            Yo'nalish kodi <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="code"
                            v-model="form.code"
                            type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.code }"
                            placeholder="Masalan: 60610100"
                        />
                        <p v-if="form.errors.code" class="mt-1 text-sm text-red-600">{{ form.errors.code }}</p>
                    </div>

                    <!-- Kafedra -->
                    <div>
                        <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Kafedra <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="department_id"
                            v-model="form.department_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.department_id }"
                        >
                            <option :value="null">Kafedrani tanlang</option>
                            <option v-for="department in departments" :key="department.id" :value="department.id">
                                {{ department.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.department_id" class="mt-1 text-sm text-red-600">{{ form.errors.department_id }}</p>
                    </div>

                    <!-- Ta'lim darajasi -->
                    <div>
                        <label for="degree_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Ta'lim darajasi <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="degree_type"
                            v-model="form.degree_type"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.degree_type }"
                        >
                            <option :value="null">Ta'lim darajasini tanlang</option>
                            <option value="bakalavr">Bakalavr</option>
                            <option value="magistratura">Magistratura</option>
                        </select>
                        <p v-if="form.errors.degree_type" class="mt-1 text-sm text-red-600">{{ form.errors.degree_type }}</p>
                    </div>

                    <!-- Davomiyligi -->
                    <div>
                        <label for="duration_years" class="block text-sm font-medium text-gray-700 mb-2">
                            Ta'lim davomiyligi (yil) <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="duration_years"
                            v-model.number="form.duration_years"
                            type="number"
                            min="1"
                            max="6"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.duration_years }"
                            placeholder="Masalan: 4"
                        />
                        <p v-if="form.errors.duration_years" class="mt-1 text-sm text-red-600">{{ form.errors.duration_years }}</p>
                    </div>

                    <!-- Izoh -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Izoh
                        </label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Yo'nalish haqida qo'shimcha ma'lumot..."
                        ></textarea>
                    </div>

                    <!-- Status -->
                    <div class="flex items-center">
                        <input
                            id="is_active"
                            v-model="form.is_active"
                            type="checkbox"
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                        />
                        <label for="is_active" class="ml-2 text-sm font-medium text-gray-700">
                            Faol yo'nalish
                        </label>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                        <Link
                            href="/directions"
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition"
                        >
                            Bekor qilish
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2"
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
import { useForm, Link, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    departments: Array,
});

const form = useForm({
    name: null,
    code: null,
    department_id: null,
    degree_type: null,
    duration_years: 4,
    description: null,
    is_active: true,
});

const submit = () => {
    form.post('/directions', {
        preserveScroll: true,
    });
};
</script>
