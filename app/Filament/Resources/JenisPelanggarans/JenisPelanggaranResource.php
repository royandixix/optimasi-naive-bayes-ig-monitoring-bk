<?php

namespace App\Filament\Resources\JenisPelanggarans;

use App\Filament\Resources\JenisPelanggarans\Pages\CreateJenisPelanggaran;
use App\Filament\Resources\JenisPelanggarans\Pages\EditJenisPelanggaran;
use App\Filament\Resources\JenisPelanggarans\Pages\ListJenisPelanggarans;
use App\Filament\Resources\JenisPelanggarans\Schemas\JenisPelanggaranForm;
use App\Filament\Resources\JenisPelanggarans\Tables\JenisPelanggaransTable;
use App\Models\JenisPelanggaran;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class JenisPelanggaranResource extends Resource
{
    protected static ?string $model = JenisPelanggaran::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldExclamation;

    protected static ?string $navigationLabel = 'Jenis Pelanggaran';

    protected static ?string $modelLabel = 'Jenis Pelanggaran';

    protected static ?string $pluralModelLabel = 'Jenis Pelanggaran';

    protected static ?string $recordTitleAttribute = 'nama_jenis';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'Master Data';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'primary';
    }

    public static function form(Schema $schema): Schema
    {
        return JenisPelanggaranForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JenisPelanggaransTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJenisPelanggarans::route('/'),
            'create' => CreateJenisPelanggaran::route('/create'),
            'edit' => EditJenisPelanggaran::route('/{record}/edit'),
        ];
    }
}