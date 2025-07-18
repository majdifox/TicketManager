<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-black to-gray-900 flex flex-col justify-center items-center p-4 relative overflow-hidden">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-blue-500/5 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-purple-500/5 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
            <div class="absolute top-3/4 left-1/2 w-64 h-64 bg-cyan-500/5 rounded-full blur-3xl animate-pulse" style="animation-delay: 4s;"></div>
        </div>

        <!-- Logo Section -->
        <div class="relative z-10 mb-8">
            <Link href="/" class="group block">
                <div class="relative">
                    <!-- Glow Effect -->
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl blur-xl opacity-50 group-hover:opacity-75 transition-opacity duration-500"></div>
                    
                    <!-- Logo Container -->
                    <div class="relative bg-gradient-to-r from-blue-500 to-purple-600 p-6 rounded-2xl transform group-hover:scale-105 transition-transform duration-300">
                        <ApplicationLogo class="w-16 h-16 text-white" />
                    </div>
                </div>
                
                <!-- App Name -->
                <div class="text-center mt-4">
                    <h1 class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                        Support Hub
                    </h1>
                    <p class="text-white/60 text-sm mt-1">Your gateway to excellence</p>
                </div>
            </Link>
        </div>

        <!-- Auth Form Container -->
        <div class="relative z-10 w-full max-w-md">
            <!-- Glassmorphism Card -->
            <div class="relative group">
                <!-- Background with glassmorphism effect -->
                <div class="absolute inset-0 bg-gradient-to-r from-white/10 to-white/5 backdrop-blur-xl rounded-2xl border border-white/20 shadow-2xl"></div>
                
                <!-- Subtle border glow -->
                <div class="absolute inset-0 bg-gradient-to-r from-blue-500/20 to-purple-500/20 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 blur-sm"></div>
                
                <!-- Content -->
                <div class="relative bg-black/20 backdrop-blur-xl rounded-2xl border border-white/10 p-8 shadow-2xl transform transition-all duration-300 hover:scale-[1.02]">
                    <slot />
                </div>
            </div>
        </div>

        <!-- Decorative Elements -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 text-center">
            <div class="flex items-center space-x-2 text-white/40 text-xs">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                </svg>
                <span>Secure & Encrypted</span>
            </div>
        </div>

        <!-- Floating Particles -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="particle particle-1"></div>
            <div class="particle particle-2"></div>
            <div class="particle particle-3"></div>
            <div class="particle particle-4"></div>
            <div class="particle particle-5"></div>
        </div>
    </div>
</template>

<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<style scoped>
/* Floating particles animation */
.particle {
    position: absolute;
    background: radial-gradient(circle, rgba(59, 130, 246, 0.3) 0%, transparent 70%);
    border-radius: 50%;
    animation: float 6s ease-in-out infinite;
}

.particle-1 {
    width: 4px;
    height: 4px;
    top: 20%;
    left: 10%;
    animation-delay: 0s;
}

.particle-2 {
    width: 6px;
    height: 6px;
    top: 40%;
    right: 15%;
    animation-delay: 1s;
}

.particle-3 {
    width: 3px;
    height: 3px;
    top: 60%;
    left: 20%;
    animation-delay: 2s;
}

.particle-4 {
    width: 5px;
    height: 5px;
    top: 80%;
    right: 25%;
    animation-delay: 3s;
}

.particle-5 {
    width: 4px;
    height: 4px;
    top: 70%;
    left: 70%;
    animation-delay: 4s;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0px) translateX(0px);
        opacity: 0.3;
    }
    25% {
        transform: translateY(-20px) translateX(10px);
        opacity: 0.6;
    }
    50% {
        transform: translateY(-10px) translateX(-5px);
        opacity: 0.8;
    }
    75% {
        transform: translateY(-30px) translateX(15px);
        opacity: 0.4;
    }
}

/* Custom input styling for forms inside this layout */
:deep(.form-input) {
    @apply bg-white/5 border-white/20 text-white placeholder-white/50 rounded-xl focus:ring-blue-500 focus:border-blue-500 transition-all duration-300;
}

:deep(.form-input:focus) {
    @apply bg-white/10 border-blue-500 shadow-lg shadow-blue-500/25;
}

:deep(.btn-primary) {
    @apply bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-semibold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300;
}

:deep(.btn-secondary) {
    @apply bg-white/10 hover:bg-white/20 text-white font-medium py-2 px-4 rounded-xl border border-white/20 hover:border-white/30 transition-all duration-300;
}

:deep(label) {
    @apply text-white/80 font-medium text-sm;
}

:deep(a) {
    @apply text-blue-400 hover:text-blue-300 transition-colors duration-200;
}
</style>