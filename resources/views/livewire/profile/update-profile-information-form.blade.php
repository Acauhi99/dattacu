<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;


new class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $cnpj = '';
    public string $telefone = '';
    public bool $editing = false;
    
    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
        $this->cnpj = Auth::user()->cnpj;
        $this->telefone = Auth::user()->telefone;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'cnpj' => ['required', 'string', 'max:255'],
            'telefone' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $path = session('url.intended', RouteServiceProvider::HOME);

            $this->redirect($path);

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Informações Pessoais') }}
        </h2>

    </header>

    <div class="mt-6 space-y-6">
        {{-- Nome Atual --}}
        <div>
            <x-input-label for="name" :value="__('Nome')" />
            <span class="mt-1 block w-full">{{ $name }}</span>
        </div>

        {{-- CNPJ Atual --}}
        <div>
            <x-input-label for="cnpj" :value="__('CNPJ')" />
            <span class="mt-1 block w-full">{{ $cnpj }}</span>
        </div>

        {{-- Telefone Atual --}}
        <div>
            <x-input-label for="telefone" :value="__('Telefone')" />
            <span class="mt-1 block w-full">{{ $telefone }}</span>
        </div>

        {{-- Email Atual --}}
        <div>
            <x-input-label for="email" :value="__('E-mail')" />
            <span class="mt-1 block w-full">{{ $email }}</span>
        </div>

        <button wire:click="$set('editing', true)" class="text-blue-500 underline cursor-pointer">
        {{ __('Editar informações') }}
        </button>

        {{-- Modal --}}
        @if($editing)
        <div class="fixed inset-0 flex items-center justify-center bg-gray-500 bg-opacity-75">
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white p-4" style="width: 400px; max-width: 80%;">
                <form wire:submit.prevent="updateProfileInformation">
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
                        <input type="text" id="name" wire:model="name" class="mt-1 p-2 border rounded-md w-full" >
                    </div>
    
                    <div class="mb-4">
                        <label for="cnpj" class="block text-sm font-medium text-gray-700">CNPJ</label>
                        <input type="text" id="cnpj" wire:model="cnpj" class="mt-1 p-2 border rounded-md w-full" >
                    </div>
    
                    <div class="mb-4">
                        <label for="telefone" class="block text-sm font-medium text-gray-700">Telefone</label>
                        <input type="text" id="telefone" wire:model="telefone" class="mt-1 p-2 border rounded-md w-full" >
                    </div>
    
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
                        <input type="text" id="email" wire:model="email" class="mt-1 p-2 border rounded-md w-full" >
                    </div>
    
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2">Save</button>
                    <button wire:click="$set('editing', false)" class="text-red-500 underline cursor-pointer">Cancel</button>
                </form>
            </div>
        </div>
        @endif
    </div>

</section>
