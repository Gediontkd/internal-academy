<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex">

        <!-- Left branded panel — hidden on mobile -->
        <div class="hidden lg:flex lg:w-1/2 flex-col justify-between bg-gradient-to-br from-rose-600 via-rose-700 to-rose-800 p-12 relative overflow-hidden">
            <!-- Background circles -->
            <div class="absolute -top-32 -right-32 h-80 w-80 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-24 -left-16 h-64 w-64 rounded-full bg-white/10"></div>

            <!-- Logo -->
            <Link href="/" class="relative flex items-center gap-3 w-fit">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 ring-1 ring-white/30">
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <span class="text-lg font-semibold text-white tracking-tight">Internal Academy</span>
            </Link>

            <!-- Center content -->
            <div class="relative">
                <h2 class="text-3xl font-bold text-white leading-snug">
                    Grow together.<br>
                    <span class="text-rose-200">Learn at work.</span>
                </h2>
                <p class="mt-4 text-rose-200 leading-relaxed max-w-sm">
                    Manage, discover, and attend company workshops — all in one place designed for your team.
                </p>

                <!-- Feature list -->
                <ul class="mt-8 space-y-4">
                    <li class="flex items-center gap-3 text-sm text-rose-100">
                        <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/20">
                            <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        One-click workshop registration
                    </li>
                    <li class="flex items-center gap-3 text-sm text-rose-100">
                        <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/20">
                            <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        Automatic waiting list promotion
                    </li>
                    <li class="flex items-center gap-3 text-sm text-rose-100">
                        <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/20">
                            <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        Real-time admin statistics
                    </li>
                </ul>
            </div>

            <!-- Bottom quote -->
            <div class="relative">
                <p class="text-xs text-rose-300 italic">
                    "An investment in knowledge pays the best interest."
                </p>
                <p class="text-xs text-rose-400 mt-1">— Benjamin Franklin</p>
            </div>
        </div>

        <!-- Right form panel -->
        <div class="flex w-full lg:w-1/2 flex-col items-center justify-center bg-gray-50 px-6 py-12">

            <!-- Mobile logo -->
            <Link href="/" class="lg:hidden flex items-center gap-2 mb-8">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-rose-600">
                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <span class="text-base font-semibold text-gray-900">Internal Academy</span>
            </Link>

            <!-- Form card -->
            <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-sm border border-gray-100">
                <slot />
            </div>
        </div>

    </div>
</template>
