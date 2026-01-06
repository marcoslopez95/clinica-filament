<?php

namespace App\Filament\Actions;

use Filament\Tables\Actions\DeleteAction;

class RefreshTotalDeleteAction extends DeleteAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->after(function ($livewire) {
            $livewire->dispatch('refreshTotal');
        });
    }
}
