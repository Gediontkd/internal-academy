<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    stats: Object,
});

const stats = ref(props.stats);
let pollInterval = null;

async function fetchStats() {
    try {
        const response = await fetch(route('admin.stats'));
        const data = await response.json();
        stats.value = data;
    } catch (e) {
        // silent fail
    }
}

onMounted(() => {
    // Poll every 5 seconds for real-time updates
    pollInterval = setInterval(fetchStats, 5000);
});

onUnmounted(() => {
    clearInterval(pollInterval);
});

function formatDate(iso) {
    return new Date(iso).toLocaleString('en-GB', {
        day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit',
    });
}

function fillPercent(workshop) {
    if (!workshop.capacity) return 0;
    return Math.min(100, Math.round((workshop.confirmed_count / workshop.capacity) * 100));
}
</script>

<template>
    <Head title="Admin Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Statistics Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-8">

                <!-- Summary Cards -->
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <p class="text-sm text-gray-500 mb-1">Total Workshops</p>
                        <p class="text-3xl font-bold text-gray-900">{{ stats.total_workshops }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <p class="text-sm text-gray-500 mb-1">Confirmed Registrations</p>
                        <p class="text-3xl font-bold text-indigo-600">{{ stats.total_registrations }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <p class="text-sm text-gray-500 mb-1">On Waiting Lists</p>
                        <p class="text-3xl font-bold text-amber-600">{{ stats.total_waiting }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <p class="text-sm text-gray-500 mb-1">Most Popular</p>
                        <p class="text-sm font-semibold text-gray-900 truncate" :title="stats.most_popular?.title">
                            {{ stats.most_popular?.title ?? '—' }}
                        </p>
                        <p class="text-xs text-gray-400" v-if="stats.most_popular">
                            {{ stats.most_popular.confirmed_count }} registrations
                        </p>
                    </div>
                </div>

                <!-- Workshop fill rates -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="font-semibold text-gray-800">Workshop Fill Rates</h3>
                        <span class="text-xs text-gray-400">Auto-refreshes every 5s</span>
                    </div>
                    <div class="divide-y divide-gray-100">
                        <div
                            v-for="workshop in stats.workshops"
                            :key="workshop.id"
                            class="px-6 py-4"
                        >
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-gray-800">{{ workshop.title }}</span>
                                <span class="text-xs text-gray-500">
                                    {{ workshop.confirmed_count }}/{{ workshop.capacity }}
                                    <span v-if="workshop.waiting_count" class="text-amber-600 ml-1">(+{{ workshop.waiting_count }} waiting)</span>
                                </span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div
                                    class="h-2 rounded-full transition-all duration-500"
                                    :class="fillPercent(workshop) >= 100 ? 'bg-red-500' : fillPercent(workshop) >= 75 ? 'bg-amber-500' : 'bg-indigo-500'"
                                    :style="{ width: fillPercent(workshop) + '%' }"
                                ></div>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">{{ formatDate(workshop.start_time) }}</p>
                        </div>
                        <div v-if="stats.workshops.length === 0" class="px-6 py-8 text-center text-gray-400 text-sm">
                            No workshops yet.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
