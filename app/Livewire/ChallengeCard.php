<?php

namespace App\Livewire;

use Carbon\Carbon;
use Livewire\Component;

class ChallengeCard extends Component
{
    public $challengeId;
    public $challengeName;
    public $status;
    public $startDate;
    public $endDate;
    public $isActive;
    public $description;
    public $isOutdated;
    public $acceptedUsers = [];
    public $hasStarted;

    public function mount($challengeId, $challengeName, $description, $startDate, $endDate, $isActive, $acceptedUsers = [])
    {
        $this->challengeId = $challengeId;
        $this->challengeName = $challengeName;
        $this->startDate = Carbon::parse($startDate)->format('Y-m-d H:i:s');
        $this->endDate = Carbon::parse($endDate)->format('Y-m-d H:i:s');
        $this->description = $description;
        $this->isActive = $isActive;
        $this->acceptedUsers = $acceptedUsers;

        // Check if the current date is past the end date
        $this->isOutdated = Carbon::now()->gt(Carbon::parse($this->endDate));

        // Check if the challenge has started
        $this->hasStarted = Carbon::now()->gte(Carbon::parse($this->startDate));
    }

    public function acceptChallenge()
    {
        if (!$this->isOutdated && $this->hasStarted) {
            $this->isActive = true;
            $this->dispatch('challengeAccepted', challengeId: $this->challengeId);

            // Add the user to the accepted users list
            $this->acceptedUsers[] = auth()->user();
        }
    }

    public function cancelChallenge()
    {
        $this->isActive = false;
        $this->dispatch('challengeCancelled', challengeId: $this->challengeId);

        $this->acceptedUsers = array_filter($this->acceptedUsers, function($userId) {
            return $userId !== auth()->user();
        });
    }

    public function render()
    {
        return view('livewire.challenge-card');
    }
}
