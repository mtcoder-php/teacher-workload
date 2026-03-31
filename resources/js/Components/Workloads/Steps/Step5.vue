<template>
    <div class="space-y-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Tasdiqlash</h3>
            <p class="text-sm text-gray-500 mt-1">Barcha ma'lumotlarni tekshiring va tasdiqlang</p>
        </div>

        <!-- Asosiy ma'lumotlar -->
        <div class="summary-card">
            <h4 class="summary-title">🏛️ Asosiy ma'lumotlar</h4>
            <div class="summary-grid">
                <SummaryRow label="Kafedra"    :value="departmentName" />
                <SummaryRow label="Yo'nalish(lar)" :value="directionNames" />
                <SummaryRow label="Kurs"        :value="form.course ? form.course + '-kurs' : '—'" />
                <SummaryRow label="Yuklama turi" :value="form.is_potok ? 'Potokli' : 'Potoksiz'"
                            :badge="form.is_potok ? 'purple' : 'blue'" />
            </div>
        </div>

        <!-- Guruhlar -->
        <div class="summary-card">
            <h4 class="summary-title">👥 Guruhlar ({{ form.group_ids.length }} ta, jami {{ totalStudents }} talaba)</h4>
            <div class="flex flex-wrap gap-2 mt-2">
                <span
                    v-for="id in form.group_ids"
                    :key="id"
                    class="text-xs bg-blue-100 text-blue-700 px-2.5 py-1 rounded-full"
                >
                    {{ groupName(id) }} ({{ groupStudents(id) }} talaba)
                </span>
            </div>
        </div>

        <!-- Fan -->
        <div class="summary-card">
            <h4 class="summary-title">📚 Fan</h4>
            <div class="summary-grid">
                <SummaryRow label="Fan nomi" :value="subjectName" />
                <SummaryRow label="Fan kodi" :value="subjectCode" />
                <SummaryRow label="Jami soat" :value="totalHours + ' soat'" />
            </div>
        </div>

        <!-- Soatlar jadvali -->
        <div class="summary-card overflow-x-auto">
            <h4 class="summary-title">⏱️ Soatlar taqsimoti</h4>
            <table class="w-full text-sm mt-3">
                <thead>
                <tr class="border-b border-gray-200">
                    <th class="text-left py-2 pr-4 text-gray-500 font-medium">Turi</th>
                    <th class="text-right py-2 px-3 text-gray-500 font-medium">1-semestr</th>
                    <th class="text-right py-2 px-3 text-gray-500 font-medium">2-semestr</th>
                    <th class="text-right py-2 text-gray-700 font-semibold">Jami</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                <tr v-for="row in hoursSummary" :key="row.label" v-show="row.s1 > 0 || row.s2 > 0">
                    <td class="py-2 pr-4 text-gray-700">{{ row.label }}</td>
                    <td class="text-right py-2 px-3 text-gray-600">{{ row.s1 || '—' }}</td>
                    <td class="text-right py-2 px-3 text-gray-600">{{ row.s2 || '—' }}</td>
                    <td class="text-right py-2 font-semibold text-gray-800">{{ (row.s1 + row.s2) || '—' }}</td>
                </tr>
                <tr class="border-t-2 border-gray-300 bg-gray-50">
                    <td class="py-2 pr-4 font-bold text-gray-800">Jami</td>
                    <td class="text-right py-2 px-3 font-semibold">{{ sem1Total }}</td>
                    <td class="text-right py-2 px-3 font-semibold">{{ sem2Total }}</td>
                    <td class="text-right py-2 font-bold text-blue-700">{{ totalHours }}</td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- O'qituvchi -->
        <div class="summary-card">
            <h4 class="summary-title">👨‍🏫 O'qituvchi</h4>
            <div class="flex items-center gap-3 mt-2">
                <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">
                    {{ (teacherName || '?').charAt(0) }}
                </div>
                <div>
                    <div class="font-medium text-gray-800">{{ teacherName || '—' }}</div>
                    <div v-if="form.notes" class="text-xs text-gray-500 mt-0.5">Izoh: {{ form.notes }}</div>
                </div>
            </div>
        </div>

        <!-- Tasdiqlash eslatmasi -->
        <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">
            ✅ Barcha ma'lumotlar to'g'ri bo'lsa, pastdagi <strong>"Yuklama yaratish"</strong> tugmasini bosing.
            Yoki <strong>"Qoralama"</strong> sifatida saqlashingiz mumkin.
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import SummaryRow from '@/Components/Workloads/SummaryRow.vue'

const props = defineProps({
    form:        { type: Object, required: true },
    departments: { type: Array, default: () => [] },
    directions:  { type: Array, default: () => [] },
    subjects:    { type: Array, default: () => [] },
    groups:      { type: Array, default: () => [] },
    teachers:    { type: Array, default: () => [] },
})
defineEmits(['valid'])

// Lookup helpers
const departmentName = computed(() =>
    props.departments.find(d => d.id === props.form.department_id)?.name ?? '—'
)
const directionNames = computed(() =>
    props.form.direction_ids
        .map(id => props.directions.find(d => d.id === id)?.name ?? id)
        .join(', ') || '—'
)
const subjectName = computed(() =>
    props.subjects.find(s => s.id === props.form.subject_id)?.name ?? '—'
)
const subjectCode = computed(() =>
    props.subjects.find(s => s.id === props.form.subject_id)?.code ?? '—'
)
const teacherName = computed(() =>
    props.teachers.find(t => t.id === props.form.teacher_id)?.name ?? '—'
)

function groupName(id) { return props.groups.find(g => g.id === id)?.name ?? id }
function groupStudents(id) { return props.groups.find(g => g.id === id)?.student_count ?? 0 }

const totalStudents = computed(() =>
    props.form.group_ids.reduce((s, id) => s + groupStudents(id), 0)
)

// Soat qatorlari
const hoursSummary = [
    { label: "Ma'ruza",      s1key: 'semester_1_lecture',    s2key: 'semester_2_lecture'    },
    { label: 'Amaliy',       s1key: 'semester_1_practical',  s2key: 'semester_2_practical'  },
    { label: 'Laboratoriya', s1key: 'semester_1_laboratory', s2key: 'semester_2_laboratory' },
    { label: 'Seminar',      s1key: 'semester_1_seminar',    s2key: 'semester_2_seminar'    },
    { label: 'Amaliyot',     s1key: 'semester_1_practice',   s2key: 'semester_2_practice'   },
    { label: 'Imtihon',      s1key: 'semester_1_exam',       s2key: 'semester_2_exam'       },
    { label: 'Sinov',        s1key: 'semester_1_test',       s2key: 'semester_2_test'       },
].map(r => ({
    label: r.label,
    s1:    Number(props.form[r.s1key]) || 0,
    s2:    Number(props.form[r.s2key]) || 0,
}))

const sem1Total = computed(() => hoursSummary.reduce((s, r) => s + r.s1, 0))
const sem2Total = computed(() => hoursSummary.reduce((s, r) => s + r.s2, 0))
const totalHours = computed(() =>
    sem1Total.value + sem2Total.value +
    (Number(props.form.coursework_hours) || 0) +
    (Number(props.form.diploma_hours) || 0) +
    (Number(props.form.consultation_hours) || 0)
)
</script>

<style scoped>
.summary-card  { @apply p-4 border border-gray-200 rounded-xl space-y-1 bg-white; }
.summary-title { @apply text-sm font-semibold text-gray-700; }
.summary-grid  { @apply grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-1 mt-2; }
</style>
