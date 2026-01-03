<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TypeDocumentResource\Pages;
use App\Filament\Resources\TypeDocumentResource\Schemas\TypeDocumentForm;
use App\Filament\Resources\TypeDocumentResource\Tables\TypeDocumentsTable;
use App\Models\TypeDocument;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TypeDocumentResource extends Resource
{
    protected static ?string $model = TypeDocument::class;

    protected static ?string $slug = 'type-documents';

    protected static ?string $navigationGroup = 'Configuración';
    protected static ?string $navigationIcon = 'heroicon-o-flag';
    protected static ?string $modelLabel = 'Tipo de Documento';
    protected static ?string $pluralModelLabel = 'Tipos de Documentos';
    protected static ?string $navigationLabel = 'Tipos de Documentos';

    public static function form(Form $form): Form
    {
        return TypeDocumentForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return TypeDocumentsTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTypeDocuments::route('/'),
            'create' => Pages\CreateTypeDocument::route('/create'),
            'edit' => Pages\EditTypeDocument::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }
}
