<template>
    <Head title="Profil" />
    
    <AuthenticatedLayout>
        <template #header>Profil sozlamalari</template>

        <div class="max-w-7xl mx-auto space-y-6">
            <!-- Profile Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-xl font-semibold text-gray-800">Profil ma'lumotlari</h2>
                </div>
                <div class="p-6">
                    <form @submit.prevent="updateProfile" class="space-y-6">
                        <!-- Avatar Section -->
                        <div class="flex items-start space-x-6">
                            <!-- Avatar Display -->
                            <div class="flex-shrink-0">
                                <div class="relative">
                                    <div class="w-32 h-32 rounded-full overflow-hidden bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                                        <img 
                                            v-if="previewAvatar || user.avatar_url" 
                                            :src="previewAvatar || user.avatar_url" 
                                            alt="Avatar"
                                            class="w-full h-full object-cover"
                                        />
                                        <span v-else class="text-4xl font-bold text-white">
                                            {{ user.name.charAt(0).toUpperCase() }}
                                        </span>
                                    </div>
                                    <button
                                        v-if="user.avatar_url && !previewAvatar"
                                        @click="deleteAvatar"
                                        type="button"
                                        class="absolute -top-2 -right-2 p-2 bg-red-500 text-white rounded-full hover:bg-red-600 shadow-lg"
                                        title="Avatar o'chirish"
                                    >
                                        <Icon :size="16">
                                            <TrashOutline />
                                        </Icon>
                                    </button>
                                </div>
                            </div>

                            <!-- Avatar Upload -->
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Profil rasmi
                                </label>
                                <div class="flex items-center space-x-3">
                                    <label class="cursor-pointer px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition text-sm">
                                        <Icon :size="16" class="inline mr-2">
                                            <CloudUploadOutline />
                                        </Icon>
                                        Rasm yuklash
                                        <input 
                                            type="file" 
                                            @change="handleAvatarChange" 
                                            accept="image/*"
                                            class="hidden"
                                            ref="avatarInput"
                                        />
                                    </label>
                                    <button
                                        v-if="previewAvatar"
                                        @click="cancelAvatarChange"
                                        type="button"
                                        class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800"
                                    >
                                        Bekor qilish
                                    </button>
                                </div>
                                <p class="mt-2 text-xs text-gray-500">
                                    JPG, PNG yoki GIF. Maksimal 2MB.
                                </p>
                                <p v-if="form.errors.avatar" class="mt-1 text-xs text-red-600">
                                    {{ form.errors.avatar }}
                                </p>
                            </div>
                        </div>

                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                To'liq ism
                            </label>
                            <input
                                v-model="form.name"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                required
                            />
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Email
                            </label>
                            <input
                                v-model="form.email"
                                type="email"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                required
                            />
                            <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                                {{ form.errors.email }}
                            </p>
                            <p v-if="!user.email_verified_at" class="mt-2 text-sm text-yellow-600">
                                ⚠️ Email tasdiqlanmagan
                            </p>
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Telefon raqam
                            </label>
                            <input
                                v-model="form.phone"
                                type="text"
                                placeholder="+998901234567"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            />
                            <p v-if="form.errors.phone" class="mt-1 text-sm text-red-600">
                                {{ form.errors.phone }}
                            </p>
                        </div>

                        <!-- Role (Read-only) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Rol
                            </label>
                            <input
                                :value="user.role?.display_name || 'Noma\'lum'"
                                type="text"
                                class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg cursor-not-allowed"
                                disabled
                            />
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 transition"
                            >
                                <span v-if="form.processing">Saqlanmoqda...</span>
                                <span v-else>Saqlash</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Update Password -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-xl font-semibold text-gray-800">Parolni o'zgartirish</h2>
                </div>
                <div class="p-6">
                    <form @submit.prevent="updatePassword" class="space-y-6">
                        <!-- Current Password -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Joriy parol
                            </label>
                            <input
                                v-model="passwordForm.current_password"
                                type="password"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                required
                            />
                            <p v-if="passwordForm.errors.current_password" class="mt-1 text-sm text-red-600">
                                {{ passwordForm.errors.current_password }}
                            </p>
                        </div>

                        <!-- New Password -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Yangi parol
                            </label>
                            <input
                                v-model="passwordForm.password"
                                type="password"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                required
                            />
                            <p v-if="passwordForm.errors.password" class="mt-1 text-sm text-red-600">
                                {{ passwordForm.errors.password }}
                            </p>
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Parolni tasdiqlash
                            </label>
                            <input
                                v-model="passwordForm.password_confirmation"
                                type="password"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                required
                            />
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button
                                type="submit"
                                :disabled="passwordForm.processing"
                                class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 transition"
                            >
                                <span v-if="passwordForm.processing">Saqlanmoqda...</span>
                                <span v-else>Parolni yangilash</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Delete Account -->
            <div class="bg-white rounded-xl shadow-sm border border-red-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-red-200 bg-red-50">
                    <h2 class="text-xl font-semibold text-red-800">Akkauntni o'chirish</h2>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-600 mb-4">
                        Akkauntni o'chirilsa, barcha ma'lumotlar butunlay yo'qoladi. Bu amalni qaytarib bo'lmaydi!
                    </p>
                    <button
                        @click="showDeleteModal = true"
                        class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
                    >
                        Akkauntni o'chirish
                    </button>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Teleport to="body">
            <Transition name="modal">
                <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
                    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Akkauntni o'chirish</h3>
                        <p class="text-gray-600 mb-6">
                            Rostdan ham akkauntni o'chirmoqchimisiz? Bu amal qaytarilmaydi!
                        </p>
                        
                        <form @submit.prevent="deleteAccount" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Parolni kiriting
                                </label>
                                <input
                                    v-model="deleteForm.password"
                                    type="password"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
                                    required
                                />
                                <p v-if="deleteForm.errors.password" class="mt-1 text-sm text-red-600">
                                    {{ deleteForm.errors.password }}
                                </p>
                            </div>

                            <div class="flex space-x-3">
                                <button
                                    type="button"
                                    @click="showDeleteModal = false"
                                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition"
                                >
                                    Bekor qilish
                                </button>
                                <button
                                    type="submit"
                                    :disabled="deleteForm.processing"
                                    class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50 transition"
                                >
                                    O'chirish
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { Icon } from '@vicons/utils';
import { 
    TrashOutline, 
    CloudUploadOutline 
} from '@vicons/ionicons5';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    user: Object,
});

// Profile Update Form
const form = useForm({
    name: props.user.name,
    email: props.user.email,
    phone: props.user.phone,
    avatar: null,
});

const previewAvatar = ref(null);
const avatarInput = ref(null);

const handleAvatarChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.avatar = file;
        previewAvatar.value = URL.createObjectURL(file);
    }
};

const cancelAvatarChange = () => {
    form.avatar = null;
    previewAvatar.value = null;
    if (avatarInput.value) {
        avatarInput.value.value = '';
    }
};

// Profile update
const updateProfile = () => {
    form.post(route('profile.update'), {  // POST ishlaydi
        preserveScroll: true,
        onSuccess: () => {
            previewAvatar.value = null;
            if (avatarInput.value) {
                avatarInput.value.value = '';
            }
        },
    });
};

// Password update
const updatePassword = () => {
    passwordForm.post(route('profile.password.update'), {  // POST ishlaydi
        preserveScroll: true,
        onSuccess: () => {
            passwordForm.reset();
        },
    });
};

const deleteAvatar = () => {
    if (confirm('Avatar o\'chirilsinmi?')) {
        router.delete(route('profile.avatar.delete'), {
            preserveScroll: true,
        });
    }
};

// Password Update Form
const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});



// Delete Account
const showDeleteModal = ref(false);
const deleteForm = useForm({
    password: '',
});

const deleteAccount = () => {
    deleteForm.delete(route('profile.destroy'), {
        preserveScroll: true,
    });
};
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-enter-active .bg-white,
.modal-leave-active .bg-white {
    transition: transform 0.3s ease;
}

.modal-enter-from .bg-white,
.modal-leave-to .bg-white {
    transform: scale(0.9);
}
</style>