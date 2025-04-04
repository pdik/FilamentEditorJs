@php
    $textVariant = $tunes['textVariant'] ?? '';
    $indentLevel = $tunes['indentTune']['indentLevel'] ?? 0;
    $anchor = $tunes['anchorTune']['anchor'] ?? null;

    $classes = ['editor-js-image-hotspot'];

    if ($textVariant) {
        $classes[] = "text-variant-{$textVariant}";
    }

    $style = '';
    if ($indentLevel > 0) {
        $style = "margin-left: " . ($indentLevel * 2) . "rem;";
    }

    // Get image URL
    $imageUrl = '';
    if (isset($data['image']['url'])) {
        $imageUrl = $data['image']['url'];
    } elseif (isset($data['src'])) {
        $imageUrl = $data['src'];
    }

    $caption = $data['caption'] ?? '';
    $hotspots = $data['hotspots'] ?? [];

    $noticeStyle = $tunes['noticeTune']['style'] ?? null;
    $noticeCaption = $tunes['noticeTune']['caption'] ?? null;

    if ($noticeStyle) {
        $classes[] = "notice-tune notice-tune--{$noticeStyle}";
    }

    // Generate a unique ID for this hotspot image
    $hotspotId = 'hotspot-' . uniqid();
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
            <div class="image-hotspot-container" id="{{ $hotspotId }}">
                <div class="image-wrapper">
                    <img
                        src="{{ $imageUrl }}"
                        alt="{{ $data['image']['alt'] ?? '' }}"
                        class="hotspot-image"
                    />

                    @foreach($hotspots as $hotspot)
                        <div
                            class="hotspot"
                            style="left: {{ $hotspot['x'] }}%; top: {{ $hotspot['y'] }}%;"
                            data-id="{{ $hotspot['id'] }}"
                            onclick="toggleHotspot('{{ $hotspotId }}', '{{ $hotspot['id'] }}')"
                        >
                            <div class="hotspot-marker"></div>
                            <div class="hotspot-content" id="{{ $hotspotId }}-{{ $hotspot['id'] }}-content" style="display: none;">
                                <div class="hotspot-header">
                                    <h4 class="hotspot-title">{{ $hotspot['title'] ?: 'Hotspot' }}</h4>
                                    <button class="close-button" onclick="event.stopPropagation(); toggleHotspot('{{ $hotspotId }}', '{{ $hotspot['id'] }}')">×</button>
                                </div>
                                <div class="hotspot-body">
                                    @if(is_string($hotspot['content']))
                                        @foreach(explode("\n\n", $hotspot['content']) as $paragraph)
                                            @if(trim($paragraph))
                                                <p>{!! nl2br(e($paragraph)) !!}</p>
                                            @endif
                                        @endforeach
                                    @else
                                        {!! $hotspot['content'] !!}
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($caption)
                    <div class="image-caption">{{ $caption }}</div>
                @endif
            </div>
        </div>
    @else
        <div class="image-hotspot-container" id="{{ $hotspotId }}">
            <div class="image-wrapper">
                <img
                    src="{{ $imageUrl }}"
                    alt="{{ $data['image']['alt'] ?? '' }}"
                    class="hotspot-image"
                />

                @foreach($hotspots as $hotspot)
                    <div
                        class="hotspot"
                        style="left: {{ $hotspot['x'] }}%; top: {{ $hotspot['y'] }}%;"
                        data-id="{{ $hotspot['id'] }}"
                        onclick="toggleHotspot('{{ $hotspotId }}', '{{ $hotspot['id'] }}')"
                    >
                        <div class="hotspot-marker"></div>
                        <div class="hotspot-content" id="{{ $hotspotId }}-{{ $hotspot['id'] }}-content" style="display: none;">
                            <div class="hotspot-header">
                                <h4 class="hotspot-title">{{ $hotspot['title'] ?: 'Hotspot' }}</h4>
                                <button class="close-button" onclick="event.stopPropagation(); toggleHotspot('{{ $hotspotId }}', '{{ $hotspot['id'] }}')">×</button>
                            </div>
                            <div class="hotspot-body">
                                @if(is_string($hotspot['content']))
                                    @foreach(explode("\n\n", $hotspot['content']) as $paragraph)
                                        @if(trim($paragraph))
                                            <p>{!! nl2br(e($paragraph)) !!}</p>
                                        @endif
                                    @endforeach
                                @else
                                    {!! $hotspot['content'] !!}
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($caption)
                <div class="image-caption">{{ $caption }}</div>
            @endif
        </div>
    @endif
</div>

<script>
    function toggleHotspot(containerId, hotspotId) {
        const contentElement = document.getElementById(`${containerId}-${hotspotId}-content`);
        const container = document.getElementById(containerId);

        // Close all other hotspots in this container
        const allHotspotContents = container.querySelectorAll('.hotspot-content');
        allHotspotContents.forEach(el => {
            if (el.id !== `${containerId}-${hotspotId}-content`) {
                el.style.display = 'none';
            }
        });

        // Toggle this hotspot
        if (contentElement) {
            contentElement.style.display = contentElement.style.display === 'none' ? 'block' : 'none';
        }
    }
</script>
