<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryItemResource\Pages;
use App\Models\InventoryItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InventoryItemResource extends Resource
{
	protected static ?string $model = InventoryItem::class;

	protected static ?string $navigationIcon = 'heroicon-o-archive-box';

	protected static ?string $navigationGroup = 'Inventory';

	public static function form(Form $form): Form
	{
		return $form
			->schema([
				Forms\Components\Select::make('category_id')
					->relationship('category', 'name')
					->required()
					->searchable()
					->preload(),
				Forms\Components\TextInput::make('name')->required()->maxLength(255),
				Forms\Components\TextInput::make('unit')->required()->maxLength(10)->default('g')->helperText('e.g. g, kg, ml, l, pcs'),
				Forms\Components\TextInput::make('quantity')->numeric()->default(0),
				Forms\Components\TextInput::make('reorder_level')->numeric()->default(0),
				Forms\Components\Toggle::make('is_active')->default(true),
			]);
	}

	public static function table(Table $table): Table
	{
		return $table
			->columns([
				Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
				Tables\Columns\TextColumn::make('category.name')->label('Category')->sortable(),
				Tables\Columns\TextColumn::make('unit')->label('Unit'),
				Tables\Columns\TextColumn::make('quantity')->numeric(3)->label('Stock'),
				Tables\Columns\TextColumn::make('reorder_level')->numeric(3)->label('Reorder'),
				Tables\Columns\IconColumn::make('is_active')->boolean(),
				Tables\Columns\TextColumn::make('updated_at')->dateTime()->since(),
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
			'index' => Pages\ListInventoryItems::route('/'),
			'create' => Pages\CreateInventoryItem::route('/create'),
			'edit' => Pages\EditInventoryItem::route('/{record}/edit'),
		];
	}
}


