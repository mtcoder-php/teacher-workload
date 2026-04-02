<template>
    <AuthenticatedLayout>
        <template #header>
            Excel Import
        </template>

        <div class="max-w-4xl mx-auto">

            <!-- Flash xabarlari -->
            <div v-if="$page.props.flash?.success"
                 class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ $page.props.flash.success }}
            </div>

            <div v-if="$page.props.flash?.error"
                 class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                <div class="flex items-start gap-2">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="font-semibold mb-1">Import xatosi:</p>
                        <pre class="whitespace-pre-wrap text-xs">{{ $page.props.flash.error }}</pre>
                    </div>
                </div>
            </div>

            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Excel orqali ma'lumot import qilish</h3>
                <p class="text-sm text-gray-500 mt-1">
                    Quyidagi tartibda import qiling:
                    <strong class="text-indigo-600">1. Yo'nalishlar → 2. Guruhlar → 3. O'qituvchilar → 4. Fanlar</strong>
                </p>
                <div class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-700">
                    ⚠️ Har bir import uchun avval <strong>"Namuna yuklash"</strong> tugmasini bosing.
                    Namuna faylda mavjud kafedralar va yo'nalishlar ro'yxati <strong>"Ma'lumotnoma"</strong> varaqida ko'rsatilgan.
                </div>
            </div>

            <!-- Import kartalar -->
            <div class="space-y-4">

                <!-- 1. Yo'nalishlar -->
                <ImportCard
                    title="1. Yo'nalishlar"
                    description="Kafedralar avval tizimda kiritilgan bo'lishi shart"
                    icon="🎓"
                    import-action="/admin/import/directions"
                    sample-action="/admin/import/sample/directions"
                    :columns="[
                        { key: 'nomi',    label: 'Yo\'nalish nomi',       required: true,  example: 'Dasturiy injiniring' },
                        { key: 'kodi',    label: 'Yo\'nalish kodi',       required: true,  example: '60110100' },
                        { key: 'kafedra', label: 'Kafedra nomi (DB dagi)',required: true,  example: 'Axborot tizimlari' },
                        { key: 'daraja',  label: 'Daraja',                required: true,  example: 'bakalavr' },
                        { key: 'yillar',  label: 'Ta\'lim davomiyligi',   required: true,  example: '4' },
                    ]"
                    :references="[
                        { title: 'Mavjud kafedralar', items: departments.map(d => d.name) }
                    ]"
                />

                <!-- 2. Guruhlar -->
                <ImportCard
                    title="2. Guruhlar"
                    description="Yo'nalishlar avval import qilingan bo'lishi shart"
                    icon="👥"
                    import-action="/admin/import/groups"
                    sample-action="/admin/import/sample/groups"
                    :columns="[
                        { key: 'nomi',           label: 'Guruh nomi',          required: true,  example: 'DI-101' },
                        { key: 'kodi',           label: 'Guruh kodi',          required: true,  example: 'DI-101' },
                        { key: 'yonalish_kodi',  label: 'Yo\'nalish kodi',     required: true,  example: '60110100' },
                        { key: 'kurs',           label: 'Kurs (1-6)',          required: true,  example: '1' },
                        { key: 'talabalar',      label: 'Talabalar soni',      required: true,  example: '25' },
                        { key: 'talim_turi',     label: 'Ta\'lim turi',        required: true,  example: 'kunduzgi' },
                        { key: 'til',            label: 'Ta\'lim tili',        required: true,  example: 'uzbek' },
                    ]"
                    :references="[
                        {
                            title: 'Mavjud yo\'nalishlar (yonalish_kodi ustuniga KODni yozing)',
                            items: directions.map(d => `${d.code} — ${d.name} (${d.department})`)
                        }
                    ]"
                />

                <!-- 3. O'qituvchilar -->
                <ImportCard
                    title="3. O'qituvchilar"
                    description="Kafedralar tizimda bo'lishi shart. Email va parol avtomatik yaratiladi"
                    icon="👨‍🏫"
                    import-action="/admin/import/teachers"
                    sample-action="/admin/import/sample/teachers"
                    :columns="[
                        { key: 'fish',         label: 'F.I.Sh (to\'liq)',   required: true,  example: 'Aliyev Vohid Baxtiyorovich' },
                        { key: 'email',        label: 'Email',              required: true,  example: 'aliyev@edu.uz' },
                        { key: 'kafedra',      label: 'Kafedra nomi',       required: true,  example: 'Axborot tizimlari' },
                        { key: 'lavozim',      label: 'Lavozim',            required: false, example: 'Dotsent' },
                        { key: 'ilmiy_daraja', label: 'Ilmiy daraja',       required: false, example: 'PhD' },
                        { key: 'ilmiy_unvon',  label: 'Ilmiy unvon',        required: false, example: 'Dotsent' },
                        { key: 'ish_turi',     label: 'Ish turi',           required: false, example: 'asosiy' },
                        { key: 'parol',        label: 'Parol',              required: false, example: 'Parol123!' },
                    ]"
                    :references="[
                        { title: 'Mavjud kafedralar', items: departments.map(d => d.name) },
                        { title: 'Ish turi qiymatlari', items: ['asosiy', 'ichki', 'tashqi', 'soatbay'] }
                    ]"
                />

                <!-- 4. Fanlar -->
                <ImportCard
                    title="4. Fanlar"
                    description="Kafedralar va yo'nalishlar avval tizimda bo'lishi shart"
                    icon="📚"
                    import-action="/admin/import/subjects"
                    sample-action="/admin/import/sample/subjects"
                    :columns="[
                        { key: 'nomi',            label: 'Fan nomi',          required: true,  example: 'Matematika' },
                        { key: 'kodi',            label: 'Fan kodi',          required: true,  example: 'MAT101' },
                        { key: 'kafedra',         label: 'Kafedra nomi',      required: true,  example: 'Axborot tizimlari' },
                        { key: 'yonalish_kodi',   label: 'Yo\'nalish kodi',   required: false, example: '60110100' },
                        { key: 'kurs',            label: 'Kurs',              required: false, example: '1' },
                        { key: 'kredit',          label: 'Kredit soat',       required: false, example: '3' },
                        { key: 'turi',            label: 'Fan turi',          required: false, example: 'majburiy' },
                        { key: 's1_maruza',       label: '1-sem Ma\'ruza',    required: false, example: '36' },
                        { key: 's1_amaliy',       label: '1-sem Amaliy',      required: false, example: '18' },
                        { key: 's1_laboratoriya', label: '1-sem Lab',         required: false, example: '0' },
                        { key: 's1_seminar',      label: '1-sem Seminar',     required: false, example: '0' },
                        { key: 's1_amaliyot',     label: '1-sem Amaliyot',    required: false, example: '0' },
                        { key: 's1_imtihon',      label: '1-sem Imtihon',     required: false, example: '0' },
                        { key: 's1_sinov',        label: '1-sem Sinov',       required: false, example: '0' },
                        { key: 's2_maruza',       label: '2-sem Ma\'ruza',    required: false, example: '0' },
                        { key: 's2_amaliy',       label: '2-sem Amaliy',      required: false, example: '0' },
                        { key: 's2_laboratoriya', label: '2-sem Lab',         required: false, example: '0' },
                        { key: 's2_seminar',      label: '2-sem Seminar',     required: false, example: '0' },
                        { key: 's2_amaliyot',     label: '2-sem Amaliyot',    required: false, example: '0' },
                        { key: 's2_imtihon',      label: '2-sem Imtihon',     required: false, example: '0' },
                        { key: 's2_sinov',        label: '2-sem Sinov',       required: false, example: '0' },
                        { key: 'kurs_ishi',       label: 'Kurs ishi',         required: false, example: '0' },
                        { key: 'diplom_ishi',     label: 'Diplom ishi',       required: false, example: '0' },
                        { key: 'konsultatsiya',   label: 'Konsultatsiya',     required: false, example: '4' },
                        { key: 'potok_mumkin',    label: 'Potok (1/0)',       required: false, example: '1' },
                        { key: 'min_guruhlar',    label: 'Min guruhlar',      required: false, example: '2' },
                    ]"
                    :references="[
                        { title: 'Mavjud kafedralar', items: departments.map(d => d.name) },
                        { title: 'Mavjud yo\'nalishlar (kod — nomi)', items: directions.map(d => `${d.code} — ${d.name}`) }
                    ]"
                />

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import ImportCard from '@/Components/Import/ImportCard.vue'

const props = defineProps({
    departments: { type: Array, default: () => [] },
    directions:  { type: Array, default: () => [] },
})
</script>
