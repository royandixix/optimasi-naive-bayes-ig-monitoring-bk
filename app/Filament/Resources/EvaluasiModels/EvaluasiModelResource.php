<?php

namespace App\Filament\Resources\EvaluasiModels;

use App\Filament\Resources\EvaluasiModels\Pages\CreateEvaluasiModel;
use App\Filament\Resources\EvaluasiModels\Pages\EditEvaluasiModel;
use App\Filament\Resources\EvaluasiModels\Pages\ListEvaluasiModels;
use App\Filament\Resources\EvaluasiModels\Schemas\EvaluasiModelForm;
use App\Filament\Resources\EvaluasiModels\Tables\EvaluasiModelsTable;
use App\Models\EvaluasiModel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class EvaluasiModelResource extends Resource
{
    protected static ?string $model = EvaluasiModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBarSquare;

    protected static ?string $recordTitleAttribute = 'metode';

    protected static ?string $navigationLabel = 'Evaluasi Model';

    protected static string|UnitEnum|null $navigationGroup = 'Machine Learning';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Evaluasi Model';

    protected static ?string $pluralModelLabel = 'Evaluasi Model';

    public static function form(Schema $schema): Schema
    {
        return EvaluasiModelForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EvaluasiModelsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEvaluasiModels::route('/'),
            'create' => CreateEvaluasiModel::route('/create'),
            'edit' => EditEvaluasiModel::route('/{record}/edit'),
        ];
    }
}