<?php

namespace Pdik\FilamentEditorJs\Commands;

use Illuminate\Console\Command;

class FilamentEditorJsCommand extends Command
{
    public $signature = 'filamenteditorjs';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
