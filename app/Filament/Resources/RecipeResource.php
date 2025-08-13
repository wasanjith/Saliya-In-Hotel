<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecipeResource\Pages;
use App\Models\Recipe;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RecipeResource extends Resource
{
	protected static ?string $model = Recipe::class;

	protected static ?string $navigationIcon = 'heroicon-o-beaker';

	protected static ?string $navigationGroup = 'Inventory';

	public static function form(Form $form): Form
	{
		return $form
			->schema([
				Forms\Components\Select::make('food_item_id')
					->relationship('foodItem', 'name')
					->required()
					->searchable()
					->preload(),
				Forms\Components\TextInput::make('name')->required()->maxLength(255)->helperText('Give this recipe a friendly name, e.g. Default'),
				Forms\Components\Textarea::make('notes')->columnSpanFull(),
				Forms\Components\Repeater::make('items')
					->relationship()
					->defaultItems(0)
					->schema([
						Forms\Components\Select::make('inventory_item_id')
							->relationship('inventoryItem', 'name')
							->required()
							->searchable()
							->preload(),
						Forms\Components\TextInput::make('quantity')->numeric()->required()->helperText('Quantity required for one full portion (uses inventory unit)'),
					])
				->columns(2)
				->itemLabel('Ingredient')
				->columnSpanFull(),
			]);
	}

	public static function table(Table $table): Table
	{
		return $table
			->columns([
				Tables\Columns\TextColumn::make('name')->searchable(),
				Tables\Columns\TextColumn::make('foodItem.name')->label('Food Item')->searchable()->sortable(),
				Tables\Columns\TextColumn::make('items_count')->counts('items')->label('Ingredients'),
				Tables\Columns\TextColumn::make('updated_at')->dateTime()->since(),
			])
			->actions([
				Tables\Actions\EditAction::make(),
			])
			->bulkActions([
				Tables\Actions\DeleteBulkAction::make(),
			]);
	}

	public static function getRelations(): array
	{
		return [];
	}

	public static function getPages(): array
	{
		return [
			'index' => Pages\ListRecipes::route('/'),
			'create' => Pages\CreateRecipe::route('/create'),
			'edit' => Pages\EditRecipe::route('/{record}/edit'),
		];
	}
}


