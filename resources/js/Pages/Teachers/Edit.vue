<template>
    <AuthenticatedLayout>
        <template #header>O'qituvchini tahrirlash</template>

        <div class="max-w-4xl mx-auto">
            <div class="mb-6">
                <Link href="/teachers"
                      class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                    <Icon :size="20" class="mr-1"><ArrowBackOutline /></Icon>
                    Orqaga qaytish
                </Link>
            </div>

            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">O'qituvchi ma'lumotlari</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ teacher.user?.name }}</p>
                    </div>
                    <Link :href="`/teachers/${teacher.id}`"
                          class="text-sm text-indigo-600 hover:text-indigo-700">Ko'rish →</Link>
                </div>

                <form @submit.prevent="submit" class="p-6 space-y-6">

                    <!-- 1. Shaxsiy -->
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <Icon :size="18" class="text-indigo-600"><PersonOutline /></Icon>
                            Shaxsiy ma'lumotlar
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    F.I.O <span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.name" type="text"
                                       class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                       :class="{ 'border-red-500': form.errors.name }" required />
                                <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.email" type="email"
                                       class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                       :class="{ 'border-red-500': form.errors.email }" required />
                                <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
                                <input v-model="form.phone" type="text"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Yangi parol
                                    <span class="text-gray-400 font-normal">(ixtiyoriy)</span>
                                </label>
                                <input v-model="form.password" type="password"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                       placeholder="••••••••" />
                                <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Parolni tasdiqlash</label>
                                <input v-model="form.password_confirmation" type="password"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                       placeholder="••••••••" />
                            </div>
                        </div>
                    </div>

                    <!-- 2. Kasbiy -->
                    <div class="pt-4 border-t border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <Icon :size="18" class="text-indigo-600"><BriefcaseOutline /></Icon>
                            Kasbiy ma'lumotlar
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Kafedra <span class="text-red-500">*</span>
                                </label>
                                <select v-model="form.department_id"
                                        class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                        :class="{ 'border-red-500': form.errors.department_id }" required>
                                    <option :value="null">Kafedra tanlang</option>
                                    <option v-for="d in departments" :key="d.id" :value="d.id">
                                        {{ d.name }} ({{ d.faculty?.name }})
                                    </option>
                                </select>
                                <p v-if="form.errors.department_id" class="mt-1 text-sm text-red-600">{{ form.errors.department_id }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Lavozim</label>
                                <input v-model="form.position" type="text"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Bandlik turi <span class="text-red-500">*</span>
                                </label>
                                <select v-model="form.employment_type"
                                        class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                        :class="{ 'border-red-500': form.errors.employment_type }" required>
                                    <option value="">Tanlang</option>
                                    <option value="main_job">Asosiy ish joyi</option>
                                    <option value="internal_part_time">O'rindoshlik (ichki-asosiy)</option>
                                    <option value="internal_additional">O'rindoshlik (ichki-qo'shimcha)</option>
                                    <option value="external_part_time">O'rindoshlik (tashqi)</option>
                                    <option value="hourly">Soatbay</option>
                                </select>
                                <p v-if="form.errors.employment_type" class="mt-1 text-sm text-red-600">{{ form.errors.employment_type }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ishga qabul sanasi</label>
                                <input v-model="form.hire_date" type="date"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ilmiy daraja</label>
                                <input v-model="form.academic_degree" type="text"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ilmiy unvon</label>
                                <input v-model="form.academic_title" type="text"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                            </div>
                        </div>
                    </div>

                    <!-- 3. Qo'shimcha -->
                    <div class="pt-4 border-t border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <Icon :size="18" class="text-indigo-600"><DocumentTextOutline /></Icon>
                            Qo'shimcha ma'lumotlar
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tug'ilgan sana</label>
                                <input v-model="form.birth_date" type="date"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pasport seriyasi</label>
                                <input v-model="form.passport_serial" type="text"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">INN</label>
                                <input v-model="form.inn" type="text"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Manzil</label>
                                <textarea v-model="form.address" rows="2"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Faol -->
                    <div class="flex items-center pt-2">
                        <input v-model="form.is_active" type="checkbox" id="is_active"
                               class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" />
                        <label for="is_active" class="ml-2 block text-sm text-gray-700">O'qituvchi faol</label>
                    </div>

                    <!-- Tugmalar -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                        <Link href="/teachers"
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
import {
    ArrowBackOutline, SaveOutline, RefreshOutline,
    PersonOutline, BriefcaseOutline, DocumentTextOutline,
} from '@vicons/ionicons5'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const toast = useToast()

const props = defineProps({ teacher: Object, departments: Array })

const fmtDate = (d) => {
    if (!d) return ''
    const dt = new Date(d)
    return `${dt.getFullYear()}-${String(dt.getMonth()+1).padStart(2,'0')}-${String(dt.getDate()).padStart(2,'0')}`
}

const form = useForm({
    name: props.teacher.user?.name || '',
    email: props.teacher.user?.email || '',
    phone: props.teacher.user?.phone || '',
    password: '', password_confirmation: '',
    department_id: props.teacher.department_id || null,
    position: props.teacher.position || '',
    academic_degree: props.teacher.academic_degree || '',
    academic_title: props.teacher.academic_title || '',
    employment_type: props.teacher.employment_type || '',
    hire_date: fmtDate(props.teacher.hire_date),
    birth_date: fmtDate(props.teacher.birth_date),
    passport_serial: props.teacher.passport_serial || '',
    inn: props.teacher.inn || '',
    address: props.teacher.address || '',
    is_active: props.teacher.is_active ?? true,
})

const submit = () => form.put(`/teachers/${props.teacher.id}`, {
    preserveScroll: true,
    onSuccess: () => toast.success('O\'qituvchi muvaffaqiyatli yangilandi!'),
    onError:   () => toast.error('Xatolik! Ma\'lumotlarni tekshiring.'),
})
</script>
