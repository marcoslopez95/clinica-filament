<?php

namespace App\Filament\Actions;

use Filament\Tables\Actions\DeleteBulkAction;

class RefreshTotalDeleteBulkAction extends DeleteBulkAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Nuevo descuento');

        $this->after(function ($livewire) {
            $livewire->dispatch('refreshTotal');
        });
    }
}
