<!-- AdminLogin.vue -->
<template>
    <Head title="Admin Login" />

    <div class="min-h-screen bg-white flex items-center justify-center px-4">
        <div class="w-full max-w-sm">
            <div class="text-center mb-12">
                <Link href="/" class="inline-block">
                    <img 
                        src="/images/logo.png" 
                        alt="Logo" 
                        class="h-12 w-auto mx-auto mb-6"
                    />
                    <h1 class="text-2xl font-medium text-black tracking-tight">Admin Portal</h1>
                </Link>
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                    </div>
                    <input
                        id="email"
                        type="email"
                        placeholder="Email"
                        class="w-full pl-10 pr-3 py-4 text-base text-black placeholder-gray-400 bg-gray-50 border-0 hover:bg-gray-100 focus:bg-white focus:outline-none focus:ring-2 focus:ring-black transition-all duration-200"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                    />
                    <InputError class="mt-2 text-sm" :message="form.errors.email" />
                </div>

                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <circle cx="12" cy="16" r="1"></circle>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </div>
                    <input
                        id="password"
                        type="password"
                        placeholder="Password"
                        class="w-full pl-10 pr-3 py-4 text-base text-black placeholder-gray-400 bg-gray-50 border-0 hover:bg-gray-100 focus:bg-white focus:outline-none focus:ring-2 focus:ring-black transition-all duration-200"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                    />
                    <InputError class="mt-2 text-sm" :message="form.errors.password" />
                </div>

                <div class="flex items-center pt-2">
                    <input
                        id="remember"
                        type="checkbox"
                        class="w-4 h-4 text-black bg-white border-gray-300 rounded focus:ring-black focus:ring-2"
                        v-model="form.remember"
                    />
                    <label for="remember" class="ml-3 text-sm text-gray-500">Remember me</label>
                </div>

                <button
                    type="submit"
                    class="w-full bg-black text-white py-4 px-6 text-base font-medium hover:bg-gray-800 focus:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed mt-6"
                    :disabled="form.processing"
                >
                    {{ form.processing ? 'Signing in...' : 'Sign in' }}
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-gray-100">
                <div class="text-center">
                    <Link
                        href="/agent/login"
                        class="text-sm text-gray-500 hover:text-black transition-colors"
                    >
                        Agent Login
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/admin/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>