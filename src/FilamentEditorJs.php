<?php

namespace Pdik\FilamentEditorJs;
use Filament\Forms\Components\Concerns\HasFileAttachments;
use Filament\Forms\Components\Concerns\HasPlaceholder;
use Filament\Forms\Components\Contracts\HasFileAttachments as HasFileAttachmentsContract;
use Filament\Forms\Components\Field;
class FilamentEditorJs extends Field implements HasFileAttachmentsContract
{
    use HasFileAttachments, HasPlaceholder;

    protected string $view = 'filamenteditorjs::editor-js';
}
