@php
    $nestedListTag = $listStyle === 'ordered' ? 'ol' : 'ul';
    $nestedListClass = "nested-list nested-list--level-{$level} list--{$listStyle}";
@endphp

@if($listStyle === 'checklist')
    <ul class="{{ $nestedListClass }}">
        @foreach($items as $item)
            <li class="list-item">
                <div class="checklist-item">
                    <div class="checkbox {{ isset($item['meta']['checked']) && $item['meta']['checked'] ? 'checkbox--checked' : '' }}">
                        @if(isset($item['meta']['checked']) && $item['meta']['checked'])
                            <svg viewBox="0 0 14 14" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.5 7L6.5 9L10 4.5" stroke="currentColor" stroke-width="2" fill="none" />
                            </svg>
                        @endif
                    </div>
                    <div class="checklist-content {{ isset($item['meta']['checked']) && $item['meta']['checked'] ? 'checklist-content--checked' : '' }}">
                        {!! $item['content'] !!}
                    </div>
                </div>

                @if(!empty($item['items']))
                    @include('filamenteditorjs::blocks.list-nested', [
                        'items' => $item['items'],
                        'listStyle' => $listStyle,
                        'level' => $level + 1
                    ])
                @endif
            </li>
        @endforeach
    </ul>
@else
    <{{ $nestedListTag }} class="{{ $nestedListClass }}">
        @foreach($items as $item)
            <li class="list-item">
                {!! $item['content'] !!}

                @if(!empty($item['items']))
                    @include('filamenteditorjs::blocks.list-nested', [
                        'items' => $item['items'],
                        'listStyle' => $listStyle,
                        'level' => $level + 1
                    ])
                @endif
            </li>
        @endforeach
    </{{ $nestedListTag }}>
@endif
