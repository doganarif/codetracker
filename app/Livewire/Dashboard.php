<?php

namespace App\Livewire;

use App\Models\Challenge;
use Livewire\Attributes\On;
use Livewire\Component;

class Dashboard extends Component
{
    public $challenges;

    public $userChallenges;

    public $acceptedChallenges;

    public $activeChallenge;

    public $progress;

    public function render()
    {
        // Get all challenges, ordered by 'start_date' in descending order
        $this->challenges = Challenge::orderBy('start_date', 'desc')->get();

        // Retrieve the challenges the user has accepted
        $this->userChallenges = auth()->user()->challenges;

        // Get IDs of accepted challenges
        $this->acceptedChallenges = $this->userChallenges->pluck('id')->toArray();

        // Find the active challenge if there's any
        $this->activeChallenge = $this->userChallenges->where('pivot.is_active', true)->first();

        // If there is an active challenge, calculate the progress
        if ($this->activeChallenge) {
            $this->calculateProgress();
        }

        return view('livewire.dashboard');
    }

    // Method to calculate the user's progress in the active challenge
    protected function calculateProgress()
    {
        // Count the user's events within the active challenge's start and end dates
        $userEventCount = auth()->user()->events()
            ->where('event_date', '>=', $this->activeChallenge->start_date)
            ->where('event_date', '<=', $this->activeChallenge->end_date)
            ->count();

        // Calculate progress as a percentage (0-100%)
        $this->progress = min(100, ($userEventCount / $this->activeChallenge->required_count) * 100);
    }

    #[On('challengeAccepted')]
    public function handleChallengeAccepted($challengeId): void
    {
        $challenge = $this->challenges->firstWhere('id', $challengeId);
        if ($challenge) {
            // Ensure only one challenge is active at a time
            auth()->user()->challenges()->updateExistingPivot($this->acceptedChallenges, ['is_active' => false]);

            // Attach or update the new active challenge for the user
            auth()->user()->challenges()->syncWithoutDetaching([$challengeId => [
                'joined_at' => now(),
                'is_active' => true,
            ]]);

            // Refresh the active challenge and calculate progress
            $this->activeChallenge = $challenge;
            $this->calculateProgress();
        }
    }

    #[On('challengeCancelled')]
    public function handleChallengeCancelled($challengeId)
    {
        $challenge = $this->challenges->firstWhere('id', $challengeId);
        if ($challenge) {
            // Detach the challenge from the user
            auth()->user()->challenges()->detach($challengeId);

            // If this was the active challenge, clear the active challenge
            if ($this->activeChallenge && $this->activeChallenge->id == $challengeId) {
                $this->activeChallenge = null;
                $this->progress = 0;

                // Check if the user has any other active challenges
                $remainingActiveChallenge = auth()->user()->challenges()->wherePivot('is_active', true)->first();
                if ($remainingActiveChallenge) {
                    $this->activeChallenge = $remainingActiveChallenge;
                    $this->calculateProgress();
                }
            }
        }
    }
}
