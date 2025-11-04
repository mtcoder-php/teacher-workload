<template>

    <Head title="Yangi O'qituvchi" />

    <AuthenticatedLayout>
        <template #header>Yangi O'qituvchi Qo'shish</template>

        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-xl font-semibold text-gray-800">O'qituvchi ma'lumotlari</h2>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="p-6 space-y-8">
                    <!-- User Information Section -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Shaxsiy ma'lumotlar
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Full Name -->
                            <div class="md:col-span-2">
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    F.I.O <span class="text-red-500">*</span>
                                </label>
                                <input id="name" v-model="form.name" type="text"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    :class="{ 'border-red-500': form.errors.name }"
                                    placeholder="Masalan: Aliyev Vali Karimovich" required />
                                <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.name }}
                                </p>
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input id="email" v-model="form.email" type="email"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    :class="{ 'border-red-500': form.errors.email }" placeholder="example@university.uz"
                                    required />
                                <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.email }}
                                </p>
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Telefon
                                </label>
                                <input id="phone" v-model="form.phone" type="text"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    :class="{ 'border-red-500': form.errors.phone }" placeholder="+998 XX XXX XX XX" />
                                <p v-if="form.errors.phone" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.phone }}
                                </p>
                            </div>

                            <!-- Password -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Parol <span class="text-red-500">*</span>
                                </label>
                                <input id="password" v-model="form.password" type="password"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    :class="{ 'border-red-500': form.errors.password }" placeholder="••••••••"
                                    required />
                                <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.password }}
                                </p>
                            </div>

                            <!-- Password Confirmation -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                    Parolni tasdiqlash <span class="text-red-500">*</span>
                                </label>
                                <input id="password_confirmation" v-model="form.password_confirmation" type="password"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    placeholder="••••••••" required />
                            </div>
                        </div>
                    </div>

                    <!-- Professional Information Section -->
                    <div class="pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Kasbiy ma'lumotlar
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Department -->
                            <div>
                                <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Kafedra <span class="text-red-500">*</span>
                                </label>
                                <select id="department_id" v-model="form.department_id"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    :class="{ 'border-red-500': form.errors.department_id }" required>
                                    <option :value="null">Kafedra tanlang</option>
                                    <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                                        {{ dept.name }} ({{ dept.faculty?.name }})
                                    </option>
                                </select>
                                <p v-if="form.errors.department_id" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.department_id }}
                                </p>
                            </div>

                            <!-- Position -->
                            <div>
                                <label for="position" class="block text-sm font-medium text-gray-700 mb-2">
                                    Lavozim
                                </label>
                                <input id="position" v-model="form.position" type="text"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    :class="{ 'border-red-500': form.errors.position }"
                                    placeholder="Masalan: Katta o'qituvchi" />
                                <p v-if="form.errors.position" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.position }}
                                </p>
                            </div>

                            <!-- Employment Type -->
                            <div>
                                <label for="employment_type" class="block text-sm font-medium text-gray-700 mb-2">
                                    Bandlik turi <span class="text-red-500">*</span>
                                </label>
                                <select id="employment_type" v-model="form.employment_type"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    :class="{ 'border-red-500': form.errors.employment_type }" required>
                                    <option value="">Tanlang</option>
                                    <option value="main_job">Asosiy ish joyi</option>
                                    <option value="internal_part_time">O'rindoshlik (ichki-asosiy)</option>
                                    <option value="internal_additional">O'rindoshlik (ichki-qo'shimcha)</option>
                                    <option value="external_part_time">O'rindoshlik (tashqi)</option>
                                    <option value="hourly">Soatbay</option>
                                </select>
                                <p v-if="form.errors.employment_type" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.employment_type }}
                                </p>
                            </div>

                            <!-- Hire Date -->
                            <div>
                                <label for="hire_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Ishga qabul qilish sanasi
                                </label>
                                <input id="hire_date" v-model="form.hire_date" type="date"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    :class="{ 'border-red-500': form.errors.hire_date }" />
                                <p v-if="form.errors.hire_date" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.hire_date }}
                                </p>
                            </div>

                            <!-- Academic Degree -->
                            <div>
                                <label for="academic_degree" class="block text-sm font-medium text-gray-700 mb-2">
                                    Ilmiy daraja
                                </label>
                                <input id="academic_degree" v-model="form.academic_degree" type="text"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    placeholder="Masalan: Texnika fanlari nomzodi" />
                            </div>

                            <!-- Academic Title -->
                            <div>
                                <label for="academic_title" class="block text-sm font-medium text-gray-700 mb-2">
                                    Ilmiy unvon
                                </label>
                                <input id="academic_title" v-model="form.academic_title" type="text"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    placeholder="Masalan: Dotsent" />
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information Section -->
                    <div class="pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Qo'shimcha ma'lumotlar
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Birth Date -->
                            <div>
                                <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tug'ilgan sana
                                </label>
                                <input id="birth_date" v-model="form.birth_date" type="date"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                            </div>

                            <!-- Passport Serial -->
                            <div>
                                <label for="passport_serial" class="block text-sm font-medium text-gray-700 mb-2">
                                    Pasport seriyasi
                                </label>
                                <input id="passport_serial" v-model="form.passport_serial" type="text"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    placeholder="AA 1234567" />
                            </div>

                            <!-- INN -->
                            <div>
                                <label for="inn" class="block text-sm font-medium text-gray-700 mb-2">
                                    INN
                                </label>
                                <input id="inn" v-model="form.inn" type="text"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    placeholder="123456789" />
                            </div>

                            <!-- Address -->
                            <div class="md:col-span-2">
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                    Manzil
                                </label>
                                <textarea id="address" v-model="form.address" rows="3"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    placeholder="To'liq yashash manzili"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <Link href="/teachers"
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>Orqaga</span>
                        </Link>
                        <button type="submit" :disabled="form.processing"
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2">
                            <svg v-if="form.processing" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
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

const props = defineProps({
    departments: Array,
});

const form = useForm({
    // User fields
    name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',

    // Teacher fields
    department_id: null,
    position: '',
    academic_degree: '',
    academic_title: '',
    employment_type: '',
    hire_date: '',
    birth_date: '',
    passport_serial: '',
    inn: '',
    address: '',
});

const submit = () => {
    form.post('/teachers', {
        preserveScroll: true,
    });
};
</script>