<template>
    <AuthenticatedLayout>
        <template #header>Yangi Guruh Qo'shish</template>

        <div class="max-w-3xl mx-auto">
            <div class="mb-6">
                <Link href="/groups"
                      class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                    <Icon :size="20" class="mr-1"><ArrowBackOutline /></Icon>
                    Orqaga qaytish
                </Link>
            </div>

            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Guruh ma'lumotlari</h3>
                    <p class="text-sm text-gray-500 mt-1">Yangi guruh yaratish uchun formani to'ldiring</p>
                </div>

                <form @submit.prevent="submit" class="p-6 space-y-6">
                    <!-- Nomi va kodi -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Guruh nomi <span class="text-red-500">*</span>
                            </label>
                            <input v-model="form.name" type="text"
                                   class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                   :class="{ 'border-red-500': form.errors.name }"
                                   placeholder="Masalan: 101-guruh" required />
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Guruh kodi <span class="text-red-500">*</span>
                            </label>
                            <input v-model="form.code" type="text"
                                   class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                   :class="{ 'border-red-500': form.errors.code }"
                                   placeholder="Masalan: 101AK/25" required />
                            <p v-if="form.errors.code" class="mt-1 text-sm text-red-600">{{ form.errors.code }}</p>
                        </div>
                    </div>

                    <!-- Yo'nalish va kurs -->
                    <div class="grid grid-cols-3 gap-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Yo'nalish <span class="text-red-500">*</span>
                            </label>
                            <select v-model="form.direction_id" @change="form.course = null"
                                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    :class="{ 'border-red-500': form.errors.direction_id }" required>
                                <option :value="null">Yo'nalishni tanlang</option>
                                <option v-for="d in directions" :key="d.id" :value="d.id">
                                    {{ d.name }} ({{ d.degree_type }})
                                </option>
                            </select>
                            <p v-if="form.errors.direction_id" class="mt-1 text-sm text-red-600">{{ form.errors.direction_id }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Kurs <span class="text-red-500">*</span>
                            </label>
                            <select v-model.number="form.course" :disabled="!selectedDirection"
                                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                                           disabled:bg-gray-100 disabled:cursor-not-allowed"
                                    :class="{ 'border-red-500': form.errors.course }" required>
                                <option :value="null">Tanlang</option>
                                <option v-for="c in availableCourses" :key="c.value" :value="c.value">{{ c.label }}</option>
                            </select>
                            <p v-if="form.errors.course" class="mt-1 text-sm text-red-600">{{ form.errors.course }}</p>
                        </div>
                    </div>

                    <!-- Ta'lim shakli va tili -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Ta'lim shakli <span class="text-red-500">*</span>
                            </label>
                            <select v-model="form.education_type"
                                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    :class="{ 'border-red-500': form.errors.education_type }" required>
                                <option value="">Tanlang</option>
                                <option value="kunduzgi">Kunduzgi</option>
                                <option value="sirtqi">Sirtqi</option>
                                <option value="kechki">Kechki</option>
                                <option value="masofaviy">Masofaviy</option>
                            </select>
                            <p v-if="form.errors.education_type" class="mt-1 text-sm text-red-600">{{ form.errors.education_type }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Ta'lim tili <span class="text-red-500">*</span>
                            </label>
                            <select v-model="form.education_language"
                                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    :class="{ 'border-red-500': form.errors.education_language }" required>
                                <option value="">Tanlang</option>
                                <option value="uzbek">O'zbek tili</option>
                                <option value="russian">Rus tili</option>
                            </select>
                            <p v-if="form.errors.education_language" class="mt-1 text-sm text-red-600">{{ form.errors.education_language }}</p>
                        </div>
                    </div>

                    <!-- Talabalar soni -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Talabalar soni</label>
                        <input v-model.number="form.student_count" type="number" min="0" max="100"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               placeholder="0" />
                        <p v-if="form.errors.student_count" class="mt-1 text-sm text-red-600">{{ form.errors.student_count }}</p>
                    </div>

                    <!-- Faol -->
                    <div class="flex items-center">
                        <input v-model="form.is_active" type="checkbox" id="is_active"
                               class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" />
                        <label for="is_active" class="ml-2 block text-sm text-gray-700">Faol guruh</label>
                    </div>

                    <!-- Tugmalar -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                        <Link href="/groups"
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
import { computed } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import { Icon } from '@vicons/utils'
import { ArrowBackOutline, SaveOutline, RefreshOutline } from '@vicons/ionicons5'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({ directions: Array })

const form = useForm({
    name: '', code: '', direction_id: null, course: null,
    education_type: '', education_language: 'uzbek', student_count: 0, is_active: true,
})

const selectedDirection = computed(() =>
    props.directions?.find(d => d.id === form.direction_id) ?? null
)

const availableCourses = computed(() => {
    const deg = selectedDirection.value?.degree_type
    if (deg === 'bakalavr')    return [{value:1,label:'1-kurs'},{value:2,label:'2-kurs'},{value:3,label:'3-kurs'},{value:4,label:'4-kurs'}]
    if (deg === 'magistratura') return [{value:5,label:'1-kurs (Mag)'},{value:6,label:'2-kurs (Mag)'}]
    return []
})

const submit = () => form.post('/groups', { preserveScroll: true })
</script>
