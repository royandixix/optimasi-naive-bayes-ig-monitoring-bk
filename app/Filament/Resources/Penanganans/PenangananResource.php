<?php

namespace App\Filament\Resources\Penanganans;

use App\Filament\Resources\Penanganans\Pages\CreatePenanganan;
use App\Filament\Resources\Penanganans\Pages\EditPenanganan;
use App\Filament\Resources\Penanganans\Pages\ListPenanganans;
use App\Filament\Resources\Penanganans\Schemas\PenangananForm;
use App\Filament\Resources\Penanganans\Tables\PenanganansTable;
use App\Models\Penanganan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class PenangananResource extends Resource
{
    protected static ?string $model = Penanganan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static string|UnitEnum|null $navigationGroup = 'Bimbingan Konseling';

    protected static ?string $navigationLabel = 'Data Penanganan';

    protected static ?string $modelLabel = 'Penanganan';

    protected static ?string $pluralModelLabel = 'Data Penanganan';

    protected static ?int $navigationSort = 40;

    public static function form(Schema $schema): Schema
    {
        return PenangananForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PenanganansTable::configure($table);
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
            'index' => ListPenanganans::route('/'),
            'create' => CreatePenanganan::route('/create'),
            'edit' => EditPenanganan::route('/{record}/edit'),
        ];
    }
}