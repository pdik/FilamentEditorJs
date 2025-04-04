@php
    $textVariant = $tunes['textVariant'] ?? '';
    $indentLevel = $tunes['indentTune']['indentLevel'] ?? 0;
    $anchor = $tunes['anchorTune']['anchor'] ?? null;

    $classes = ['editor-js-list'];

    if ($textVariant) {
        $classes[] = "text-variant-{$textVariant}";
    }

    $style = '';
    if ($indentLevel > 0) {
        $style = "margin-left: " . ($indentLevel * 2) . "rem;";
    }

    $listStyle = $data['style'] ?? 'unordered';
    $items = $data['items'] ?? [];

    $listTag = $listStyle === 'ordered' ? 'ol' : 'ul';
    $listClass = "list--{$listStyle}";

    // Add counter type class for ordered lists
    if ($listStyle === 'ordered' && isset($data['meta']['counterType'])) {
        $listClass .= " list--counter-{$data['meta']['counterType']}";
    }

    $noticeStyle = $tunes['noticeTune']['style'] ?? null;
    $noticeCaption = $tunes['noticeTune']['caption'] ?? null;

    if ($noticeStyle) {
        $classes[] = "notice-tune notice-tune--{$noticeStyle}";
    }
@endphp

<div
    class="{{ implode(' ', $classes) }}"
    style="{{ $style }}"
    @if($anchor) id="{{ $anchor }}" @endif
>
    @if($noticeStyle)
        <div class="notice-tune__header">
            <div class="notice-tune__caption">
                {{ $noticeCaption ?? ($noticeStyle === 'info' ? 'Information' : ($noticeStyle === 'warning' ? 'Warning' : 'Spoiler')) }}
            </div>
            @if($noticeStyle === 'spoiler')
                <button class="notice-tune__toggle" onclick="this.parentNode.nextElementSibling.classList.toggle('notice-tune__content--hidden'); this.textContent = this.textContent === 'Show' ? 'Hide' : 'Show'">
                    Show
                </button>
            @endif
        </div>
        <div class="notice-tune__content {{ $noticeStyle === 'spoiler' ? 'notice-tune__content--hidden' : '' }}">
            @if($listStyle === 'checklist')
                <ul class="list {{ $listClass }}">
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
                                    'level' => 1
                                ])
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <{{ $listTag }} class="list {{ $listClass }}">
                    @foreach($items as $item)
                        <li class="list-item">
                            {!! $item['content'] !!}

                            @if(!empty($item['items']))
                                @include('filamenteditorjs::blocks.list-nested', [
                                    'items' => $item['items'],
                                    'listStyle' => $listStyle,
                                    'level' => 1
                                ])
                            @endif
                        </li>
                    @endforeach
                </{{ $listTag }}>
            @endif
        </div>
    @else
        @if($listStyle === 'checklist')
            <ul class="list {{ $listClass }}">
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
                                'level' => 1
                            ])
                        @endif
                    </li>
                @endforeach
            </ul>
        @else
            <{{ $listTag }} class="list {{ $listClass }}">
                @foreach($items as $item)
                    <li class="list-item">
                        {!! $item['content'] !!}

                        @if(!empty($item['items']))
                            @include('filamenteditorjs::blocks.list-nested', [
                                'items' => $item['items'],
                                'listStyle' => $listStyle,
                                'level' => 1
                            ])
                        @endif
                    </li>
                @endforeach
            </{{ $listTag }}>
        @endif
    @endif
</div>
