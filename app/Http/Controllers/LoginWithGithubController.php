<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;

class LoginWithGithubController extends Controller
{
    public function __invoke(Request $request)
    {
        // Retrieve the GitHub user information
        $user = Socialite::driver('github')->user();

        // Check if the user already exists, otherwise create a new one
        $existingUser = User::where('github_id', $user->id)->first();

        if ($existingUser) {
            // Log in the existing user
            auth()->login($existingUser, true);
        } else {
            // Create a new user in the database
            $newUser = User::create([
                'name' => $user->name,
                'email' => $user->email,
                'nickname' => $user->nickname,
                'github_id' => $user->id,
                'profile_picture' => $user->avatar,
                'github_access_token' => $user->token,
                'github_refresh_token' => $user->refreshToken,
            ]);

            // Log in the new user
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

        // Get public and private events for the user
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

    // Get public and private events for the user
    private function getUserPublicEvents($nickname, $token)
    {
        // Fetch public and private events
        $response = Http::withToken($token)->get("https://api.github.com/users/$nickname/events?per_page=100");

        if ($response->successful()) {
            return $response->json();
        }

        return [];
    }

    // Get all organizations the user belongs to
    private function getUserOrganizations($token)
    {
        // Fetch user's organizations
        $response = Http::withToken($token)->get('https://api.github.com/user/orgs');

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
            // Fetch organization events
            $response = Http::withToken($token)->get("https://api.github.com/orgs/$orgName/events");

            if ($response->successful()) {
                $orgEvents = $orgEvents->merge($response->json());
            }
        }

        return $orgEvents;
    }
}
