<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class LoginWithGithubController extends Controller
{
    public function redirectToGithub()
    {
        return Socialite::driver('github')
            ->scopes(['repo', 'read:org'])
            ->redirect();
    }

    public function __invoke(Request $request)
    {
        $githubUser = Socialite::driver('github')->user();

        $user = User::updateOrCreate(
            ['github_id' => $githubUser->id],
            [
                'name' => $githubUser->name,
                'email' => $githubUser->email,
                'nickname' => $githubUser->nickname,
                'profile_picture' => $githubUser->avatar,
                'github_access_token' => $githubUser->token,
                'github_refresh_token' => $githubUser->token, // Use token as refresh token
            ]
        );

        auth()->login($user, true);

        $user->fetchGithubData();

        return redirect()->route('dashboard');
    }
}
