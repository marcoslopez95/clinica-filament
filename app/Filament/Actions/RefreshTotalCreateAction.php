<?php

namespace App\Filament\Actions;

use Filament\Tables\Actions\CreateAction;

class RefreshTotalCreateAction extends CreateAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->after(function ($livewire) {
            $livewire->dispatch('refreshTotal');
        });
    }
}
