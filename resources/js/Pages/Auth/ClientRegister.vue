<!-- ClientRegister.vue -->
<template>
    <Head title="Client Registration" />

    <div class="min-h-screen bg-white flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-sm">
            <div class="text-center mb-12">
                <Link href="/" class="inline-block">
                    <img 
                        src="/images/logo.png" 
                        alt="Logo" 
                        class="h-12 w-auto mx-auto mb-6"
                    />
                    <h1 class="text-2xl font-medium text-black tracking-tight">Create Account</h1>
                </Link>
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <input
                        id="name"
                        type="text"
                        placeholder="Full name"
                        class="w-full pl-10 pr-3 py-4 text-base text-black placeholder-gray-400 bg-gray-50 border-0 hover:bg-gray-100 focus:bg-white focus:outline-none focus:ring-2 focus:ring-black transition-all duration-200"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                    />
                    <InputError class="mt-2 text-sm" :message="form.errors.name" />
                </div>

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
                        autocomplete="new-password"
                    />
                    <InputError class="mt-2 text-sm" :message="form.errors.password" />
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
                        id="password_confirmation"
                        type="password"
                        placeholder="Confirm password"
                        class="w-full pl-10 pr-3 py-4 text-base text-black placeholder-gray-400 bg-gray-50 border-0 hover:bg-gray-100 focus:bg-white focus:outline-none focus:ring-2 focus:ring-black transition-all duration-200"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                    />
                    <InputError class="mt-2 text-sm" :message="form.errors.password_confirmation" />
                </div>

                <!-- Optional fields section -->
                <div class="pt-4">
                    <p class="text-xs text-gray-400 mb-4 uppercase tracking-wide">Optional</p>
                    
                    <div class="space-y-4">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                    <polyline points="9,22 9,12 15,12 15,22"></polyline>
                                </svg>
                            </div>
                            <input
                                id="company"
                                type="text"
                                placeholder="Company"
                                class="w-full pl-10 pr-3 py-4 text-base text-black placeholder-gray-400 bg-gray-50 border-0 hover:bg-gray-100 focus:bg-white focus:outline-none focus:ring-2 focus:ring-black transition-all duration-200"
                                v-model="form.company"
                                autocomplete="organization"
                            />
                            <InputError class="mt-2 text-sm" :message="form.errors.company" />
                        </div>

                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                            </div>
                            <input
                                id="phone"
                                type="text"
                                placeholder="Phone"
                                class="w-full pl-10 pr-3 py-4 text-base text-black placeholder-gray-400 bg-gray-50 border-0 hover:bg-gray-100 focus:bg-white focus:outline-none focus:ring-2 focus:ring-black transition-all duration-200"
                                v-model="form.phone"
                                autocomplete="tel"
                            />
                            <InputError class="mt-2 text-sm" :message="form.errors.phone" />
                        </div>

                        <div class="relative">
                            <div class="absolute top-4 left-0 pl-3 flex items-start pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                            </div>
                            <textarea
                                id="address"
                                placeholder="Address"
                                class="w-full pl-10 pr-3 py-4 text-base text-black placeholder-gray-400 bg-gray-50 border-0 hover:bg-gray-100 focus:bg-white focus:outline-none focus:ring-2 focus:ring-black transition-all duration-200 resize-none"
                                v-model="form.address"
                                rows="2"
                                autocomplete="street-address"
                            ></textarea>
                            <InputError class="mt-2 text-sm" :message="form.errors.address" />
                        </div>
                    </div>
                </div>

                <button
                    type="submit"
                    class="w-full bg-black text-white py-4 px-6 text-base font-medium hover:bg-gray-800 focus:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed mt-6"
                    :disabled="form.processing"
                >
                    {{ form.processing ? 'Creating account...' : 'Create account' }}
                </button>
            </form>

            <div class="mt-8 text-center">
                <Link
                    href="/client/login"
                    class="text-sm text-gray-500 hover:text-black transition-colors"
                >
                    Already have an account? Sign in
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    company: '',
    phone: '',
    address: '',
});

const submit = () => {
    form.post('/client/register', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>