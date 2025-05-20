<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema(([
                        Forms\Components\Section::make('Data Diri')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('gender')
                                    ->label('Jenis Kelamin')
                                    ->required(),
                                Forms\Components\DatePicker::make('birthday'),
                            ])
                    ])),
                Forms\Components\Group::make()
                    ->schema(([
                        Forms\Components\Section::make('Kontak')
                            ->schema([
                                Forms\Components\TextInput::make('phone')
                                    ->tel()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255),
                            ])
                    ])),
                Forms\Components\Section::make('Produk dipesan')
                    ->schema([
                        self::getItemsRepeater(),
                    ]),
                Forms\Components\Group::make()
                    ->schema(([
                        Forms\Components\Section::make('Pembayaran')
                            ->schema([
                                Forms\Components\TextInput::make('total_price')
                                    ->required()
                                    ->numeric(),
                                Forms\Components\TextInput::make('paid_amount')
                                    ->required()
                                    ->numeric(),

                                Forms\Components\TextInput::make('change_amount')
                                    ->numeric(),
                                Forms\Components\Select::make('payment_method_id')
                                    ->relationship('paymentMethod', 'name'),
                            ])
                    ])),
                Forms\Components\Group::make()
                    ->schema(([
                        Forms\Components\Section::make('Pesan')
                            ->schema([
                                Forms\Components\Textarea::make('note')
                                    ->columnSpanFull(),
                            ])
                    ]))
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('gender'),
                Tables\Columns\TextColumn::make('birthday')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('paymentMethod.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('paid_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('change_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
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

    public static function getItemsRepeater(): Repeater
    {
        return Repeater::make('orderProducts')
            ->relationship()
            ->live()
            ->columns([
                'md' => 10,
            ])
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label('Produk')
                    ->required()
                    ->options(Product::query()->where('stock', '>', 1)->pluck('name', 'id'))
                    ->columnSpan([
                        'md' => 5,
                    ])
                    ->afterStateUpdated(function ($state,  Forms\Set $set, Forms\Get $get) {
                        $product = Product::find($state);
                        $set('unit_price', $product->price);
                        $set('stock', $product->stock ?? 0);
                    }),
                Forms\Components\TextInput::make('quantity')
                    ->label('Jumlah Pembelian')
                    ->required()
                    ->numeric()
                    ->columnSpan([
                        'md' => 2,
                    ]),
                Forms\Components\TextInput::make('stock')
                    ->label('Stok')
                    // ->required()
                    ->numeric()
                    // ->disabled()
                    ->readOnly()
                    ->columnSpan([
                        'md' => 1,
                    ]),
                Forms\Components\TextInput::make('unit_price')
                    ->label('Harga')
                    // ->required()
                    ->numeric()
                    ->readOnly()
                    ->columnSpan([
                        'md' => 1,
                    ]),

            ]);
    }
}
