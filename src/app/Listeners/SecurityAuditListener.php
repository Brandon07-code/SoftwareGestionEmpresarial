<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Request;

class SecurityAuditListener
{
    public function handleLogin(Login $event): void
    {
        activity('seguridad')
            ->causedBy($event->user)
            ->withProperties([
                'ip' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ])
            ->log('Inicio de sesión exitoso');
    }

    public function handleLogout(Logout $event): void
    {
        if ($event->user) {
            activity('seguridad')
                ->causedBy($event->user)
                ->withProperties([
                    'ip' => Request::ip(),
                ])
                ->log('Cierre de sesión de usuario');
        }
    }

    public function handleFailed(Failed $event): void
    {
        activity('seguridad')
            ->causedBy($event->user)
            ->withProperties([
                'email' => $event->credentials['email'] ?? null,
                'ip' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ])
            ->log('Intento fallido de inicio de sesión');
    }

    public function handlePasswordReset(PasswordReset $event): void
    {
        activity('seguridad')
            ->causedBy($event->user)
            ->withProperties([
                'ip' => Request::ip(),
            ])
            ->log('Restablecimiento de contraseña');
    }

    public function subscribe(): array
    {
        return [
            Login::class => 'handleLogin',
            Logout::class => 'handleLogout',
            Failed::class => 'handleFailed',
            PasswordReset::class => 'handlePasswordReset',
        ];
    }
}
