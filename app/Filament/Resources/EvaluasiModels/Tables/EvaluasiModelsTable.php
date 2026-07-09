<?php

namespace App\Filament\Resources\EvaluasiModels\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EvaluasiModelsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->columns([
                TextColumn::make('metode')
                    ->label('Metode')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('jumlah_data_training')
                    ->label('Data Training')
                    ->numeric()
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('jumlah_data_testing')
                    ->label('Data Testing')
                    ->numeric()
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('akurasi')
                    ->label('Akurasi')
                    ->suffix('%')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                TextColumn::make('precision')
                    ->label('Precision')
                    ->suffix('%')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('recall')
                    ->label('Recall')
                    ->suffix('%')
                    ->badge()
                    ->color('warning')
                    ->sortable(),

                TextColumn::make('f1_score')
                    ->label('F1 Score')
                    ->suffix('%')
                    ->badge()
                    ->color('danger')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diubah')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus Terpilih'),
                ]),
            ]);
    }
}