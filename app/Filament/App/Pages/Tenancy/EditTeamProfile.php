<?php

namespace App\Filament\App\Pages\Tenancy;

use App\Models\Tenant;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Pages\Tenancy\EditTenantProfile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class EditTeamProfile extends EditTenantProfile
{
      public static function getLabel(): string
      {
            return 'Tenant Profile';
      }

      public function form(Form $form): Form
      {
            return $form
                  ->schema([
                      TextInput::make('name')
                          ->label('Nama Perusahaan Anda')
                          ->required()
                          ->maxLength(255)
                          ->live(onBlur: true)
                          ->afterStateUpdated(fn (string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),

                      TextInput::make('slug')
                          ->disabled()
                          ->dehydrated()
                          ->required()
                          ->maxLength(255)
                          ->unique(Tenant::class, 'slug', ignoreRecord: true),

                      FileUpload::make('logo')
                          ->disk('public')
                          ->directory('logo')
                          ->getUploadedFileNameForStorageUsing(
                              fn (TemporaryUploadedFile $file): string => (string) Str::slug(str($file->getClientOriginalName())
                                      ->prepend(time().'-')).'.'.$file->getClientOriginalExtension(),
                          )
                          ->image()
                          ->maxSize(1024)
                          ->visibility('public'),
                  ]);
      }
}
