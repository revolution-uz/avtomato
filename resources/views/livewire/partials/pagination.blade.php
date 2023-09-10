<div class="row mx-2">
    <div class="col-sm-12 col-md-6">
        <div class="dataTables_info">
            Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} entries
        </div>
    </div>
    <div class="col-sm-12 col-md-6">
        <div class="dataTables_paginate paging_simple_numbers">
            @if ($paginator->hasPages())
            <ul class="pagination">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="paginate_button page-item previous disabled">
                        <a href="javascript:void(0)" class="page-link">@lang('pagination.previous')</a>
                    </li>
                @else
                    <li class="paginate_button page-item previous">
                        <a wire:click="previousPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" href="javascript:void(0)" class="page-link">@lang('pagination.previous')</a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="paginate_button page-item active" wire:key="paginator-{{ $paginator->getPageName() }}-page-{{ $page }}">
                                    <a href="javascript:void(0)" class="page-link">{{ $page }}</a>
                                </li>
                            @else
                                <li class="paginate_button page-item" wire:key="paginator-{{ $paginator->getPageName() }}-page-{{ $page }}">
                                    <a wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" href="javascript:void(0)" class="page-link">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="paginate_button page-item next">
                        <a wire:click="nextPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" href="javascript:void(0)" class="page-link">@lang('pagination.next')</a>
                    </li>
                @else
                    <li class="paginate_button page-item next disabled">
                        <a href="javascript:void(0)" class="page-link">@lang('pagination.next')</a>
                    </li>
                @endif
            </ul>
            @endif
        </div>
    </div>
</div>
