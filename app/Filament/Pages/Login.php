<?php

namespace App\Filament\Pages;

use Filament\Pages\Auth\Login as AuthLogin;

class Login extends AuthLogin
{
    public function mount(): void
    {
        parent::mount();

        $this->data = [
            'email' => 'testuser@testmail.com',
            'password' => 'password',
        ];
    }
}
