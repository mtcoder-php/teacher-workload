<template>
    <AuthenticatedLayout>
        <template #header>Yangi Fan Qo'shish</template>

        <div class="max-w-5xl mx-auto">
            <div class="mb-6">
                <Link href="/subjects"
                      class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                    <Icon :size="20" class="mr-1"><ArrowBackOutline /></Icon>
                    Orqaga qaytish
                </Link>
            </div>

            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Fan ma'lumotlari</h3>
                    <p class="text-sm text-gray-500 mt-1">Yangi fan yaratish uchun formani to'ldiring</p>
                </div>

                <form @submit.prevent="submit" class="p-6 space-y-8">

                    <!-- 1. Asosiy -->
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <Icon :size="18" class="text-indigo-600"><BookOutline /></Icon>
                            Asosiy ma'lumotlar
                        </h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Fan nomi <span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.name" type="text"
                                       class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                       :class="{ 'border-red-500': form.errors.name }"
                                       placeholder="Dasturlash asoslari" required />
                                <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Fan kodi <span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.code" type="text"
                                       class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent uppercase"
                                       :class="{ 'border-red-500': form.errors.code }"
                                       placeholder="CS101" required />
                                <p v-if="form.errors.code" class="mt-1 text-sm text-red-600">{{ form.errors.code }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Kafedra <span class="text-red-500">*</span>
                                </label>
                                <select v-model="form.department_id"
                                        class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                        :class="{ 'border-red-500': form.errors.department_id }" required>
                                    <option :value="null">Tanlang</option>
                                    <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
                                </select>
                                <p v-if="form.errors.department_id" class="mt-1 text-sm text-red-600">{{ form.errors.department_id }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Yo'nalish</label>
                                <select v-model="form.direction_id"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                    <option :value="null">Tanlang</option>
                                    <option v-for="d in filteredDirections" :key="d.id" :value="d.id">
                                        {{ d.name }} ({{ d.code }})
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Kurs <span class="text-red-500">*</span>
                                </label>
                                <select v-model="form.course_level"
                                        class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                        required>
                                    <option :value="null">Tanlang</option>
                                    <option v-for="l in courseLevels" :key="l.value" :value="l.value">{{ l.label }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Kredit soat <span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.credit_hours" type="number" min="1" max="10"
                                       class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                       placeholder="1-10" required />
                            </div>
                        </div>
                    </div>

                    <!-- 2. Fan turlari -->
                    <div class="pt-4 border-t border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <Icon :size="18" class="text-indigo-600"><LayersOutline /></Icon>
                            Fan turlari
                        </h4>
                        <div class="grid grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Fan turi <span class="text-red-500">*</span>
                                </label>
                                <select v-model="form.subject_type"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm"
                                        required>
                                    <option v-for="t in subjectTypes" :key="t.value" :value="t.value">{{ t.label }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ta'lim shakli</label>
                                <select v-model="form.education_form"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                                    <option :value="null">Tanlang</option>
                                    <option v-for="e in educationForms" :key="e.value" :value="e.value">{{ e.label }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">1-sem nazorat</label>
                                <select v-model="form.semester_1_control"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                                    <option :value="null">Tanlang</option>
                                    <option v-for="c in controlTypes" :key="c.value" :value="c.value">{{ c.label }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">2-sem nazorat</label>
                                <select v-model="form.semester_2_control"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                                    <option :value="null">Tanlang</option>
                                    <option v-for="c in controlTypes" :key="c.value" :value="c.value">{{ c.label }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- 3. 1-Semestr soatlar -->
                    <div class="pt-4 border-t border-gray-200">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-green-500 inline-block"></span>
                                1-Semestr soatlari
                            </h4>
                            <span class="text-sm font-medium text-green-700 bg-green-50 px-3 py-1 rounded-full">
                                Jami: {{ semester1Total }}
                            </span>
                        </div>
                        <div class="grid grid-cols-7 gap-3">
                            <div v-for="f in sem1Fields" :key="f.key">
                                <label class="block text-xs font-medium text-gray-600 mb-1">{{ f.label }}</label>
                                <input v-model="form[f.key]" type="number" min="0" step="0.5"
                                       class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-green-500"
                                       placeholder="0" />
                            </div>
                        </div>
                    </div>

                    <!-- 4. 2-Semestr soatlar -->
                    <div class="pt-4 border-t border-gray-200">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-purple-500 inline-block"></span>
                                2-Semestr soatlari
                            </h4>
                            <span class="text-sm font-medium text-purple-700 bg-purple-50 px-3 py-1 rounded-full">
                                Jami: {{ semester2Total }}
                            </span>
                        </div>
                        <div class="grid grid-cols-7 gap-3">
                            <div v-for="f in sem2Fields" :key="f.key">
                                <label class="block text-xs font-medium text-gray-600 mb-1">{{ f.label }}</label>
                                <input v-model="form[f.key]" type="number" min="0" step="0.5"
                                       class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-purple-500"
                                       placeholder="0" />
                            </div>
                        </div>
                    </div>

                    <!-- 5. Qo'shimcha soatlar -->
                    <div class="pt-4 border-t border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-orange-500 inline-block"></span>
                            Qo'shimcha soatlar
                        </h4>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kurs ishi</label>
                                <input v-model="form.coursework_hours" type="number" min="0" step="0.5"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm"
                                       placeholder="0" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Diplom ishi</label>
                                <input v-model="form.diploma_hours" type="number" min="0" step="0.5"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm"
                                       placeholder="0" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Konsultatsiya</label>
                                <input v-model="form.consultation_hours" type="number" min="0" step="0.5"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm"
                                       placeholder="0" />
                            </div>
                        </div>
                    </div>

                    <!-- 6. Qo'shimcha sozlamalar -->
                    <div class="pt-4 border-t border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-900 mb-4">Qo'shimcha sozlamalar</h4>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tavsif</label>
                                <textarea v-model="form.description" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm"
                                          placeholder="Fan haqida qisqacha..." maxlength="1000"></textarea>
                                <p class="mt-1 text-xs text-gray-400">{{ form.description?.length || 0 }}/1000</p>
                            </div>
                            <div class="flex flex-wrap gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input v-model="form.is_active" type="checkbox"
                                           class="w-4 h-4 text-indigo-600 rounded" />
                                    <span class="text-sm text-gray-700">Fan faol</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input v-model="form.can_be_potok" type="checkbox"
                                           class="w-4 h-4 text-indigo-600 rounded" />
                                    <span class="text-sm text-gray-700">Potok mumkin</span>
                                </label>
                                <div v-if="form.can_be_potok" class="flex items-center gap-2">
                                    <label class="text-sm text-gray-700">Min guruhlar:</label>
                                    <input v-model="form.min_groups_for_potok" type="number" min="2"
                                           class="w-20 px-2 py-1 border border-gray-300 rounded text-sm" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Jami -->
                    <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Umumiy jami soat</p>
                                <p class="text-2xl font-bold text-indigo-600">{{ totalHours }}</p>
                            </div>
                            <div class="text-right text-sm text-gray-600 space-y-1">
                                <p>1-semestr: <span class="font-semibold text-green-600">{{ semester1Total }}</span></p>
                                <p>2-semestr: <span class="font-semibold text-purple-600">{{ semester2Total }}</span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Tugmalar -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                        <Link href="/subjects"
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
import { useToast } from '@/Composables/useToast'
import { Icon } from '@vicons/utils'
import { ArrowBackOutline, SaveOutline, RefreshOutline, BookOutline, LayersOutline } from '@vicons/ionicons5'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const toast = useToast()

const props = defineProps({
    departments:    { type: Array, default: () => [] },
    directions:     { type: Array, default: () => [] },
    courseLevels:   { type: Array, default: () => [] },
    subjectTypes:   { type: Array, default: () => [] },
    educationForms: { type: Array, default: () => [] },
    controlTypes:   { type: Array, default: () => [] },
})

const form = useForm({
    name: '', code: '', department_id: null, direction_id: null, course_level: null,
    subject_type: 'asosiy', education_form: null, credit_hours: null,
    semester_1_lecture:0, semester_1_practical:0, semester_1_laboratory:0,
    semester_1_seminar:0, semester_1_practice:0, semester_1_exam:0, semester_1_test:0,
    semester_1_control: null,
    semester_2_lecture:0, semester_2_practical:0, semester_2_laboratory:0,
    semester_2_seminar:0, semester_2_practice:0, semester_2_exam:0, semester_2_test:0,
    semester_2_control: null,
    coursework_hours:0, diploma_hours:0, consultation_hours:0,
    description:'', is_active:true, can_be_potok:false, min_groups_for_potok:2,
})

const sem1Fields = [
    {key:'semester_1_lecture',label:"Ma'ruza"}, {key:'semester_1_seminar',label:'Seminar'},
    {key:'semester_1_practical',label:'Amaliy'}, {key:'semester_1_laboratory',label:'Lab'},
    {key:'semester_1_practice',label:'Amaliyot'}, {key:'semester_1_exam',label:'Imtihon'},
    {key:'semester_1_test',label:'Sinov'},
]
const sem2Fields = [
    {key:'semester_2_lecture',label:"Ma'ruza"}, {key:'semester_2_seminar',label:'Seminar'},
    {key:'semester_2_practical',label:'Amaliy'}, {key:'semester_2_laboratory',label:'Lab'},
    {key:'semester_2_practice',label:'Amaliyot'}, {key:'semester_2_exam',label:'Imtihon'},
    {key:'semester_2_test',label:'Sinov'},
]

const sum = (keys) => keys.reduce((s,k) => s + parseFloat(form[k]||0), 0)

const semester1Total = computed(() => sum(sem1Fields.map(f=>f.key)).toFixed(1))
const semester2Total = computed(() => sum(sem2Fields.map(f=>f.key)).toFixed(1))
const totalHours = computed(() => (
    parseFloat(semester1Total.value) + parseFloat(semester2Total.value) +
    parseFloat(form.coursework_hours||0) + parseFloat(form.diploma_hours||0) + parseFloat(form.consultation_hours||0)
).toFixed(1))

const filteredDirections = computed(() =>
    form.department_id ? props.directions?.filter(d => d.department_id === form.department_id) : props.directions
)

const submit = () => form.post(`/subjects`, {
    preserveScroll: true,
    onSuccess: () => toast.success("Fan muvaffaqiyatli qo'shildi!"),
    onError:   () => toast.error("Xatolik! Ma'lumotlarni tekshiring."),
})
</script>
