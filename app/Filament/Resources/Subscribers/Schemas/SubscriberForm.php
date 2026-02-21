<?php

namespace App\Filament\Resources\Subscribers\Schemas;

use App\Enums\SubscriberStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class SubscriberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Select::make('status')
                    ->options(collect(SubscriberStatus::cases())->mapWithKeys(
                        fn (SubscriberStatus $status): array => [
                            $status->value => Str::headline($status->value),
                        ],
                    )->all())
                    ->required(),
                TextInput::make('confirmation_token')
                    ->maxLength(64),
                DateTimePicker::make('confirmed_at')
                    ->native(false),
                Select::make('locale')
                    ->options(collect(config('app.supported_locales', ['fr', 'en']))->mapWithKeys(
                        fn (string $locale): array => [
                            $locale => Str::upper($locale),
                        ],
                    )->all())
                    ->required(),
            ]);
    }
}
