<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryCategoryResource\Pages;
use App\Models\InventoryCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InventoryCategoryResource extends Resource
{
	protected static ?string $model = InventoryCategory::class;

	protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

	protected static ?string $navigationGroup = 'Inventory';

	public static function form(Form $form): Form
	{
		return $form
			->schema([
				Forms\Components\TextInput::make('name')->required()->maxLength(255),
				Forms\Components\Toggle::make('is_active')->default(true),
				Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
			]);
	}

	public static function table(Table $table): Table
	{
		return $table
			->columns([
				Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
				Tables\Columns\IconColumn::make('is_active')->boolean(),
				Tables\Columns\TextColumn::make('sort_order')->sortable(),
				Tables\Columns\TextColumn::make('created_at')->dateTime()->since(),
			])
			->filters([
				Tables\Filters\TernaryFilter::make('is_active'),
			])
			->actions([
				Tables\Actions\EditAction::make(),
			])
			->bulkActions([
				Tables\Actions\DeleteBulkAction::make(),
			]);
	}

	public static function getPages(): array
	{
		return [
			'index' => Pages\ListInventoryCategories::route('/'),
			'create' => Pages\CreateInventoryCategory::route('/create'),
			'edit' => Pages\EditInventoryCategory::route('/{record}/edit'),
		];
	}
}


