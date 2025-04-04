@php
    $textVariant = $tunes['textVariant'] ?? '';
    $indentLevel = $tunes['indentTune']['indentLevel'] ?? 0;
    $anchor = $tunes['anchorTune']['anchor'] ?? null;

    $classes = ['editor-js-paragraph'];

    if ($textVariant) {
        $classes[] = "text-variant-{$textVariant}";
    }

    $style = '';
    if ($indentLevel > 0) {
        $style = "margin-left: " . ($indentLevel * 2) . "rem;";
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
            <p>{!! $data['text'] ?? '' !!}</p>
        </div>
    @else
        <p>{!! $data['text'] ?? '' !!}</p>
    @endif
</div>
