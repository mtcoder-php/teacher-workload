<template>
    <AuthenticatedLayout>
        <template #header>Fakultetni tahrirlash</template>

        <div class="max-w-3xl mx-auto">
            <!-- Orqaga -->
            <div class="mb-6">
                <Link href="/faculties"
                      class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                    <Icon :size="20" class="mr-1"><ArrowBackOutline /></Icon>
                    Orqaga qaytish
                </Link>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Fakultet ma'lumotlari</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ faculty.name }}</p>
                    </div>
                    <Link :href="`/faculties/${faculty.id}`"
                          class="text-sm text-indigo-600 hover:text-indigo-700">Ko'rish →</Link>
                </div>

                <form @submit.prevent="submit" class="p-6 space-y-6">
                    <!-- Nomi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Fakultet nomi <span class="text-red-500">*</span>
                        </label>
                        <input v-model="form.name" type="text"
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               :class="{ 'border-red-500': form.errors.name }"
                               placeholder="Masalan: Informatika fakulteti" required />
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                    </div>

                    <!-- Kodi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Fakultet kodi <span class="text-red-500">*</span>
                        </label>
                        <input v-model="form.code" type="text"
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               :class="{ 'border-red-500': form.errors.code }"
                               placeholder="Masalan: IF" maxlength="10" required />
                        <p v-if="form.errors.code" class="mt-1 text-sm text-red-600">{{ form.errors.code }}</p>
                    </div>

                    <!-- Dekan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dekan</label>
                        <select v-model="form.dean_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option :value="null">Dekan tanlanmagan</option>
                            <option v-for="dean in deans" :key="dean.id" :value="dean.id">
                                {{ dean.name }} ({{ dean.email }})
                            </option>
                        </select>
                        <p v-if="form.errors.dean_id" class="mt-1 text-sm text-red-600">{{ form.errors.dean_id }}</p>
                    </div>

                    <!-- Tavsif -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tavsif</label>
                        <textarea v-model="form.description" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                  placeholder="Fakultet haqida qisqacha ma'lumot..."></textarea>
                        <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                    </div>

                    <!-- Faol -->
                    <div class="flex items-center">
                        <input v-model="form.is_active" type="checkbox" id="is_active"
                               class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" />
                        <label for="is_active" class="ml-2 block text-sm text-gray-700">Fakultet faol</label>
                    </div>

                    <!-- Tugmalar -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                        <Link href="/faculties"
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

const toast = useToast()

const props = defineProps({ faculty: Object, deans: Array })

const form = useForm({
    name: props.faculty.name,
    code: props.faculty.code,
    dean_id: props.faculty.dean_id,
    description: props.faculty.description || '',
    is_active: props.faculty.is_active ?? true,
})

const submit = () => form.put(`/faculties/${props.faculty.id}`, {
    preserveScroll: true,
    onSuccess: () => toast.success('Fakultet muvaffaqiyatli yangilandi!'),
    onError:   () => toast.error("Xatolik! Ma'lumotlarni tekshiring."),
})
</script>
