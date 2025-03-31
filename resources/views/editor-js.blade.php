@php use Filament\Support\Facades\FilamentAsset; @endphp
<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div ax-load
         ax-load-src="{{ FilamentAsset::getAlpineComponentSrc('editor') }}"
         x-data="EditorComponent({ state: $wire.$entangle('{{ $getStatePath() }}'),statePath: '{{ $getStatePath() }}', })"
         x-ignore
         class="rounded-md shadow-sm editorjs-wrapper w-full" >

    <div    wire:ignore
            x-ref="editor"
        class="">
    </div>
</div>

</x-dynamic-component>