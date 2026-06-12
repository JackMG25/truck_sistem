@props([
    'title',
    'subtitle' => null,
])

<header class="sticky top-0 z-20 border-b border-sky-100 bg-white/90 px-4 pb-3 pt-4 backdrop-blur-xl sm:px-5 sm:pb-4 sm:pt-5">
    <div class="flex items-start justify-between gap-3 sm:gap-4">
        <div>
            <p class="text-[10px] font-semibold uppercase tracking-[0.28em] text-sky-600/70 sm:text-xs sm:tracking-[0.35em]">Transportista</p>
            <h1 class="mt-1.5 text-xl font-extrabold tracking-tight text-slate-800 sm:mt-2 sm:text-2xl lg:text-[1.75rem]">{{ $title }}</h1>

            @if ($subtitle)
                <p class="mt-1 hidden text-sm text-slate-500 sm:block">{{ $subtitle }}</p>
            @endif
        </div>

        <div class="flex h-10 w-10 items-center justify-center rounded-xl border border-sky-100 bg-sky-50 text-sky-600 shadow-md shadow-sky-100/70 sm:h-12 sm:w-12 sm:rounded-2xl sm:shadow-lg sm:shadow-sky-100/80">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5 sm:h-6 sm:w-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5V8.25A2.25 2.25 0 0 1 5.25 6h9.879a2.25 2.25 0 0 1 1.59.659l3.622 3.621A2.25 2.25 0 0 1 21 11.871V16.5M3 16.5A2.25 2.25 0 0 0 5.25 18.75h.75A2.25 2.25 0 0 0 8.25 16.5m-5.25 0A2.25 2.25 0 0 1 5.25 14.25H6a2.25 2.25 0 0 1 2.25 2.25m0 0h7.5m0 0A2.25 2.25 0 0 1 18 14.25h.75A2.25 2.25 0 0 1 21 16.5m-5.25 0A2.25 2.25 0 0 0 18 18.75h.75A2.25 2.25 0 0 0 21 16.5" />
            </svg>
        </div>
    </div>
</header>