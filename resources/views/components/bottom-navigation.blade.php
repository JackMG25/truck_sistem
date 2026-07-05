@php
    $items = [
        [
            'label' => 'Servicios',
            'url' => url('/servicios'),
            'active' => request()->is('servicios*'),
            'featured' => true,
            'icon' => 'M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9.75-4.5h9.75m-9.75 0a3 3 0 1 0 6 0m-9.75 0V10.5m0 4.25V15m12 3.75H15.75M15.75 15v4.5m0-4.5a3 3 0 0 0 3-3h2.25a3 3 0 0 1 3 3v4.5H15.75m-6-4.5h6m-6 0V10.5m6 4.5V10.5m0 0V9.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V10.5',
        ],
        [
            'label' => 'Dashboard',
            'url' => url('/dashboard'),
            'active' => request()->is('dashboard'),
            'icon' => 'M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z',
        ],
        [
            'label' => 'Cliente',
            'url' => url('/clientes'),
            'active' => request()->is('clientes*'),
            'icon' => 'M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z',
        ],
        [
            'label' => 'Agencia',
            'url' => url('/agencias'),
            'active' => request()->is('agencias*'),
            'icon' => 'M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z',
        ],
    ];
@endphp

<nav class="fixed inset-x-0 bottom-0 z-30 border-t border-slate-200/80 bg-white/95 pb-[max(0.5rem,env(safe-area-inset-bottom))] backdrop-blur-xl">
    <div class="mx-auto w-full max-w-5xl px-3 sm:px-4 lg:px-6">
        <div class="grid grid-cols-4 gap-1 rounded-2xl border border-slate-200/70 bg-slate-100/80 p-1.5 shadow-[0_-4px_24px_-4px_rgba(15,23,42,0.08)] sm:gap-2 sm:rounded-[1.75rem] sm:p-2">
        @foreach ($items as $item)
            @php
                $featured = $item['featured'] ?? false;
                $active = $item['active'];

                if ($featured && $active) {
                    $itemClass = 'bg-sky-600 text-white shadow-md shadow-sky-300/50 ring-1 ring-sky-500/30';
                } elseif ($featured) {
                    $itemClass = 'bg-sky-50 text-sky-700 ring-1 ring-sky-200 hover:bg-sky-100 hover:text-sky-800';
                } elseif ($active) {
                    $itemClass = 'bg-white text-slate-900 shadow-sm ring-1 ring-slate-200';
                } else {
                    $itemClass = 'text-slate-500 hover:bg-white/80 hover:text-slate-800';
                }
            @endphp
            <a
                href="{{ $item['url'] }}"
                class="flex min-w-0 flex-col items-center justify-center gap-0.5 rounded-xl px-0.5 py-2 text-[9px] font-semibold leading-tight transition-all duration-200 sm:gap-1 sm:rounded-2xl sm:px-2 sm:py-3 sm:text-[11px] {{ $itemClass }}"
                aria-label="{{ $item['label'] }}"
                @if ($active) aria-current="page" @endif
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="h-5 w-5 shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                </svg>
                <span class="block w-full truncate text-center">{{ $item['label'] }}</span>
            </a>
        @endforeach
        </div>
    </div>
</nav>
