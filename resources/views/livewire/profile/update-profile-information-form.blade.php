<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public string $name = '';
    public $profilePicture;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->profilePicture = Auth::user()->profile_picture;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'profilePicture' => ['nullable', 'image', 'max:1024'], // Validating image upload
        ]);

        $user->fill($validated);

        if ($this->profilePicture) {
            // Store the profile pictures
            $path = $this->profilePicture->store('profile-pictures', 'public');
            $user->profile_picture = $path;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and profile picture.") }}
        </p>
    </header>

    <form wire:submit="updateProfileInformation" class="mt-6 space-y-6">
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input wire:model="name" id="name" name="name" type="text" class="mt-1 block w-full" readonly required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>
        <!-- email field -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" value="{{ Auth::user()->email }}" readonly required autofocus autocomplete="email" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>



        <!-- Profile Picture Upload -->
        <div>
            <x-input-label for="profilePicture" :value="__('Profile Picture')" />

            <div class="mt-2">
                @if (auth()->user()->profile_picture)
                    <div class="relative inline-block">
                        <img src="{{ auth()->user()->profile_picture }}" alt="{{ __('Profile Picture') }}" class="w-20 h-20 rounded-full object-cover">
                    </div>
                @else
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center text-gray-500">
                        {{ __('No Image') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            <x-action-message class="me-3" on="profile-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>
