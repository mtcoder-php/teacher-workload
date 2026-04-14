<template>
    <AuthenticatedLayout>
        <template #header>Yo'nalishni tahrirlash</template>

        <div class="max-w-3xl mx-auto">
            <div class="mb-6">
                <Link href="/directions"
                      class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                    <Icon :size="20" class="mr-1"><ArrowBackOutline /></Icon>
                    Orqaga qaytish
                </Link>
            </div>

            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Yo'nalish ma'lumotlari</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ direction.name }}</p>
                    </div>
                    <Link :href="`/directions/${direction.id}`"
                          class="text-sm text-indigo-600 hover:text-indigo-700">Ko'rish →</Link>
                </div>

                <form @submit.prevent="submit" class="p-6 space-y-6">
                    <!-- Nomi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Yo'nalish nomi <span class="text-red-500">*</span>
                        </label>
                        <input v-model="form.name" type="text"
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               :class="{ 'border-red-500': form.errors.name }" required />
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                    </div>

                    <!-- Kodi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Yo'nalish kodi <span class="text-red-500">*</span>
                        </label>
                        <input v-model="form.code" type="text"
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               :class="{ 'border-red-500': form.errors.code }" required />
                        <p v-if="form.errors.code" class="mt-1 text-sm text-red-600">{{ form.errors.code }}</p>
                    </div>

                    <!-- Kafedra -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Kafedra <span class="text-red-500">*</span>
                        </label>
                        <select v-model="form.department_id"
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.department_id }" required>
                            <option :value="null">Kafedrani tanlang</option>
                            <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
                        </select>
                        <p v-if="form.errors.department_id" class="mt-1 text-sm text-red-600">{{ form.errors.department_id }}</p>
                    </div>

                    <!-- Ta'lim darajasi + davomiyligi -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Ta'lim darajasi <span class="text-red-500">*</span>
                            </label>
                            <select v-model="form.degree_type"
                                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    :class="{ 'border-red-500': form.errors.degree_type }" required>
                                <option value="bakalavr">Bakalavr</option>
                                <option value="magistratura">Magistratura</option>
                            </select>
                            <p v-if="form.errors.degree_type" class="mt-1 text-sm text-red-600">{{ form.errors.degree_type }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Davomiyligi (yil) <span class="text-red-500">*</span>
                            </label>
                            <input v-model.number="form.duration_years" type="number" min="1" max="6"
                                   class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                   :class="{ 'border-red-500': form.errors.duration_years }" required />
                            <p v-if="form.errors.duration_years" class="mt-1 text-sm text-red-600">{{ form.errors.duration_years }}</p>
                        </div>
                    </div>

                    <!-- Tavsif -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tavsif</label>
                        <textarea v-model="form.description" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                  placeholder="Yo'nalish haqida qo'shimcha ma'lumot..."></textarea>
                    </div>

                    <!-- Faol -->
                    <div class="flex items-center">
                        <input v-model="form.is_active" type="checkbox" id="is_active"
                               class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" />
                        <label for="is_active" class="ml-2 block text-sm text-gray-700">Faol yo'nalish</label>
                    </div>

                    <!-- Tugmalar -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                        <Link href="/directions"
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

const props = defineProps({ direction: Object, departments: Array })

const form = useForm({
    name: props.direction.name,
    code: props.direction.code,
    department_id: props.direction.department_id,
    degree_type: props.direction.degree_type,
    duration_years: props.direction.duration_years,
    description: props.direction.description || '',
    is_active: props.direction.is_active,
})

const submit = () => form.put(`/directions/${props.direction.id}`, {
    preserveScroll: true,
    onSuccess: () => toast.success("Yo'nalish muvaffaqiyatli yangilandi!"),
    onError:   () => toast.error("Xatolik! Ma'lumotlarni tekshiring."),
})
</script>
