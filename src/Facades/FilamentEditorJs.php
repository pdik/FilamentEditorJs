<?php

namespace Pdik\FilamentEditorJs\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Pdik\FilamentEditorJs\FilamentEditorJs
 */
class FilamentEditorJs extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Pdik\FilamentEditorJs\FilamentEditorJs::class;
    }
}
