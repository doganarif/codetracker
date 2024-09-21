<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'profile_picture',
        'nickname',
        'github_id',
        'github_access_token',
        'github_refresh_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'remember_token',
    ];

    public function challenges(): BelongsToMany
    {
        return $this->belongsToMany(Challenge::class, 'challenge_user')
            ->withPivot('joined_at', 'total_count', 'is_active')
            ->withTimestamps();
    }

    public function events(): HasMany
    {
        return $this->hasMany(UserEvent::class);
    }

    public function fetchGithubData()
    {
        $publicEvents = $this->getUserPublicEvents();
        $orgs = $this->getUserOrganizations();
        $orgEvents = $this->getUserOrganizationEvents($orgs);

        $allEvents = collect($publicEvents)->merge($orgEvents);

        $filteredEvents = $allEvents->filter(function ($event) {
            return in_array($event['type'], ['PushEvent', 'IssuesEvent', 'PullRequestEvent']) &&
                   (! isset($event['payload']['action']) || $event['payload']['action'] === 'opened');
        });

        foreach ($filteredEvents as $event) {
            $this->saveEvent($event);
        }
    }

    private function getUserPublicEvents()
    {
        $response = Http::withToken($this->github_access_token)
            ->get("https://api.github.com/users/{$this->nickname}/events?per_page=100");

        return $response->successful() ? $response->json() : [];
    }

    private function getUserOrganizations()
    {
        $response = Http::withToken($this->github_access_token)
            ->get('https://api.github.com/user/orgs');

        return $response->successful() ? $response->json() : [];
    }

    private function getUserOrganizationEvents($orgs)
    {
        return collect($orgs)->flatMap(function ($org) {
            $response = Http::withToken($this->github_access_token)
                ->get("https://api.github.com/users/{$this->nickname}/events/orgs/{$org['login']}?per_page=100");

            return $response->successful() ? $response->json() : [];
        });
    }

    private function saveEvent($event)
    {
        // Check if the event already exists in the database
        if (UserEvent::where('event_id', $event['id'])->exists()) {
            return; // Skip this event if it already exists
        }

        $eventData = [
            'user_id' => $this->id,
            'event_id' => $event['id'],
            'repo_name' => $event['repo']['name'] ?? 'Unknown Repo',
            'type' => $event['type'],
            'event_date' => Carbon::parse($event['created_at'])->format('Y-m-d H:i:s'),
        ];

        if ($event['type'] === 'PushEvent') {
            $eventData['title'] = collect($event['payload']['commits'])->pluck('message')->implode("\n");
            $eventData['description'] = null;
        } elseif (in_array($event['type'], ['PullRequestEvent', 'IssuesEvent'])) {
            $key = $event['type'] === 'PullRequestEvent' ? 'pull_request' : 'issue';
            $eventData['title'] = $event['payload'][$key]['title'] ?? 'No Title';
            $eventData['description'] = $event['payload'][$key]['body'] ?? 'No Description';
        }

        UserEvent::create($eventData);
    }
}
