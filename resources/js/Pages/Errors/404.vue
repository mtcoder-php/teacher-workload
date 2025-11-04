<template>
    <Head title="404 - Sahifa topilmadi" />
    
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 flex items-center justify-center p-4 relative overflow-hidden">
        <!-- Animated Background -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-blue-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
            <div class="absolute top-1/3 right-1/4 w-96 h-96 bg-indigo-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
            <div class="absolute bottom-1/4 left-1/3 w-96 h-96 bg-purple-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000"></div>
        </div>

        <div class="max-w-4xl w-full relative z-10">
            <!-- Main Card -->
            <div class="bg-white/90 backdrop-blur-sm rounded-3xl shadow-2xl overflow-hidden border border-white/20">
                <!-- Content -->
                <div class="px-8 py-12 text-center">
                    <!-- 404 Illustration -->
                    <div class="mb-8 relative">
                        <!-- Main 404 -->
                        <div class="flex items-center justify-center space-x-4">
                            <div class="text-9xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 animate-bounce-slow">
                                4
                            </div>
                            
                            <!-- Animated Search Icon -->
                            <div class="relative">
                                <div class="w-32 h-32 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center animate-spin-slow">
                                    <div class="w-24 h-24 rounded-full bg-gradient-to-br from-indigo-200 to-purple-200 flex items-center justify-center">
                                        <svg class="w-12 h-12 text-indigo-600 animate-wiggle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="absolute inset-0 rounded-full bg-gradient-to-br from-indigo-400 to-purple-400 blur-xl opacity-30 animate-pulse"></div>
                            </div>
                            
                            <div class="text-9xl font-black text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 animate-bounce-slow animation-delay-300">
                                4
                            </div>
                        </div>
                        
                        <!-- Floating Elements -->
                        <div class="absolute top-0 left-1/4 animate-float">
                            <div class="w-6 h-6 bg-blue-400 rounded-full opacity-50"></div>
                        </div>
                        <div class="absolute bottom-0 right-1/4 animate-float animation-delay-2000">
                            <div class="w-8 h-8 bg-indigo-400 rounded-full opacity-50"></div>
                        </div>
                        <div class="absolute top-1/2 left-1/3 animate-float animation-delay-4000">
                            <div class="w-5 h-5 bg-purple-400 rounded-full opacity-50"></div>
                        </div>
                    </div>

                    <!-- Message -->
                    <div class="mb-10 animate-fade-in-up">
                        <h1 class="text-4xl font-bold text-gray-900 mb-4">
                            Sahifa topilmadi
                        </h1>
                        <p class="text-xl text-gray-600 mb-3">
                            {{ message || "Kechirasiz, siz qidirayotgan sahifa mavjud emas." }}
                        </p>
                        <p class="text-gray-500">
                            Havola noto'g'ri yoki sahifa o'chirilgan bo'lishi mumkin.
                        </p>
                    </div>

                    <!-- Quick Links -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10">
                        <button
                            @click="goToDashboard"
                            class="group bg-gradient-to-br from-blue-50 to-blue-100 hover:from-blue-100 hover:to-blue-200 rounded-xl p-6 border border-blue-200 transition-all duration-300 transform hover:scale-105 hover:shadow-lg"
                        >
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mx-auto mb-3 group-hover:rotate-12 transition-transform duration-300">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                            </div>
                            <h3 class="font-semibold text-blue-900 mb-1">Bosh sahifa</h3>
                            <p class="text-sm text-blue-700">Dashboard ga o'tish</p>
                        </button>

                        <button
                            @click="goBack"
                            class="group bg-gradient-to-br from-indigo-50 to-indigo-100 hover:from-indigo-100 hover:to-indigo-200 rounded-xl p-6 border border-indigo-200 transition-all duration-300 transform hover:scale-105 hover:shadow-lg"
                        >
                            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center mx-auto mb-3 group-hover:-rotate-12 transition-transform duration-300">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                            </div>
                            <h3 class="font-semibold text-indigo-900 mb-1">Orqaga</h3>
                            <p class="text-sm text-indigo-700">Oldingi sahifa</p>
                        </button>

                        <a
                            href="/help"
                            class="group bg-gradient-to-br from-purple-50 to-purple-100 hover:from-purple-100 hover:to-purple-200 rounded-xl p-6 border border-purple-200 transition-all duration-300 transform hover:scale-105 hover:shadow-lg"
                        >
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="font-semibold text-purple-900 mb-1">Yordam</h3>
                            <p class="text-sm text-purple-700">Qo'llab-quvvatlash</p>
                        </a>
                    </div>

                    <!-- Search Box -->
                    <div class="max-w-xl mx-auto mb-8">
                        <div class="relative">
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Sahifa qidirish..."
                                class="w-full px-6 py-4 pr-12 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-300 outline-none"
                                @keyup.enter="searchPage"
                            >
                            <button
                                @click="searchPage"
                                class="absolute right-2 top-1/2 -translate-y-1/2 w-10 h-10 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg flex items-center justify-center hover:from-indigo-700 hover:to-purple-700 transition-all duration-300 group"
                            >
                                <svg class="w-5 h-5 text-white group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Popular Links -->
                    <div class="border-t border-gray-200 pt-8">
                        <p class="text-sm text-gray-600 mb-4">Mashhur sahifalar:</p>
                        <div class="flex flex-wrap justify-center gap-2">
                            <Link
                                href="/dashboard"
                                class="px-4 py-2 bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-700 rounded-lg transition-all duration-200 text-sm font-medium"
                            >
                                Dashboard
                            </Link>
                            <Link
                                href="/workloads"
                                class="px-4 py-2 bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-700 rounded-lg transition-all duration-200 text-sm font-medium"
                            >
                                Yuklamalar
                            </Link>
                            <Link
                                href="/teachers"
                                class="px-4 py-2 bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-700 rounded-lg transition-all duration-200 text-sm font-medium"
                            >
                                O'qituvchilar
                            </Link>
                            <Link
                                href="/reports"
                                class="px-4 py-2 bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-700 rounded-lg transition-all duration-200 text-sm font-medium"
                            >
                                Hisobotlar
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-8 animate-fade-in">
                <p class="text-sm text-gray-600 bg-white/50 backdrop-blur-sm rounded-full px-6 py-2 inline-block shadow-sm">
                    <span class="font-semibold">Error 404</span> • Page Not Found • <span class="text-indigo-500">🔍</span> Sahifa topilmadi
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    message: {
        type: String,
        default: "Kechirasiz, siz qidirayotgan sahifa mavjud emas.",
    },
});

const searchQuery = ref('');

const goToDashboard = () => {
    router.visit('/dashboard');
};

const goBack = () => {
    window.history.back();
};

const searchPage = () => {
    if (searchQuery.value.trim()) {
        // Simple search - redirect to dashboard with query
        router.visit(`/dashboard?search=${encodeURIComponent(searchQuery.value)}`);
    }
};
</script>

<style scoped>
@keyframes blob {
    0%, 100% {
        transform: translate(0, 0) scale(1);
    }
    25% {
        transform: translate(20px, -50px) scale(1.1);
    }
    50% {
        transform: translate(-20px, 20px) scale(0.9);
    }
    75% {
        transform: translate(50px, 50px) scale(1.05);
    }
}

@keyframes float {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-20px);
    }
}

@keyframes bounce-slow {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

@keyframes spin-slow {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

@keyframes wiggle {
    0%, 100% {
        transform: rotate(0deg);
    }
    25% {
        transform: rotate(-10deg);
    }
    75% {
        transform: rotate(10deg);
    }
}

@keyframes fade-in-up {
    from {
        transform: translateY(20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes fade-in {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

.animate-bounce-slow {
    animation: bounce-slow 2s ease-in-out infinite;
}

.animate-spin-slow {
    animation: spin-slow 8s linear infinite;
}

.animate-wiggle {
    animation: wiggle 1s ease-in-out infinite;
}

.animate-fade-in-up {
    animation: fade-in-up 0.8s ease-out;
}

.animate-fade-in {
    animation: fade-in 1s ease-out;
}

.animation-delay-300 {
    animation-delay: 0.3s;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}
</style>