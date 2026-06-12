<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ ($title ?? null) ? $title.' | '.config('app.name', 'Camionero Carga') : config('app.name', 'Camionero Carga') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden bg-slate-50 font-sans text-slate-800 antialiased">
    <div class="relative min-h-screen overflow-x-hidden bg-[radial-gradient(circle_at_top,_rgba(14,165,233,0.14),_transparent_32%),linear-gradient(180deg,_#ffffff_0%,_#f8fafc_58%,_#eef6ff_100%)]">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-48 bg-gradient-to-b from-sky-100/70 to-transparent"></div>
        <div class="pointer-events-none absolute -top-12 right-[-4rem] h-36 w-36 rounded-full bg-amber-100/60 blur-3xl"></div>

        <div class="mx-auto flex min-h-screen w-full max-w-5xl flex-col bg-white/85 backdrop-blur-xl sm:border-x sm:border-sky-100 sm:shadow-2xl sm:shadow-sky-100/80 xl:rounded-[2rem]">
            @isset($header)
                {{ $header }}
            @else
                <x-header :title="$title ?? config('app.name', 'Camionero Carga')" />
            @endisset

            <main class="flex-1 px-4 pb-24 pt-4 sm:px-6 lg:px-8">
                @if (session('success'))
                    <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 shadow-lg shadow-emerald-100/70">
                        {{ session('success') }}
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>

        <x-bottom-navigation />
    </div>
</body>
</html>
