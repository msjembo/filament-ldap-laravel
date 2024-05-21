<?php

namespace App\Filament\Auth;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Facades\Filament;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Filament\Http\Livewire\Auth\Login as BasePage;

class Login extends BasePage implements HasForms
{

    public $email = '';
    public $password = '';
    public $remember = false;

    public function authenticate(): ?LoginResponse
    {
        // try {
        //     $this->rateLimit(5);
        // } catch (TooManyRequestsException $exception) {
        //     $this->addError('username', __('filament::login.messages.throttled', [
        //         'seconds' => $exception->secondsUntilAvailable,
        //         'minutes' => ceil($exception->secondsUntilAvailable / 60),
        //     ]));

        //     return null;
        // }

        $data = $this->form->getState();

        if (! Filament::auth()->attempt([
            'mail' => $data['email'],
            'password' => $data['password'],
        ], $data['remember'])) {
            $this->addError('username', __('filament::login.messages.failed'));

            return null;
        }

        return app(LoginResponse::class);
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('username')
                ->label(__('Username'))
                ->required()
                ->autocomplete(),
            TextInput::make('password')
                ->label(__('filament::login.fields.password.label'))
                ->password()
                ->required(),
            Checkbox::make('remember')
                ->label(__('filament::login.fields.remember.label')),
        ];
    }
}