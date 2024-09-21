<?php

use App\Http\Controllers\LoginWithGithubController;
use App\Livewire\ChallengeDetail;
use App\Livewire\Dashboard;
use App\Livewire\EventDetail;
use App\Livewire\MyEvents;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

Route::view('/', 'welcome');

Route::get('dashboard', Dashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('my-events', MyEvents::class)
    ->middleware(['auth', 'verified'])
    ->name('my-events');

Route::get('/events/{eventId}', EventDetail::class)->name('events.detail');

// Challenge detail route
Route::get('challenge/{challenge}', ChallengeDetail::class)
    ->middleware(['auth', 'verified'])
    ->name('challenge.detail');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/auth/github', function () {
    return Socialite::driver('github')
        ->scopes(['repo', 'read:org']) // Request access to private repositories
        ->redirect();
})->name('auth.github');

Route::get('/auth/github/callback', LoginWithGithubController::class);

require __DIR__.'/auth.php';
