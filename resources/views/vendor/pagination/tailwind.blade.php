@if ($paginator->hasPages())
    <nav class="pagination" role="navigation" aria-label="{{ __('Pagination Navigation') }}">
        <div class="flex items-center justify-between gap-3 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="inline-flex min-h-10 items-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-400" aria-disabled="true">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <a class="inline-flex min-h-10 items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition-colors hover:border-primary-500 hover:bg-primary-50 hover:text-primary-800" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a class="inline-flex min-h-10 items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition-colors hover:border-primary-500 hover:bg-primary-50 hover:text-primary-800" href="{{ $paginator->nextPageUrl() }}" rel="next">
                    {!! __('pagination.next') !!}
                </a>
            @else
                <span class="inline-flex min-h-10 items-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-400" aria-disabled="true">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>

        <div class="hidden items-center justify-between gap-4 sm:flex">
            <p class="text-sm text-slate-600">
                {!! __('Showing') !!}
                @if ($paginator->firstItem())
                    <span class="font-medium text-slate-800">{{ $paginator->firstItem() }}</span>
                    {!! __('to') !!}
                    <span class="font-medium text-slate-800">{{ $paginator->lastItem() }}</span>
                @else
                    {{ $paginator->count() }}
                @endif
                {!! __('of') !!}
                <span class="font-medium text-slate-800">{{ $paginator->total() }}</span>
                {!! __('results') !!}
            </p>

            <div class="inline-flex overflow-hidden rounded-lg border border-slate-300 bg-white shadow-sm">
                @if ($paginator->onFirstPage())
                    <span class="inline-flex min-h-10 min-w-10 items-center justify-center border-r border-slate-200 px-2 text-slate-400" aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                    </span>
                @else
                    <a class="inline-flex min-h-10 min-w-10 items-center justify-center border-r border-slate-200 px-2 text-slate-600 transition-colors hover:bg-primary-50 hover:text-primary-800" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                    </a>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="inline-flex min-h-10 min-w-10 items-center justify-center border-r border-slate-200 px-3 text-sm text-slate-500">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="inline-flex min-h-10 min-w-10 items-center justify-center border-r border-primary-600 bg-primary-600 px-3 text-sm font-semibold text-white" aria-current="page">{{ $page }}</span>
                            @else
                                <a class="inline-flex min-h-10 min-w-10 items-center justify-center border-r border-slate-200 bg-white px-3 text-sm font-medium text-slate-700 transition-colors hover:bg-primary-50 hover:text-primary-800" href="{{ $url }}" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <a class="inline-flex min-h-10 min-w-10 items-center justify-center px-2 text-slate-600 transition-colors hover:bg-primary-50 hover:text-primary-800" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L10.586 10l-3.293 3.293a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                    </a>
                @else
                    <span class="inline-flex min-h-10 min-w-10 items-center justify-center px-2 text-slate-400" aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10l-3.293-3.293a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L10.586 10l-3.293 3.293a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif
