@php
    $textVariant = $tunes['textVariant'] ?? '';
    $indentLevel = $tunes['indentTune']['indentLevel'] ?? 0;
    $anchor = $tunes['anchorTune']['anchor'] ?? null;

    $classes = ['editor-js-alert'];

    if ($textVariant) {
        $classes[] = "text-variant-{$textVariant}";
    }

    $style = '';
    if ($indentLevel > 0) {
        $style = "margin-left: " . ($indentLevel * 2) . "rem;";
    }

    $type = $data['type'] ?? 'primary';
    $title = $data['title'] ?? '';
    $message = $data['message'] ?? '';

    $noticeStyle = $tunes['noticeTune']['style'] ?? null;
    $noticeCaption = $tunes['noticeTune']['caption'] ?? null;

    if ($noticeStyle) {
        $classes[] = "notice-tune notice-tune--{$noticeStyle}";
    }

    // Define icon based on alert type
    $icon = '';
    switch ($type) {
        case 'info':
            $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>';
            break;
        case 'success':
            $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>';
            break;
        case 'warning':
            $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>';
            break;
        case 'danger':
            $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>';
            break;
        case 'dark':
            $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>';
            break;
        case 'primary':
        default:
            $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>';
            break;
    }

    $showIcon = $type !== 'light' && $type !== 'secondary';
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
            <div class="alert alert-{{ $type }}" role="alert">
                @if($showIcon)
                    <div class="alert-icon">
                        {!! $icon !!}
                    </div>
                @endif
                <div class="alert-content">
                    @if($title)
                        <div class="alert-title">{{ $title }}</div>
                    @endif
                    <div class="alert-message">{!! $message !!}</div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-{{ $type }}" role="alert">
            @if($showIcon)
                <div class="alert-icon">
                    {!! $icon !!}
                </div>
            @endif
            <div class="alert-content">
                @if($title)
                    <div class="alert-title">{{ $title }}</div>
                @endif
                <div class="alert-message">{!! $message !!}</div>
            </div>
        </div>
    @endif
</div>
