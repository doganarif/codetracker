<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Http;

class LoginWithGithubController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = Socialite::driver('github')->user();

        // Check if the user already exists, otherwise create a new one
        $existingUser = User::where('github_id', $user->id)->first();

        if ($existingUser) {
            auth()->login($existingUser, true);
        } else {
            $newUser = User::create([
                'name' => $user->name,
                'email' => $user->email,
                'nickname' => $user->nickname,
                'github_id' => $user->id,
                'profile_picture' => $user->avatar,
                'github_access_token' => $user->token,
                'github_refresh_token' => $user->refreshToken,
            ]);

            auth()->login($newUser, true);
        }

        // Fetch user activity from GitHub including organizations' events
        dd($this->fetchGithubActivity(auth()->user()));
    }

    // Function to fetch GitHub activity
    private function fetchGithubActivity(User $user)
    {
        $token = $user->github_access_token;
        $nickname = $user->nickname;

        // Get public events for the user
        $publicEvents = $this->getUserPublicEvents($nickname, $token);

        // Get all organizations the user belongs to
        $orgs = $this->getUserOrganizations($token);

        // Get events from all organizations
        $orgEvents = $this->getUserOrganizationEvents($orgs, $token);

        // Merge public events and organization events into a single list
        $allEvents = collect($publicEvents)->merge($orgEvents);

        // Format the events
        $activities = $allEvents->map(function ($event) {
            return [
                'type' => $event['type'],
                'repo' => $event['repo']['name'],
                'created_at' => $event['created_at'],
            ];
        });

        // Return all activities
        return $activities;
    }

    // Get public events for the user
    private function getUserPublicEvents($nickname, $token)
    {
        $response = Http::withToken($token)->get("https://api.github.com/users/$nickname/events");

        if ($response->successful()) {
            return $response->json();
        }

        return [];
    }

    // Get all organizations the user belongs to
    private function getUserOrganizations($token)
    {
        $response = Http::withToken($token)->get('https://api.github.com/user/orgs');

        dd($response->json());
        if ($response->successful()) {
            return $response->json();
        }

        return [];
    }

    // Get events for all organizations the user belongs to
    private function getUserOrganizationEvents($orgs, $token)
    {
        $orgEvents = collect();

        foreach ($orgs as $org) {
            $orgName = $org['login'];
            $response = Http::withToken($token)->get("https://api.github.com/orgs/$orgName/events");

            if ($response->successful()) {
                $orgEvents = $orgEvents->merge($response->json());
            }
        }

        return $orgEvents;
    }
}
