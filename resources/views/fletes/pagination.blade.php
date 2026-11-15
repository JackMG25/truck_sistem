@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Paginación" class="rounded-2xl border border-slate-200 bg-white px-2 py-2 shadow-lg shadow-sky-100/70">
        <div class="flex items-center justify-between gap-2">
            @if ($paginator->onFirstPage())
                <span class="inline-flex shrink-0 items-center rounded-xl px-2.5 py-1.5 text-sm font-semibold text-slate-300">{{ __('pagination.previous') }}</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex shrink-0 items-center rounded-xl px-2.5 py-1.5 text-sm font-semibold text-sky-700 hover:bg-sky-50">{{ __('pagination.previous') }}</a>
            @endif

            <p class="text-sm font-semibold text-slate-700">
                {{ $paginator->currentPage() }}
                <span class="font-medium text-slate-400">de</span>
                {{ $paginator->lastPage() }}
            </p>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex shrink-0 items-center rounded-xl px-2.5 py-1.5 text-sm font-semibold text-sky-700 hover:bg-sky-50">{{ __('pagination.next') }}</a>
            @else
                <span class="inline-flex shrink-0 items-center rounded-xl px-2.5 py-1.5 text-sm font-semibold text-slate-300">{{ __('pagination.next') }}</span>
            @endif
        </div>

        <div class="mt-1 flex flex-wrap items-center justify-center gap-1">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-0.5 text-sm text-slate-400">…</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg bg-sky-600 px-1.5 text-sm font-bold text-white">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg px-1.5 text-sm font-semibold text-slate-600 hover:bg-slate-100">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>
    </nav>
@endif
