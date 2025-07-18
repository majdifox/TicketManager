<template>
    <Head title="Create Category" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Category</h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <div>
                            <InputLabel for="name" value="Category Name" />
                            <TextInput
                                id="name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.name"
                                required
                                autofocus
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <div>
                            <InputLabel for="description" value="Description" />
                            <textarea
                                id="description"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                v-model="form.description"
                                rows="3"
                                placeholder="Brief description of this category..."
                            ></textarea>
                            <InputError class="mt-2" :message="form.errors.description" />
                        </div>

                        <div>
                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    v-model="form.is_active"
                                />
                                <span class="ml-2 text-sm text-gray-600">Active</span>
                            </label>
                            <p class="mt-1 text-sm text-gray-500">
                                Inactive categories won't be available for new tickets.
                            </p>
                        </div>

                        <div>
                            <InputLabel value="Assign Agents" />
                            <p class="text-sm text-gray-600 mb-3">
                                Select agents who can handle tickets in this category.
                            </p>
                            <div class="space-y-2 max-h-64 overflow-y-auto border border-gray-200 rounded-md p-3">
                                <label
                                    v-for="agent in agents"
                                    :key="agent.id"
                                    class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer"
                                >
                                    <input
                                        type="checkbox"
                                        :value="agent.id"
                                        v-model="form.agents"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    />
                                    <div class="ml-3">
                                        <span class="text-sm font-medium text-gray-900">
                                            {{ agent.user.name }}
                                        </span>
                                        <span v-if="agent.department" class="text-sm text-gray-500 ml-2">
                                            ({{ agent.department }})
                                        </span>
                                    </div>
                                </label>
                            </div>
                            <InputError class="mt-2" :message="form.errors.agents" />
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <Link
                                :href="route('admin.categories.index')"
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                            >
                                Cancel
                            </Link>
                            <PrimaryButton
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                            >
                                Create Category
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    agents: Array,
});

const form = useForm({
    name: '',
    description: '',
    is_active: true,
    agents: [],
});

const submit = () => {
    form.post(route('admin.categories.store'));
};
</script>