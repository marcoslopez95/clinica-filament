<?php

namespace App\Filament\Actions;

use Filament\Tables\Actions\EditAction;

class RefreshTotalEditAction extends EditAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->after(function ($livewire) {
            $livewire->dispatch('refreshTotal');
        });
    }
}
