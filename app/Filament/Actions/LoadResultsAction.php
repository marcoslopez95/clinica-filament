<?php

namespace App\Filament\Actions;

use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use App\Models\Exam;

class LoadResultsAction
{
    public static function make(string $name = 'load_results'): Action
    {
        return Action::make($name)
            ->label('Cargar resultados')
            ->icon('heroicon-o-pencil')
            ->modalWidth('lg')
            ->modalContent(fn (Model $record) => view('filament.actions.manage-exam-results', ['record' => $record]))
            ->visible(fn (?Model $record) => isset($record) && ($record->content_type ?? null) === Exam::class);
    }
}
