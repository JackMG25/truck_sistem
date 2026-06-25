<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'sistema de carga') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-950 text-slate-100">
        <div class="relative min-h-screen overflow-hidden">
            <div class="pointer-events-none absolute -top-40 -left-32 h-96 w-96 rounded-full bg-cyan-500/30 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-48 -right-24 h-[30rem] w-[30rem] rounded-full bg-indigo-500/30 blur-3xl"></div>

            <div class="relative mx-auto flex min-h-screen w-full max-w-7xl items-center px-4 py-8 sm:px-6 lg:px-8">
                <div class="grid w-full overflow-hidden rounded-2xl border border-white/10 bg-slate-900/80 shadow-2xl backdrop-blur-sm lg:grid-cols-2">
                    <div class="hidden bg-gradient-to-br from-cyan-500/20 via-transparent to-indigo-500/20 p-10 lg:flex lg:flex-col lg:justify-between">
                        <div class="space-y-5">
                            <p class="inline-flex items-center rounded-full border border-cyan-400/30 bg-cyan-400/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-cyan-200">
                                Acceso Seguro
                            </p>
                            <h1 class="max-w-sm text-3xl font-semibold leading-tight text-white">
                                Gestiona tus cargas y operaciones desde un solo lugar.
                            </h1>
                            <p class="max-w-md text-sm text-slate-300">
                                Panel profesional para administrar clientes, agencias y servicios de forma rapida y ordenada.
                            </p>
                        </div>
                        <p class="text-xs text-slate-400">
                            {{ config('app.name', 'Camionero Carga') }} &copy; {{ now()->year }}
                        </p>
                    </div>

                    <div class="w-full px-6 py-8 sm:px-10 sm:py-10 lg:px-12">
                        <div class="mb-8">
                            
                        </div>

                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
