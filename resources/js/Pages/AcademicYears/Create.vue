<template>
    <AuthenticatedLayout>
        <template #header>Yangi O'quv Yili Qo'shish</template>

        <div class="max-w-3xl mx-auto">
            <div class="mb-6">
                <Link href="/academic-years"
                      class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                    <Icon :size="20" class="mr-1"><ArrowBackOutline /></Icon>
                    Orqaga qaytish
                </Link>
            </div>

            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">O'quv yili ma'lumotlari</h3>
                    <p class="text-sm text-gray-500 mt-1">Yangi o'quv yili yaratish uchun formani to'ldiring</p>
                </div>

                <form @submit.prevent="submit" class="p-6 space-y-6">
                    <!-- Nomi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            O'quv yili nomi <span class="text-red-500">*</span>
                        </label>
                        <input v-model="form.name" type="text"
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               :class="{ 'border-red-500': form.errors.name }"
                               placeholder="Masalan: 2024-2025" required />
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                        <p class="mt-1 text-xs text-gray-400">Format: YYYY-YYYY</p>
                    </div>

                    <!-- Sanalar -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Boshlanish sanasi <span class="text-red-500">*</span>
                            </label>
                            <input v-model="form.start_date" type="date"
                                   class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                   :class="{ 'border-red-500': form.errors.start_date }" required />
                            <p v-if="form.errors.start_date" class="mt-1 text-sm text-red-600">{{ form.errors.start_date }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Tugash sanasi <span class="text-red-500">*</span>
                            </label>
                            <input v-model="form.end_date" type="date"
                                   class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                   :class="{ 'border-red-500': form.errors.end_date }" required />
                            <p v-if="form.errors.end_date" class="mt-1 text-sm text-red-600">{{ form.errors.end_date }}</p>
                        </div>
                    </div>

                    <!-- Joriy o'quv yili -->
                    <div class="flex items-center">
                        <input v-model="form.is_active" type="checkbox" id="is_active"
                               class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" />
                        <label for="is_active" class="ml-2 block text-sm text-gray-700">
                            Joriy o'quv yili sifatida belgilash
                        </label>
                    </div>

                    <!-- Info -->
                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-start gap-3">
                            <Icon :size="18" class="text-blue-600 flex-shrink-0 mt-0.5"><InformationCircleOutline /></Icon>
                            <p class="text-sm text-blue-700">
                                O'quv yili odatda sentyabr oyidan boshlanib, keyingi yilning iyun oyida tugaydi.
                                Joriy deb belgilansangiz, avvalgi joriy o'quv yili avtomatik o'chiriladi.
                            </p>
                        </div>
                    </div>

                    <!-- Tugmalar -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                        <Link href="/academic-years"
                              class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            Bekor qilish
                        </Link>
                        <button type="submit" :disabled="form.processing"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors
                                       disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center gap-2">
                            <Icon v-if="form.processing" :size="18" class="animate-spin"><RefreshOutline /></Icon>
                            <Icon v-else :size="18"><SaveOutline /></Icon>
                            {{ form.processing ? 'Saqlanmoqda...' : 'Saqlash' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import { useToast } from '@/Composables/useToast'
import { Icon } from '@vicons/utils'
import { ArrowBackOutline, SaveOutline, RefreshOutline, InformationCircleOutline } from '@vicons/ionicons5'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const toast = useToast()

const form = useForm({
    name: '', start_date: '', end_date: '', is_active: false,
})

const submit = () => form.post('/academic-years', {
        preserveScroll: true,
        onSuccess: () => toast.success('O\'quv yili muvaffaqiyatli qo\'shildi!'),
    onError:   () => toast.error('Xatolik! Ma\'lumotlarni tekshiring.'),
})
</script>
