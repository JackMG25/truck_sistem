@php
    $items = [
        [
            'label' => 'Dashboard',
            'url' => url('/dashboard'),
            'active' => request()->is('dashboard'),
            'icon' => 'M3.75 12a8.25 8.25 0 1 1 16.5 0a8.25 8.25 0 0 1-16.5 0ZM10.5 8.25h3a.75.75 0 0 1 .75.75v6.75m-3.75 0h4.5',
        ],
        [
            'label' => 'Clientes',
            'url' => url('/clientes'),
            'active' => request()->is('clientes*'),
            'icon' => 'M15 19.128a9.38 9.38 0 0 0 2.625.372A3.375 3.375 0 0 0 21 16.125V15a2.25 2.25 0 0 0-2.25-2.25h-1.5A2.25 2.25 0 0 1 15 10.5V9a2.25 2.25 0 0 1 2.25-2.25h1.372M15 19.128v-.003a24.271 24.271 0 0 0-3.825-.568 24.294 24.294 0 0 0-3.825.568V19.5m7.65-.372A9.337 9.337 0 0 1 12 20.25a9.337 9.337 0 0 1-3-.494m6-13.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm-9 13.5A3.375 3.375 0 0 1 3 16.125V15a2.25 2.25 0 0 1 2.25-2.25h1.5A2.25 2.25 0 0 0 9 10.5V9A2.25 2.25 0 0 0 6.75 6.75H5.378',
        ],
        [
            'label' => 'Agencias',
            'url' => url('/agencias'),
            'active' => request()->is('agencias'),
            'icon' => 'M3.75 21h16.5M4.5 3h15A1.5 1.5 0 0 1 21 4.5v12.75a.75.75 0 0 1-.75.75h-16.5a.75.75 0 0 1-.75-.75V4.5A1.5 1.5 0 0 1 4.5 3Zm3 4.5h9m-9 3h9m-9 3h5.25',
        ],
        [
            'label' => 'Servicios',
            'url' => url('/servicios'),
            'active' => request()->is('servicios'),
            'icon' => 'M8.25 18.75a1.5 1.5 0 0 1-1.5-1.5V6.108c0-.815.66-1.476 1.476-1.476h5.463a1.5 1.5 0 0 1 1.06.44l2.178 2.178a1.5 1.5 0 0 1 .44 1.06v8.94a1.5 1.5 0 0 1-1.5 1.5h-7.617ZM9.75 9h4.5M9.75 12.75h4.5m-4.5 3.75h2.25',
        ],
        [
            'label' => 'Pagos',
            'url' => url('/pagos'),
            'active' => request()->is('pagos'),
            'icon' => 'M2.25 8.25h19.5m-18 0h16.5A1.5 1.5 0 0 1 21.75 9.75v8.25a1.5 1.5 0 0 1-1.5 1.5H3.75a1.5 1.5 0 0 1-1.5-1.5V9.75a1.5 1.5 0 0 1 1.5-1.5Zm3 6h3',
        ],
    ];
@endphp

<nav class="fixed inset-x-0 bottom-0 z-30 border-t border-sky-100 bg-white/90 pb-2 backdrop-blur-2xl sm:pb-3">
    <div class="mx-auto w-full max-w-5xl px-2 sm:px-4 lg:px-6">
        <div class="grid grid-cols-5 gap-1 rounded-2xl border border-sky-100 bg-slate-50/90 p-1 shadow-lg shadow-sky-100/60 sm:gap-2 sm:rounded-[1.75rem] sm:p-2">
        @foreach ($items as $item)
            <a
                href="{{ $item['url'] }}"
                class="flex flex-col items-center justify-center gap-0.5 rounded-xl px-1 py-2 text-[10px] font-medium transition sm:gap-1 sm:rounded-2xl sm:px-2 sm:py-3 sm:text-[11px] {{ $item['active'] ? 'bg-sky-600 text-white shadow-md shadow-sky-200' : 'text-slate-500 hover:bg-white hover:text-slate-800' }}"
                aria-label="{{ $item['label'] }}"
                @if ($item['active']) aria-current="page" @endif
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                </svg>
                <span class="hidden sm:block">{{ $item['label'] }}</span>
            </a>
        @endforeach
        </div>
    </div>
</nav>