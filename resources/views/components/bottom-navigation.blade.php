<nav class="fixed inset-x-0 bottom-0 z-30 border-t border-slate-200/80 bg-white/95 pb-[max(0.5rem,env(safe-area-inset-bottom))] backdrop-blur-xl">
    <div class="mx-auto flex w-full max-w-5xl items-center gap-2 px-4 py-2 sm:px-6">
        <a
            href="{{ route('fletes.index') }}"
            class="flex min-w-0 flex-1 items-center justify-center gap-2 rounded-2xl px-4 py-3 text-sm font-semibold {{ request()->routeIs('fletes*') ? 'bg-sky-600 text-white shadow-md shadow-sky-200' : 'bg-slate-100 text-slate-700' }}"
            @if (request()->routeIs('fletes*')) aria-current="page" @endif
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="h-5 w-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9.75h7.5M8.25 12.75h7.5m-7.5 3h7.5M3.75 6.75h16.5a.75.75 0 0 1 .75.75v10.5a.75.75 0 0 1-.75.75H3.75a.75.75 0 0 1-.75-.75V7.5a.75.75 0 0 1 .75-.75Z" />
            </svg>
            Fletes
        </a>

        <a
            href="{{ route('profile.edit') }}"
            class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl {{ request()->routeIs('profile.*') ? 'bg-sky-600 text-white shadow-md shadow-sky-200' : 'bg-slate-100 text-slate-700' }}"
            aria-label="Configurar perfil"
            title="Perfil"
            @if (request()->routeIs('profile.*')) aria-current="page" @endif
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="h-5 w-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
            </svg>
        </a>
    </div>
</nav>
