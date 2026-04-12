<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: { type: Boolean },
    canRegister: { type: Boolean },
});

const features = [
    {
        icon: 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z',
        title: 'Browse Workshops',
        description: 'Employees see all upcoming sessions in one place and register with a single click.',
    },
    {
        icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
        title: 'Admin CRUD',
        description: 'HR and managers create, edit, and delete workshops with title, schedule, and capacity.',
    },
    {
        icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
        title: 'Waiting List',
        description: 'Full workshop? Join the FIFO queue. You are promoted automatically when a seat opens up.',
    },
    {
        icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        title: 'Live Statistics',
        description: 'Admin dashboard refreshes every 5 seconds — registrations, fill rates, and most popular sessions.',
    },
];
</script>

<template>
    <Head title="Internal Academy" />

    <div class="min-h-screen bg-white">

        <!-- Nav -->
        <nav class="border-b border-gray-100 bg-white/80 backdrop-blur-sm sticky top-0 z-50">
            <div class="mx-auto max-w-6xl px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <!-- Book icon -->
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-rose-600">
                        <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <span class="text-base font-semibold text-gray-900 tracking-tight">Internal Academy</span>
                </div>

                <div v-if="canLogin" class="flex items-center gap-3">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-medium text-white hover:bg-rose-700 transition"
                    >
                        Go to Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="text-sm font-medium text-gray-600 hover:text-gray-900 transition"
                        >
                            Log in
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-medium text-white hover:bg-rose-700 transition"
                        >
                            Get started
                        </Link>
                    </template>
                </div>
            </div>
        </nav>

        <!-- Hero -->
        <section class="relative overflow-hidden bg-gradient-to-br from-rose-600 via-rose-700 to-rose-800">
            <!-- Background decoration -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute -top-40 -right-32 h-96 w-96 rounded-full bg-white"></div>
                <div class="absolute -bottom-32 -left-20 h-72 w-72 rounded-full bg-white"></div>
            </div>

            <div class="relative mx-auto max-w-6xl px-6 py-28 text-center">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/20 px-3 py-1 text-xs font-medium text-white ring-1 ring-white/30 mb-8">
                    <span class="h-1.5 w-1.5 rounded-full bg-green-400 animate-pulse"></span>
                    Company-wide learning platform
                </span>

                <h1 class="text-4xl font-bold tracking-tight text-white sm:text-5xl lg:text-6xl">
                    Grow together.<br>
                    <span class="text-rose-200">Learn at work.</span>
                </h1>

                <p class="mx-auto mt-6 max-w-xl text-lg text-rose-100 leading-relaxed">
                    Internal Academy brings your team's workshops, training sessions, and knowledge-sharing events into one clean, easy-to-use platform.
                </p>

                <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                    <Link
                        v-if="canLogin && !$page.props.auth.user"
                        :href="route('login')"
                        class="w-full sm:w-auto rounded-xl bg-white px-8 py-3.5 text-sm font-semibold text-rose-700 shadow-lg hover:bg-rose-50 transition"
                    >
                        Sign in to your account
                    </Link>
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="w-full sm:w-auto rounded-xl bg-white px-8 py-3.5 text-sm font-semibold text-rose-700 shadow-lg hover:bg-rose-50 transition"
                    >
                        Go to Dashboard
                    </Link>
                    <a href="#features" class="text-sm font-medium text-rose-100 hover:text-white transition flex items-center gap-1">
                        See what's included
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </a>
                </div>

                <!-- Stats bar -->
                <div class="mt-16 grid grid-cols-3 gap-6 max-w-sm mx-auto sm:max-w-md">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-white">2</div>
                        <div class="text-xs text-rose-200 mt-0.5">Roles</div>
                    </div>
                    <div class="text-center border-x border-white/20">
                        <div class="text-2xl font-bold text-white">∞</div>
                        <div class="text-xs text-rose-200 mt-0.5">Workshops</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-white">Live</div>
                        <div class="text-xs text-rose-200 mt-0.5">Stats</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features -->
        <section id="features" class="py-24 bg-gray-50">
            <div class="mx-auto max-w-6xl px-6">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold text-gray-900">Everything your team needs</h2>
                    <p class="mt-4 text-gray-500 max-w-xl mx-auto">From booking a seat to managing a full training calendar — it's all here.</p>
                </div>

                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                    <div
                        v-for="feature in features"
                        :key="feature.title"
                        class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow"
                    >
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-rose-50 mb-5">
                            <svg class="h-5 w-5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" :d="feature.icon" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">{{ feature.title }}</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">{{ feature.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Role section -->
        <section class="py-24 bg-white">
            <div class="mx-auto max-w-6xl px-6">
                <div class="grid gap-12 lg:grid-cols-2 lg:gap-16 items-center">
                    <!-- Admin side -->
                    <div class="rounded-2xl bg-gradient-to-br from-rose-50 to-rose-50 p-8 border border-rose-100">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-rose-600">
                                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-rose-600 bg-rose-100 px-2.5 py-1 rounded-full">Admin</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Full control for HR & managers</h3>
                        <ul class="space-y-3 text-sm text-gray-600">
                            <li class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                Create, edit, and delete workshops
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                Set capacity and schedule
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                Live statistics dashboard with fill rates
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                Automatic reminder emails via artisan
                            </li>
                        </ul>
                    </div>

                    <!-- Employee side -->
                    <div class="rounded-2xl bg-gradient-to-br from-emerald-50 to-teal-50 p-8 border border-emerald-100">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-600">
                                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-emerald-700 bg-emerald-100 px-2.5 py-1 rounded-full">Employee</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Seamless booking for everyone</h3>
                        <ul class="space-y-3 text-sm text-gray-600">
                            <li class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                Browse all upcoming workshops
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                One-click registration and cancellation
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                Auto-join waiting list when full
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                Overlap prevention — no double-booking
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-20 bg-rose-600">
            <div class="mx-auto max-w-2xl px-6 text-center">
                <h2 class="text-2xl font-bold text-white sm:text-3xl">Ready to get started?</h2>
                <p class="mt-4 text-rose-200">Log in with your company credentials and explore your upcoming workshops.</p>
                <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
                    <Link
                        v-if="canLogin && !$page.props.auth.user"
                        :href="route('login')"
                        class="rounded-xl bg-white px-8 py-3 text-sm font-semibold text-rose-700 hover:bg-rose-50 transition"
                    >
                        Log in
                    </Link>
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="rounded-xl bg-white px-8 py-3 text-sm font-semibold text-rose-700 hover:bg-rose-50 transition"
                    >
                        Open Dashboard
                    </Link>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 py-8">
            <div class="mx-auto max-w-6xl px-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <div class="flex h-6 w-6 items-center justify-center rounded bg-rose-600">
                        <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-400">Internal Academy</span>
                </div>
                <p class="text-xs text-gray-600">Built with Laravel · Vue · Inertia.js</p>
            </div>
        </footer>
    </div>
</template>
