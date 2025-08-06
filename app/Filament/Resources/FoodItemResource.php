<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FoodItemResource\Pages;
use App\Filament\Resources\FoodItemResource\RelationManagers;
use App\Models\FoodItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                            ->preload(),
                        
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', \Str::slug($state)) : null),
                        
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
                        Forms\Components\TextInput::make('dine_in_price')
                            ->label('Dine-in Price (Legacy)')
                            ->numeric()
                            ->prefix('Rs.')
                            ->step(1)
                            ->helperText('Legacy field - use portion-specific pricing below'),
                        
                        Forms\Components\TextInput::make('takeaway_price')
                            ->label('Takeaway Price (Legacy)')
                            ->numeric()
                            ->prefix('Rs.')
                            ->step(1)
                            ->helperText('Legacy field - use portion-specific pricing below'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Portion Pricing')
                    ->schema([
                        Forms\Components\Toggle::make('has_half_portion')
                            ->label('Has Half Portion')
                            ->default(false)
                            ->reactive(),
                        
                        Forms\Components\TextInput::make('full_portion_name')
                            ->label('Full Portion Name')
                            ->default('Full Portion')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('half_portion_name')
                            ->label('Half Portion Name')
                            ->default('Half Portion')
                            ->maxLength(255)
                            ->visible(fn (Forms\Get $get) => $get('has_half_portion')),
                        
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('full_portion_dine_in_price')
                                    ->label('Full Portion - Dine-in Price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rs.')
                                    ->step(1),
                                
                                Forms\Components\TextInput::make('full_portion_takeaway_price')
                                    ->label('Full Portion - Takeaway Price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rs.')
                                    ->step(1),
                                
                                Forms\Components\TextInput::make('half_portion_dine_in_price')
                                    ->label('Half Portion - Dine-in Price')
                                    ->numeric()
                                    ->prefix('Rs.')
                                    ->step(1)
                                    ->visible(fn (Forms\Get $get) => $get('has_half_portion')),
                                
                                Forms\Components\TextInput::make('half_portion_takeaway_price')
                                    ->label('Half Portion - Takeaway Price')
                                    ->numeric()
                                    ->prefix('Rs.')
                                    ->step(1)
                                    ->visible(fn (Forms\Get $get) => $get('has_half_portion')),
                            ]),
                    ])
                    ->columns(1),
                
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
                Tables\Columns\TextColumn::make('full_portion_dine_in_price')
                    ->label('Full Portion - Dine-in')
                    ->formatStateUsing(fn ($state) => $state ? 'Rs. ' . number_format($state, 0) : '-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('half_portion_dine_in_price')
                    ->label('Half Portion - Dine-in')
                    ->formatStateUsing(fn ($state) => $state ? 'Rs. ' . number_format($state, 0) : '-')
                    ->sortable()
                    ->visible(fn ($record) => $record && $record->has_half_portion),
                Tables\Columns\IconColumn::make('has_half_portion')
                    ->label('Half Portion')
                    ->boolean()
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
