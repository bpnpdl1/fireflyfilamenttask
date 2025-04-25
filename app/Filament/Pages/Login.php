<?php

namespace App\Pages;

use Filament\Pages\Auth\Login as AuthLogin;

class Login extends AuthLogin
{

    public function mount(): void
    {
        parent::mount();

        $this->data = [
            'email' => 'admin@gmail.com',
            'password' => 'password',
        ];
    }
}