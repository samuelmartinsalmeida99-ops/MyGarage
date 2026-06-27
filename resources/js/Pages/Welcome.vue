<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});
</script>

<template>

    <Head title="Gestão de Garagem" />

    <div
        class="bg-slate-50 text-slate-800 min-h-screen dark:bg-zinc-950 dark:text-zinc-200 transition-colors duration-200">
        <div class="relative flex min-h-screen flex-col items-center selection:bg-blue-600 selection:text-white">
            <div class="relative w-full max-w-7xl px-6 lg:max-w-7xl flex-1 flex flex-col">

                <header class="flex items-center justify-between py-6 border-b border-slate-200 dark:border-zinc-800">
                    <div class="flex items-center gap-3">
                        <div class="flex size-10 items-center justify-center rounded-lg bg-blue-600 text-white">
                            <svg class="size-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M11.42 15.17 17.25 21A2.652 2.652 0 1 0 21 17.25l-5.83-5.83m0 0a2.999 2.999 0 0 0-4.14-4.14m4.14 4.14L11.129 12.3a2.999 2.999 0 1 1-3.43-3.43l1.314-1.313m0 0a2.999 2.999 0 0 0-4.14-4.14l5.83 5.83M21 3h-3.75a.75.75 0 0 0-.75.75v3.197m0 0H13.5a.75.75 0 0 0-.75.75V11.25m3.75-3.303 3.51 3.51a.75.75 0 0 1 0 1.06l-1.314 1.314" />
                            </svg>
                        </div>
                        <span
                            class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">GarageControl</span>
                    </div>

                    <nav v-if="canLogin" class="flex gap-4">
                        <Link v-if="$page.props.auth.user" :href="route('dashboard')"
                            class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-500 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600">
                            Ir para o Painel
                        </Link>

                        <template v-else>
                            <Link :href="route('login')"
                                class="rounded-md px-4 py-2 text-sm font-medium text-slate-700 hover:text-slate-900 dark:text-zinc-300 dark:hover:text-white transition">
                                Entrar
                            </Link>

                            <Link v-if="canRegister" :href="route('register')"
                                class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800 dark:bg-zinc-800 dark:hover:bg-zinc-700 transition">
                                Registar
                            </Link>
                        </template>
                    </nav>
                </header>

                <main class="mt-12 flex-1">
                    <div class="text-center max-w-3xl mx-auto space-y-4 mb-12">
                        <h1 class="text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white sm:text-5xl">
                            O controlo total da sua garagem <span class="text-blue-600">sem
                                complicações.</span>
                        </h1>
                        <p class="text-lg text-slate-600 dark:text-zinc-400">
                            Centralize ordens de trabalho, acompanhe o histórico dos veículos, faça
                            agendamentos e controle a faturação numa única plataforma intuitiva.
                        </p>
                    </div>

                    <div class="flex justify-center mb-16">
                        <div
                            class="flex max-w-xl gap-4 rounded-xl bg-white p-6 shadow-sm border border-slate-200 dark:bg-zinc-900 dark:border-zinc-800 text-left">

                            <div
                                class="flex size-12 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-400">
                                <svg class="size-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z" />
                                </svg>
                            </div>

                            <div class="space-y-2">
                                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Frota & Veículos</h2>
                                <p class="text-sm leading-relaxed text-slate-600 dark:text-zinc-400">
                                    Registe matrículas, marcas, modelos e quilometragens. Aceda instantaneamente ao
                                    histórico completo de intervenções e peças aplicadas a cada veículo.
                                </p>
                            </div>

                        </div>
                    </div>
                </main>

                <footer
                    class="py-8 text-center text-xs text-slate-500 border-t border-slate-200 dark:border-zinc-800 dark:text-zinc-500">
                    Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})
                </footer>
            </div>
        </div>
    </div>
</template>