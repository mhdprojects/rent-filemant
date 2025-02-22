<?php

namespace App\Filament\App\Pages\Tenancy;

use App\Models\Tenant;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Pages\Tenancy\RegisterTenant;
use Illuminate\Support\Str;

class RegisterTeam extends RegisterTenant{

      public static function getLabel(): string{
            return 'Register team';
      }

      public function form(Form $form): Form{
            return $form
                  ->schema([
                      TextInput::make('name')
                          ->label('Nama Perusahaan Anda')
                          ->required()
                          ->maxLength(255)
                          ->live(onBlur: true)
                          ->afterStateUpdated(fn (string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),

                      TextInput::make('slug')
                          ->label('Prefix')
                          ->dehydrated()
                          ->required()
                          ->maxLength(255)
                          ->unique(Tenant::class, 'slug', ignoreRecord: true),
                  ]);
      }

      protected function handleRegistration(array $data): Tenant{
            $team = Tenant::create($data);

            $team->members()->attach(auth()->user());

            return $team;
      }
}
