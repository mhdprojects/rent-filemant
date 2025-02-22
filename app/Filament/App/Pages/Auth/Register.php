<?php

namespace App\Filament\App\Pages\Auth;

class Register extends \Filament\Pages\Auth\Register {

    protected function getForms(): array{
        return [
            'forms' => $this->form(
                $this->makeForm()->schema([
                    $this->getPasswordFormComponent(),
                    $this->getPasswordConfirmationFormComponent()
                ])
            ),
        ];
    }
}
