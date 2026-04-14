<template>
    <AuthenticatedLayout>
        <template #header>Yangi Kafedra Qo'shish</template>

        <div class="max-w-3xl mx-auto">
            <div class="mb-6">
                <Link href="/departments"
                      class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                    <Icon :size="20" class="mr-1"><ArrowBackOutline /></Icon>
                    Orqaga qaytish
                </Link>
            </div>

            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Kafedra ma'lumotlari</h3>
                    <p class="text-sm text-gray-500 mt-1">Yangi kafedra yaratish uchun formani to'ldiring</p>
                </div>

                <form @submit.prevent="submit" class="p-6 space-y-6">
                    <!-- Fakultet -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Fakultet <span class="text-red-500">*</span>
                        </label>
                        <select v-model="form.faculty_id"
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.faculty_id }" required>
                            <option :value="null">Fakultet tanlang</option>
                            <option v-for="f in faculties" :key="f.id" :value="f.id">{{ f.name }}</option>
                        </select>
                        <p v-if="form.errors.faculty_id" class="mt-1 text-sm text-red-600">{{ form.errors.faculty_id }}</p>
                    </div>

                    <!-- Nomi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Kafedra nomi <span class="text-red-500">*</span>
                        </label>
                        <input v-model="form.name" type="text"
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               :class="{ 'border-red-500': form.errors.name }"
                               placeholder="Masalan: Dasturiy injiniring kafedrasi" required />
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                    </div>

                    <!-- Kodi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Kafedra kodi <span class="text-red-500">*</span>
                        </label>
                        <input v-model="form.code" type="text"
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               :class="{ 'border-red-500': form.errors.code }"
                               placeholder="Masalan: DI" required />
                        <p v-if="form.errors.code" class="mt-1 text-sm text-red-600">{{ form.errors.code }}</p>
                    </div>

                    <!-- Kafedra mudiri -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kafedra mudiri</label>
                        <select v-model="form.head_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option :value="null">Mudir tanlanmagan</option>
                            <option v-for="h in heads" :key="h.id" :value="h.id">
                                {{ h.name }} ({{ h.email }})
                            </option>
                        </select>
                        <p v-if="form.errors.head_id" class="mt-1 text-sm text-red-600">{{ form.errors.head_id }}</p>
                    </div>

                    <!-- Tavsif -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tavsif</label>
                        <textarea v-model="form.description" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                  placeholder="Kafedra haqida qisqacha ma'lumot..."></textarea>
                        <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                    </div>

                    <!-- Faol -->
                    <div class="flex items-center">
                        <input v-model="form.is_active" type="checkbox" id="is_active"
                               class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" />
                        <label for="is_active" class="ml-2 block text-sm text-gray-700">Kafedra faol</label>
                    </div>

                    <!-- Tugmalar -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                        <Link href="/departments"
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
import { ArrowBackOutline, SaveOutline, RefreshOutline } from '@vicons/ionicons5'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

defineProps({ faculties: Array, heads: Array })

const toast = useToast()

const form = useForm({
    faculty_id: null, name: '', code: '', head_id: null, description: '', is_active: true,
})

const submit = () => form.post(`/departments`, {
    preserveScroll: true,
    onSuccess: () => toast.success("Kafedra muvaffaqiyatli qo'shildi!"),
    onError:   () => toast.error("Xatolik! Ma'lumotlarni tekshiring."),
})
</script>
