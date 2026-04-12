<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    workshops: Array,
});

const page = usePage();
const flash = computed(() => page.props.flash ?? {});

function formatDate(iso) {
    return new Date(iso).toLocaleString('en-GB', {
        weekday: 'short', day: '2-digit', month: 'short', year: 'numeric',
        hour: '2-digit', minute: '2-digit',
    });
}

function deleteWorkshop(workshop) {
    if (!confirm(`Delete "${workshop.title}"? This will also remove all registrations.`)) return;
    router.delete(route('admin.workshops.destroy', workshop.id));
}
</script>

<template>
    <Head title="Manage Workshops" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Manage Workshops</h2>
                <Link
                    :href="route('admin.workshops.create')"
                    class="inline-flex items-center rounded-lg bg-rose-600 px-4 py-2 text-sm font-medium text-white hover:bg-rose-700 transition"
                >
                    + New Workshop
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                <div v-if="flash.success" class="mb-4 rounded-md bg-green-50 p-4 text-green-800 border border-green-200">
                    {{ flash.success }}
                </div>

                <div v-if="workshops.length === 0" class="text-center text-gray-500 py-16 bg-white rounded-xl border border-gray-200">
                    No workshops yet. <Link :href="route('admin.workshops.create')" class="text-rose-600 underline">Create the first one.</Link>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200" v-if="workshops.length > 0">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Workshop</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registrations</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="workshop in workshops" :key="workshop.id">
                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-900">{{ workshop.title }}</p>
                                    <p class="text-sm text-gray-500 line-clamp-1">{{ workshop.description }}</p>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">
                                    {{ formatDate(workshop.start_time) }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-medium text-gray-800">{{ workshop.confirmed_count }}/{{ workshop.capacity }}</span>
                                    <span v-if="workshop.waiting_count" class="ml-2 text-xs text-amber-600">+{{ workshop.waiting_count }} waiting</span>
                                    <div class="w-24 bg-gray-100 rounded-full h-1.5 mt-1">
                                        <div
                                            class="h-1.5 rounded-full"
                                            :class="workshop.available_seats === 0 ? 'bg-red-500' : 'bg-rose-500'"
                                            :style="{ width: Math.min(100, Math.round(workshop.confirmed_count / workshop.capacity * 100)) + '%' }"
                                        ></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap space-x-2">
                                    <Link
                                        :href="route('admin.workshops.edit', workshop.id)"
                                        class="text-sm text-rose-600 hover:text-rose-800 font-medium"
                                    >Edit</Link>
                                    <button
                                        @click="deleteWorkshop(workshop)"
                                        class="text-sm text-red-500 hover:text-red-700 font-medium"
                                    >Delete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
