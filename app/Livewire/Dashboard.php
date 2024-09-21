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

    public function render()
    {
        // Order challenges by 'start_date' in descending order so the latest starting challenges appear first
        $this->challenges = Challenge::orderBy('start_date', 'desc')->get();

        // Retrieve the challenges the user has accepted
        $this->userChallenges = auth()->user()->challenges;

        // Instead of relying on an 'is_active' field, we will use the challenge ids in the user's challenges
        $this->acceptedChallenges = $this->userChallenges->pluck('id')->toArray();

        return view('livewire.dashboard');
    }

    #[On('challengeAccepted')]
    public function handleChallengeAccepted($challengeId): void
    {
        $challenge = $this->challenges->firstWhere('id', $challengeId);
        if ($challenge) {
            // Attach challenge to the user. It will be attached only once.
            auth()->user()->challenges()->syncWithoutDetaching([$challengeId => [
                'joined_at' => now(),
                'is_active' => true,
            ]]);
        }
    }

    #[On('challengeCancelled')]
    public function handleChallengeCancelled($challengeId)
    {
        $challenge = $this->challenges->firstWhere('id', $challengeId);
        if ($challenge) {
            // Detach challenge from the user
            auth()->user()->challenges()->detach($challengeId);
        }
    }
}
