<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    workshop: Object, // null for create
});

const isEditing = computed(() => !!props.workshop);

const form = useForm({
    title: props.workshop?.title ?? '',
    description: props.workshop?.description ?? '',
    start_time: props.workshop?.start_time_input ?? '',
    end_time: props.workshop?.end_time_input ?? '',
    capacity: props.workshop?.capacity ?? 10,
});

function submit() {
    if (isEditing.value) {
        form.put(route('admin.workshops.update', props.workshop.id));
    } else {
        form.post(route('admin.workshops.store'));
    }
}
</script>

<template>
    <Head :title="isEditing ? 'Edit Workshop' : 'New Workshop'" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link :href="route('admin.workshops.index')" class="text-gray-400 hover:text-gray-600">
                    ← Workshops
                </Link>
                <span class="text-gray-400">/</span>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ isEditing ? 'Edit Workshop' : 'New Workshop' }}
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                    <form @submit.prevent="submit" class="space-y-6">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                            <input
                                v-model="form.title"
                                type="text"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm"
                                placeholder="e.g. Introduction to Domain-Driven Design"
                            />
                            <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">{{ form.errors.title }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea
                                v-model="form.description"
                                rows="4"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm"
                                placeholder="What will participants learn?"
                            ></textarea>
                            <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                                <input
                                    v-model="form.start_time"
                                    type="datetime-local"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm"
                                />
                                <p v-if="form.errors.start_time" class="mt-1 text-sm text-red-600">{{ form.errors.start_time }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                                <input
                                    v-model="form.end_time"
                                    type="datetime-local"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm"
                                />
                                <p v-if="form.errors.end_time" class="mt-1 text-sm text-red-600">{{ form.errors.end_time }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Max Seats (Capacity)</label>
                            <input
                                v-model.number="form.capacity"
                                type="number"
                                min="1"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 text-sm"
                            />
                            <p v-if="form.errors.capacity" class="mt-1 text-sm text-red-600">{{ form.errors.capacity }}</p>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-2">
                            <Link
                                :href="route('admin.workshops.index')"
                                class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="rounded-lg bg-rose-600 px-6 py-2 text-sm font-medium text-white hover:bg-rose-700 disabled:opacity-50 transition"
                            >
                                {{ isEditing ? 'Update Workshop' : 'Create Workshop' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
