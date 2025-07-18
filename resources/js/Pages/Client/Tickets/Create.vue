<template>
    <Head title="Create Ticket" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create New Ticket</h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <div>
                            <InputLabel for="subject" value="Subject" />
                            <TextInput
                                id="subject"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.subject"
                                required
                                autofocus
                                placeholder="Brief description of your issue"
                            />
                            <InputError class="mt-2" :message="form.errors.subject" />
                        </div>

                        <div>
                            <InputLabel for="category_id" value="Category" />
                            <select
                                id="category_id"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                v-model="form.category_id"
                                required
                            >
                                <option value="">Select a category</option>
                                <option
                                    v-for="category in categories"
                                    :key="category.id"
                                    :value="category.id"
                                >
                                    {{ category.name }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.category_id" />
                        </div>

                        <div>
                            <InputLabel for="priority" value="Priority" />
                            <select
                                id="priority"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                v-model="form.priority"
                                required
                            >
                                <option value="">Select priority</option>
                                <option
                                    v-for="priority in priorities"
                                    :key="priority"
                                    :value="priority"
                                >
                                    {{ priority }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.priority" />
                            <p class="mt-1 text-sm text-gray-600">
                                <span class="font-semibold">Low:</span> General inquiries |
                                <span class="font-semibold">Medium:</span> Standard issues |
                                <span class="font-semibold">High:</span> Urgent matters |
                                <span class="font-semibold">Critical:</span> Service outages
                            </p>
                        </div>

                        <div>
                            <InputLabel for="description" value="Description" />
                            <textarea
                                id="description"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                v-model="form.description"
                                rows="6"
                                required
                                placeholder="Please provide detailed information about your issue..."
                            ></textarea>
                            <InputError class="mt-2" :message="form.errors.description" />
                        </div>

                        <div>
                            <InputLabel for="attachment" value="Attachment (Optional)" />
                            <input
                                id="attachment"
                                type="file"
                                class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none"
                                @change="handleFileChange"
                                accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx"
                            />
                            <InputError class="mt-2" :message="form.errors.attachment" />
                            <p class="mt-1 text-sm text-gray-600">
                                Supported formats: PDF, JPG, PNG, DOC, DOCX, XLS, XLSX (Max: 10MB)
                            </p>
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <Link
                                :href="route('client.tickets.index')"
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                            >
                                Cancel
                            </Link>
                            <PrimaryButton
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                            >
                                Create Ticket
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
    categories: Array,
    priorities: Array,
});

const form = useForm({
    subject: '',
    category_id: '',
    priority: 'Medium',
    description: '',
    attachment: null,
});

const handleFileChange = (e) => {
    form.attachment = e.target.files[0];
};

const submit = () => {
    form.post(route('client.tickets.store'), {
        forceFormData: true,
    });
};
</script>