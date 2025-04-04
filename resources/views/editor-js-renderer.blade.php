@if (is_array($content) && isset($content['blocks']) && count($content['blocks']) > 0)
    <div class="editor-js-content">
        @foreach ($content['blocks'] as $block)
            @php
                $blockType = $block['type'] ?? null;
                $blockData = $block['data'] ?? [];
                $blockTunes = $block['tunes'] ?? [];
            @endphp

            @if ($blockType)
                @include("filamenteditorjs::blocks.{$blockType}", [
                    'data' => $blockData,
                    'tunes' => $blockTunes,
                ])
            @endif
        @endforeach
    </div>
@endif
