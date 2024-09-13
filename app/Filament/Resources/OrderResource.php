<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ButtonAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Log;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form

            ->schema([
                Forms\Components\Section::make('')
                    ->schema([
                        Grid::make(1)->schema([
                            Repeater::make('order_product')  // Assuming 'order_product' is the relationship name
                                ->label('Products')
                                ->relationship('order_product') // Your existing relationship
                                ->schema([
                                    Select::make('category_id')
                                        ->relationship('category', 'name')
                                        ->label('Category Type')
                                        ->preload()
                                        ->searchable()
                                        ->required()
                                        ->reactive()
                                        ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                            $set('product_id', null);
                                        }),

                                    Select::make('product_id')
                                        ->label('Product')
                                        ->options(function (callable $get) {
                                            $categoryId = $get('category_id');
                                            if (!$categoryId) {
                                                return [];
                                            }
                                            return Product::where('category_id', $categoryId)
                                                ->pluck('name', 'id');
                                        })
                                        ->preload()
                                        ->searchable()
                                        ->required()
                                        ->reactive(),

                                    TextInput::make('quantity')
                                        ->numeric()
                                        ->required()
                                ])
                                ->createItemButtonLabel('Add Product')
                                ->reactive(),
                        ]),

                        Select::make('customer_id')
                            ->relationship('customer', 'name')
                            ->label('Customer')
                            ->preload()
                            ->searchable()
                            ->required(),

                        TextInput::make('shipping_price')
                            ->required()
                            ->numeric()
                            ->label('Shipping Price')
                            ->prefix('$'),

                        Select::make('status')
                            ->label('Order Status')
                            ->required()
                            ->options([
                                'processing' => 'Processing',
                                'delivered' => 'Delivered',
                                'shipped' => 'Shipped',
                                'cancelled' => 'Cancelled',
                            ]),

                        TextInput::make('total_price') 
                            ->label('Total Amount')
                            ->numeric()
                            ->prefix('$')
                            // ->disabled() // Disable input to prevent manual edits, it's calculated automatically
                    ]),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('status'),
                TextColumn::make('order_product.quantity'),
                TextColumn::make('total_price')->money('usd'),
                TextColumn::make('shipping_price')->money('usd'),
                TextColumn::make('customer.name')->label('Customer'),
                TextColumn::make('user.name')->label('User'),
                TextColumn::make('order_product.category.name')->label('Category'),
                TextColumn::make('order_product.products.name')->label('Products'),
                TextColumn::make('created_at')->date(),
            ])
            ->filters([
                // Define your filters here
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
