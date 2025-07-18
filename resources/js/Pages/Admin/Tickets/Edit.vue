<template>
    <Head :title="`Edit Ticket #${ticket.ticket_number}`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Ticket #{{ ticket.ticket_number }}
            </h2>
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
                            />
                            <InputError class="mt-2" :message="form.errors.subject" />
                        </div>

                        <div>
                            <InputLabel for="description" value="Description" />
                            <textarea
                                id="description"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                v-model="form.description"
                                rows="5"
                                required
                            ></textarea>
                            <InputError class="mt-2" :message="form.errors.description" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="status" value="Status" />
                                <select
                                    id="status"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    v-model="form.status"
                                    required
                                >
                                    <option value="Open">Open</option>
                                    <option value="In Progress">In Progress</option>
                                    <option value="Resolved">Resolved</option>
                                    <option value="Closed">Closed</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.status" />
                            </div>

                            <div>
                                <InputLabel for="priority" value="Priority" />
                                <select
                                    id="priority"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    v-model="form.priority"
                                    required
                                >
                                    <option value="Low">Low</option>
                                    <option value="Medium">Medium</option>
                                    <option value="High">High</option>
                                    <option value="Critical">Critical</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.priority" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="category_id" value="Category" />
                                <select
                                    id="category_id"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    v-model="form.category_id"
                                    required
                                >
                                    <option value="">Select Category</option>
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
                                <InputLabel for="agent_id" value="Assign to Agent" />
                                <select
                                    id="agent_id"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    v-model="form.agent_id"
                                >
                                    <option value="">Unassigned</option>
                                    <option
                                        v-for="agent in agents"
                                        :key="agent.id"
                                        :value="agent.id"
                                    >
                                        {{ agent.user.name }} {{ agent.is_available ? '' : '(Unavailable)' }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.agent_id" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <Link
                                :href="route('admin.tickets.show', ticket.id)"
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                            >
                                Cancel
                            </Link>
                            <PrimaryButton
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                            >
                                Update Ticket
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
    ticket: Object,
    categories: Array,
    agents: Array,
});

const form = useForm({
    subject: props.ticket.subject,
    description: props.ticket.description,
    status: props.ticket.status,
    priority: props.ticket.priority,
    category_id: props.ticket.category_id,
    agent_id: props.ticket.agent_id || '',
});

const submit = () => {
    form.put(route('admin.tickets.update', props.ticket.id));
};
</script>