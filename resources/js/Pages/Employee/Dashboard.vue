<script setup>
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';

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

function register(workshop) {
    router.post(route('employee.workshops.register', workshop.id));
}

function unregister(workshop) {
    router.delete(route('employee.workshops.unregister', workshop.id));
}
</script>

<template>
    <Head title="Workshops" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Upcoming Workshops
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                <!-- Flash messages -->
                <div v-if="flash.success" class="mb-4 rounded-md bg-green-50 p-4 text-green-800 border border-green-200">
                    {{ flash.success }}
                </div>
                <div v-if="flash.error" class="mb-4 rounded-md bg-red-50 p-4 text-red-800 border border-red-200">
                    {{ flash.error }}
                </div>

                <div v-if="workshops.length === 0" class="text-center text-gray-500 py-16">
                    No upcoming workshops at the moment. Check back soon!
                </div>

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="workshop in workshops"
                        :key="workshop.id"
                        class="bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col overflow-hidden"
                    >
                        <div class="p-6 flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ workshop.title }}</h3>
                            <p class="text-sm text-gray-500 mb-3">{{ formatDate(workshop.start_time) }} – {{ formatDate(workshop.end_time) }}</p>
                            <p class="text-gray-700 text-sm leading-relaxed">{{ workshop.description }}</p>
                        </div>

                        <div class="px-6 pb-6">
                            <!-- Seats info -->
                            <div class="flex items-center justify-between text-sm mb-4">
                                <span class="text-gray-600">
                                    <span class="font-medium">{{ workshop.available_seats }}</span> / {{ workshop.capacity }} seats available
                                </span>
                                <span
                                    v-if="workshop.available_seats === 0"
                                    class="text-xs font-medium text-orange-600 bg-orange-50 px-2 py-0.5 rounded-full"
                                >
                                    Full
                                </span>
                                <span
                                    v-else
                                    class="text-xs font-medium text-green-600 bg-green-50 px-2 py-0.5 rounded-full"
                                >
                                    Open
                                </span>
                            </div>

                            <!-- Registration status -->
                            <div v-if="workshop.registration">
                                <div v-if="workshop.registration.status === 'confirmed'" class="mb-2 text-xs text-green-700 bg-green-50 px-3 py-1.5 rounded-md border border-green-200 flex items-center gap-1">
                                    ✓ You are registered
                                </div>
                                <div v-else class="mb-2 text-xs text-amber-700 bg-amber-50 px-3 py-1.5 rounded-md border border-amber-200">
                                    ⏳ Waiting list — position #{{ workshop.registration.waiting_position }}
                                </div>
                                <button
                                    @click="unregister(workshop)"
                                    class="w-full rounded-lg bg-red-50 border border-red-200 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-100 transition"
                                >
                                    Cancel Registration
                                </button>
                            </div>

                            <div v-else>
                                <button
                                    @click="register(workshop)"
                                    class="w-full rounded-lg px-4 py-2 text-sm font-medium transition"
                                    :class="workshop.available_seats > 0
                                        ? 'bg-indigo-600 text-white hover:bg-indigo-700'
                                        : 'bg-amber-500 text-white hover:bg-amber-600'"
                                >
                                    {{ workshop.available_seats > 0 ? 'Register' : 'Join Waiting List' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
