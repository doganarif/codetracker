<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Challenge;

class ChallengeDetail extends Component
{
    public Challenge $challenge;

    public function mount(Challenge $challenge)
    {
        $this->challenge = $challenge;

        $users = $this->challenge->users()->pluck('users.id')->toArray();

        if (in_array(auth()->id(), $users)) {
            $this->challenge->is_active = true;
        } else {
            $this->challenge->is_active = false;
        }
    }

    public function render()
    {
        return view('livewire.challenge-detail');
    }
}
