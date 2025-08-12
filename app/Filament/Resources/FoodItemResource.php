<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FoodItemResource\Pages;
use App\Filament\Resources\FoodItemResource\RelationManagers;
use App\Models\FoodItem;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class FoodItemResource extends Resource
{
    protected static ?string $model = FoodItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-cake';
    protected static ?string $navigationLabel = 'Food Items';
    protected static ?string $modelLabel = 'Food Item';
    protected static ?string $pluralModelLabel = 'Food Items';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Food Item Information')
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->reactive(),
                        
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                        
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        
                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        
                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->imageEditor()
                            ->imageCropAspectRatio('4:3')
                            ->imageResizeTargetWidth('400')
                            ->imageResizeTargetHeight('300')
                            ->directory('food-items')
                            ->disk('public')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->helperText('Upload a landscape image (400x300px recommended)')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Pricing')
                    ->schema([
                        // Rice category specific pricing (no half portion toggle needed)
                        Forms\Components\Fieldset::make('Rice Category Pricing')
                            ->visible(fn (Forms\Get $get) => in_array((Category::find($get('category_id'))?->name), ['Rice', 'Fried Rice']))
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('full_samba_price')
                                            ->label('Samba Full Price')
                                            ->required()
                                            ->numeric()
                                            ->prefix('Rs.')
                                            ->step(1),
                                        Forms\Components\TextInput::make('half_samba_price')
                                            ->label('Samba Half Price')
                                            ->required()
                                            ->numeric()
                                            ->prefix('Rs.')
                                            ->step(1),
                                        Forms\Components\TextInput::make('full_basmathi_price')
                                            ->label('Basmathi Full Price')
                                            ->required()
                                            ->numeric()
                                            ->prefix('Rs.')
                                            ->step(1),
                                        Forms\Components\TextInput::make('half_basmathi_price')
                                            ->label('Basmathi Half Price')
                                            ->required()
                                            ->numeric()
                                            ->prefix('Rs.')
                                            ->step(1),
                                    ]),
                            ]),
                            
                        // Beverage category pricing (with multiple sizes)
                        Forms\Components\Fieldset::make('Beverage Category Pricing')
                            ->visible(fn (Forms\Get $get) => (Category::find($get('category_id'))?->name) === 'Drinks')
                            ->schema([
                                Forms\Components\Toggle::make('has_drink_sizes')
                                    ->label('Has Multiple Drink Sizes')
                                    ->default(false)
                                    ->reactive(),
                                    
                                Forms\Components\KeyValue::make('beverage_prices')
                                    ->label('Drink Sizes and Prices')
                                    ->keyLabel('Size (e.g., 500ml, 1L, 1.5L, 2L)')
                                    ->valueLabel('Price (Rs.)')
                                    ->visible(fn (Forms\Get $get) => $get('has_drink_sizes'))
                                    ->helperText('Add different sizes and their prices. Example: 500ml = 200, 1L = 400, 1.5L = 600, 2L = 800')
                                    ->columnSpanFull(),
                            ]),
                            
                        // Non-rice category pricing (with half portion toggle)
                        Forms\Components\Fieldset::make('Non-Rice Category Pricing')
                            ->visible(fn (Forms\Get $get) => !in_array((Category::find($get('category_id'))?->name), ['Rice', 'Fried Rice', 'Drinks']))
                            ->schema([
                                Forms\Components\Toggle::make('has_half_portion')
                                    ->label('Has Half Portion')
                                    ->default(false)
                                    ->reactive(),
                                    
                                Forms\Components\TextInput::make('price')
                                    ->label('Base Price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rs.')
                                    ->step(1)
                                    ->visible(fn (Forms\Get $get) => !$get('has_half_portion'))
                                    ->helperText('Static item price. Dine-in adds 10% on the order total. Portion prices fall back to this when empty.'),

                                Forms\Components\Section::make('Portion Settings')
                                    ->schema([
                                        Forms\Components\TextInput::make('full_price')
                                            ->label('Full Portion Price')
                                            ->numeric()
                                            ->prefix('Rs.')
                                            ->step(1)
                                            ->required(fn (Forms\Get $get) => $get('has_half_portion'))
                                            ->visible(fn (Forms\Get $get) => $get('has_half_portion'))
                                            ->helperText('Price for full portion (overrides base price)'),
                                            
                                        Forms\Components\TextInput::make('half_price')
                                            ->label('Half Portion Price')
                                            ->numeric()
                                            ->prefix('Rs.')
                                            ->step(1)
                                            ->required(fn (Forms\Get $get) => $get('has_half_portion'))
                                            ->visible(fn (Forms\Get $get) => $get('has_half_portion'))
                                            ->helperText('Price for half portion'),
                                    ])
                                    ->columns(1)
                                    ->visible(fn (Forms\Get $get) => $get('has_half_portion')),
                            ]),
                            

                    ])
                    ->columns(2),
                
                
                
                Forms\Components\Section::make('Settings')
                    ->schema([
                        Forms\Components\Toggle::make('is_available')
                            ->required()
                            ->default(true),
                        
                        Forms\Components\Toggle::make('is_featured')
                            ->required()
                            ->default(false),
                        
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Rice category pricing columns
                Tables\Columns\TextColumn::make('full_samba_price')
                    ->label('Samba Full')
                    ->formatStateUsing(fn ($state) => $state ? 'Rs. ' . number_format($state, 0) : '-')
                    ->visible(fn ($record) => $record && $record->category && in_array($record->category->name, ['Rice', 'Fried Rice']))
                    ->sortable(),
                Tables\Columns\TextColumn::make('half_samba_price')
                    ->label('Samba Half')
                    ->formatStateUsing(fn ($state) => $state ? 'Rs. ' . number_format($state, 0) : '-')
                    ->visible(fn ($record) => $record && $record->category && in_array($record->category->name, ['Rice', 'Fried Rice']))
                    ->sortable(),
                Tables\Columns\TextColumn::make('full_basmathi_price')
                    ->label('Basmathi Full')
                    ->formatStateUsing(fn ($state) => $state ? 'Rs. ' . number_format($state, 0) : '-')
                    ->visible(fn ($record) => $record && $record->category && in_array($record->category->name, ['Rice', 'Fried Rice']))
                    ->sortable(),
                Tables\Columns\TextColumn::make('half_basmathi_price')
                    ->label('Basmathi Half')
                    ->formatStateUsing(fn ($state) => $state ? 'Rs. ' . number_format($state, 0) : '-')
                    ->visible(fn ($record) => $record && $record->category && in_array($record->category->name, ['Rice', 'Fried Rice']))
                    ->sortable(),
                
                // Beverage category pricing columns
                Tables\Columns\TextColumn::make('beverage_prices')
                    ->label('Drink Sizes')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return '-';
                        
                        $prices = [];
                        foreach ($state as $size => $price) {
                            $prices[] = "$size: Rs. " . number_format($price, 0);
                        }
                        return implode(', ', $prices);
                    })
                    ->visible(fn ($record) => $record && $record->category && $record->category->name === 'Drinks' && $record->has_drink_sizes)
                    ->sortable()
                    ->toggleable(),
                
                // Non-rice category pricing columns
                Tables\Columns\TextColumn::make('price')
                    ->label('Base Price')
                    ->formatStateUsing(fn ($state) => $state ? 'Rs. ' . number_format($state, 0) : '-')
                    ->visible(fn ($record) => $record && $record->category && !in_array($record->category->name, ['Rice', 'Fried Rice', 'Drinks']))
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('full_price')
                    ->label('Full Price')
                    ->formatStateUsing(fn ($state) => $state ? 'Rs. ' . number_format($state, 0) : '-')
                    ->visible(fn ($record) => $record && $record->category && !in_array($record->category->name, ['Rice', 'Fried Rice', 'Drinks']))
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('half_price')
                    ->label('Half Price')
                    ->formatStateUsing(fn ($state) => $state ? 'Rs. ' . number_format($state, 0) : '-')
                    ->visible(fn ($record) => $record && $record->category && !in_array($record->category->name, ['Rice', 'Fried Rice', 'Drinks']) && $record->has_half_portion)
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('image')
                    ->label('Image')
                    ->html()
                    ->formatStateUsing(function ($state) {
                        if ($state) {
                            return '<img src="' . asset('storage/' . $state) . '" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">';
                        }
                        return '<img src="' . asset('images/placeholder-food.svg') . '" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">';
                    }),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('has_half_portion')
                    ->label('Half Portion')
                    ->boolean()
                    ->visible(fn ($record) => $record && $record->category && !in_array($record->category->name, ['Rice', 'Fried Rice', 'Drinks']))
                    ->sortable(),
                Tables\Columns\IconColumn::make('has_drink_sizes')
                    ->label('Drink Sizes')
                    ->boolean()
                    ->visible(fn ($record) => $record && $record->category && $record->category->name === 'Drinks')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_available')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name'),
                Tables\Filters\TernaryFilter::make('is_available')
                    ->label('Available'),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured'),
                Tables\Filters\TernaryFilter::make('has_half_portion')
                    ->label('Has Half Portion'),
                Tables\Filters\TernaryFilter::make('has_drink_sizes')
                    ->label('Has Drink Sizes'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFoodItems::route('/'),
            'create' => Pages\CreateFoodItem::route('/create'),
            'edit' => Pages\EditFoodItem::route('/{record}/edit'),
        ];
    }
}
