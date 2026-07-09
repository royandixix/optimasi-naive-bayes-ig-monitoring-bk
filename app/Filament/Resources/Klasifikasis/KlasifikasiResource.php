<?php

namespace App\Filament\Resources\Klasifikasis;

use App\Filament\Resources\Klasifikasis\Pages\CreateKlasifikasi;
use App\Filament\Resources\Klasifikasis\Pages\EditKlasifikasi;
use App\Filament\Resources\Klasifikasis\Pages\ListKlasifikasis;
use App\Filament\Resources\Klasifikasis\Schemas\KlasifikasiForm;
use App\Filament\Resources\Klasifikasis\Tables\KlasifikasisTable;
use App\Models\Klasifikasi;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class KlasifikasiResource extends Resource
{
    protected static ?string $model = Klasifikasi::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCpuChip;

    protected static string|UnitEnum|null $navigationGroup = 'Algoritma Klasifikasi';

    protected static ?string $navigationLabel = 'Klasifikasi Naive Bayes';

    protected static ?string $modelLabel = 'Klasifikasi';

    protected static ?string $pluralModelLabel = 'Klasifikasi Naive Bayes';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return KlasifikasiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KlasifikasisTable::configure($table);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasAnyRole([
            'super_admin',
            'kepala_sekolah',
        ]) ?? false;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole([
            'super_admin',
            'kepala_sekolah',
        ]) ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->isGuruBk() ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->isGuruBk() ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->isGuruBk() ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->isGuruBk() ?? false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKlasifikasis::route('/'),
            'create' => CreateKlasifikasi::route('/create'),
            'edit' => EditKlasifikasi::route('/{record}/edit'),
        ];
    }
}