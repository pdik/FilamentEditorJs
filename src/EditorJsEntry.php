<?php

namespace Pdik\FilamentEditorJs;

use Filament\Support\Components\ViewComponent;

class EditorJsEntry extends ViewComponent
{
    protected string $view = 'filamenteditorjs::editor-js-renderer';

    public function __construct(
        protected array | string | null $content = null,
    ) {
        if (is_string($this->content) && $this->isJson($this->content)) {
            $this->content = json_decode($this->content, true);
        }
    }

     private function isJson($string): bool
    {
        if (!is_string($string)) {
            return false;
        }

        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
    public function render(): \Illuminate\Contracts\View\View
    {
        return view($this->view, [
            'content' => $this->content,
        ]);
    }
}
