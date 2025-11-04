<template>
    <Head title="403 - Ruxsat yo'q" />
    
    <div class="min-h-screen bg-gradient-to-br from-red-50 via-orange-50 to-yellow-50 flex items-center justify-center p-4 relative overflow-hidden">
        <!-- Animated Background -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-red-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
            <div class="absolute top-1/3 right-1/4 w-96 h-96 bg-orange-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
            <div class="absolute bottom-1/4 left-1/3 w-96 h-96 bg-yellow-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000"></div>
        </div>

        <div class="max-w-3xl w-full relative z-10">
            <!-- Main Card -->
            <div class="bg-white/90 backdrop-blur-sm rounded-3xl shadow-2xl overflow-hidden border border-white/20">


                <!-- Content -->
                <div class="px-8 py-12">
                    <!-- Main Message with Icon -->
                    <div class="text-center mb-10 animate-fade-in-up">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-4">
                            <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <p class="text-xl text-gray-800 font-semibold mb-3">
                            {{ message || "Sizda bu sahifaga kirish huquqi yo'q" }}
                        </p>
                        <p class="text-gray-600 max-w-md mx-auto">
                            Agar bu xato deb o'ylasangiz, administrator bilan bog'lanib, kerakli ruxsatlarni so'rang.
                        </p>
                    </div>

                    <!-- Info Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10">
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5 border border-blue-200 transform hover:scale-105 transition-transform duration-300">
                            <div class="flex items-center space-x-3 mb-2">
                                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                </div>
                                <h3 class="font-semibold text-blue-900">Dashboard</h3>
                            </div>
                            <p class="text-sm text-blue-700">Bosh sahifaga qaytish</p>
                        </div>

                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-5 border border-purple-200 transform hover:scale-105 transition-transform duration-300">
                            <div class="flex items-center space-x-3 mb-2">
                                <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <h3 class="font-semibold text-purple-900">Administrator</h3>
                            </div>
                            <p class="text-sm text-purple-700">Ruxsat so'rang</p>
                        </div>

                        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5 border border-green-200 transform hover:scale-105 transition-transform duration-300">
                            <div class="flex items-center space-x-3 mb-2">
                                <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h3 class="font-semibold text-green-900">Yordam</h3>
                            </div>
                            <p class="text-sm text-green-700">Qo'llab-quvvatlash</p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                        <button
                            @click="goToDashboard"
                            class="group relative px-8 py-4 bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-600 text-white rounded-xl hover:shadow-2xl transition-all duration-300 flex items-center justify-center space-x-3 overflow-hidden"
                        >
                            <div class="absolute inset-0 bg-gradient-to-r from-indigo-700 via-purple-700 to-indigo-700 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                            <svg class="w-5 h-5 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span class="font-semibold relative z-10">Dashboard</span>
                        </button>
                        
                        <button
                            @click="goBack"
                            class="group px-8 py-4 bg-white border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 hover:border-gray-400 hover:shadow-lg transition-all duration-300 flex items-center justify-center space-x-3"
                        >
                            <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            <span class="font-semibold">Orqaga qaytish</span>
                        </button>
                    </div>

                    <!-- Help Section -->
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-2xl p-6 border border-gray-200">
                        <div class="flex items-center justify-between flex-wrap gap-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">Yordam kerakmi?</p>
                                    <p class="text-xs text-gray-600">Biz sizga yordam berishga tayyormiz</p>
                                </div>
                            </div>
                            <div class="flex space-x-3">
                                <a 
                                    href="mailto:support@example.com" 
                                    class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200 flex items-center space-x-2 text-sm font-medium text-gray-700"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <span>Email</span>
                                </a>
                                <a 
                                    href="/help" 
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors duration-200 flex items-center space-x-2 text-sm font-medium"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Yordam</span>
                                </a>
                            </div>
                        </div>
                    </div>
                       <!-- Footer -->
            <div class="text-center mt-3 animate-fade-in">
                <p class="text-sm text-gray-600 bg-white/50 backdrop-blur-sm rounded-full px-6 py-2 inline-block shadow-sm">
                    <span class="font-semibold">Error 403</span> • Forbidden Access • <span class="text-red-500">⚠️</span> Kirish taqiqlangan
                </p>
            </div>
                </div>
            </div>

         
        </div>
    </div>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3';

defineProps({
    message: {
        type: String,
        default: "Sizda bu sahifaga kirish huquqi yo'q",
    },
});

const goToDashboard = () => {
    router.visit('/dashboard');
};

const goBack = () => {
    window.history.back();
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
        transform: translateY(0) rotate(0deg);
    }
    50% {
        transform: translateY(-20px) rotate(180deg);
    }
}

@keyframes pulse-slow {
    0%, 100% {
        transform: scale(1);
        opacity: 1;
    }
    50% {
        transform: scale(1.08);
        opacity: 0.8;
    }
}

@keyframes lock {
    0%, 100% {
        transform: rotate(0deg);
    }
    25% {
        transform: rotate(-5deg);
    }
    75% {
        transform: rotate(5deg);
    }
}

@keyframes slide-down {
    from {
        transform: translateY(-30px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes slide-up {
    from {
        transform: translateY(30px);
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

.animate-blob {
    animation: blob 7s infinite;
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}

.animate-pulse-slow {
    animation: pulse-slow 3s ease-in-out infinite;
}

.animate-lock {
    animation: lock 2s ease-in-out infinite;
}

.animate-slide-down {
    animation: slide-down 0.8s ease-out;
}

.animate-slide-up {
    animation: slide-up 0.8s ease-out 0.2s backwards;
}

.animate-fade-in {
    animation: fade-in 1s ease-out 0.4s backwards;
}

.animate-fade-in-up {
    animation: fade-in-up 0.8s ease-out 0.3s backwards;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}
</style>