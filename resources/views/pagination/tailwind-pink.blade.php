@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman" class="flex flex-wrap items-center justify-center gap-1.5 sm:gap-2">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="inline-flex h-10 min-w-[2.5rem] cursor-not-allowed items-center justify-center rounded-full border border-brand-100 bg-brand-50/60 text-sm text-brand-300" aria-disabled="true">
                <span class="sr-only">Sebelumnya</span>
                <i class="fas fa-chevron-left text-xs" aria-hidden="true"></i>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex h-10 min-w-[2.5rem] items-center justify-center rounded-full border border-brand-200 bg-white text-sm font-medium text-brand-600 shadow-sm transition hover:border-brand-400 hover:bg-brand-50 hover:text-brand-700 hover:shadow-pink-soft focus:outline-none focus:ring-2 focus:ring-brand-300">
                <span class="sr-only">Sebelumnya</span>
                <i class="fas fa-chevron-left text-xs" aria-hidden="true"></i>
            </a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="inline-flex h-10 items-center px-2 text-sm font-medium text-brand-400">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page" class="inline-flex h-10 min-w-[2.5rem] items-center justify-center rounded-full bg-gradient-to-r from-brand-400 to-brand-600 px-3 text-sm font-semibold text-white shadow-md shadow-brand-400/35">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" class="inline-flex h-10 min-w-[2.5rem] items-center justify-center rounded-full border border-transparent bg-white px-3 text-sm font-medium text-brand-800 transition hover:border-brand-200 hover:bg-brand-50 hover:text-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-200">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex h-10 min-w-[2.5rem] items-center justify-center rounded-full border border-brand-200 bg-white text-sm font-medium text-brand-600 shadow-sm transition hover:border-brand-400 hover:bg-brand-50 hover:text-brand-700 hover:shadow-pink-soft focus:outline-none focus:ring-2 focus:ring-brand-300">
                <span class="sr-only">Berikutnya</span>
                <i class="fas fa-chevron-right text-xs" aria-hidden="true"></i>
            </a>
        @else
            <span class="inline-flex h-10 min-w-[2.5rem] cursor-not-allowed items-center justify-center rounded-full border border-brand-100 bg-brand-50/60 text-sm text-brand-300" aria-disabled="true">
                <span class="sr-only">Berikutnya</span>
                <i class="fas fa-chevron-right text-xs" aria-hidden="true"></i>
            </span>
        @endif
    </nav>
@endif
