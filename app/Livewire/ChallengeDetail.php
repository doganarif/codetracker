<?php

namespace App\Livewire;

use App\Models\Challenge;
use Livewire\Component;

class ChallengeDetail extends Component
{
    public Challenge $challenge;

    public $is_accepted;

    public $user_event_count;

    public $progress;

    public $top_users; // New property to store top users by event count

    public function mount(Challenge $challenge)
    {
        $this->challenge = $challenge;

        // Check if the user has accepted the challenge
        $users = $this->challenge->users()->pluck('users.id')->toArray();
        $this->is_accepted = in_array(auth()->id(), $users);

        // Calculate progress if the user has accepted the challenge
        if ($this->is_accepted) {
            // Get the count of events the user has during the challenge's period
            $this->user_event_count = auth()->user()->events()
                ->where('event_date', '>=', $this->challenge->start_date)
                ->where('event_date', '<=', $this->challenge->end_date)
                ->count();

            // Calculate the progress based on required_count and user event count
            $this->progress = min(100, ($this->user_event_count / $this->challenge->required_count) * 100);
        } else {
            $this->user_event_count = 0;
            $this->progress = 0;
        }

        // Get top users by event count for this challenge
        $this->top_users = $this->challenge->users()
            ->withCount(['events' => function ($query) {
                $query->where('event_date', '>=', $this->challenge->start_date)
                    ->where('event_date', '<=', $this->challenge->end_date);
            }])
            ->orderBy('events_count', 'desc')
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.challenge-detail');
    }
}
