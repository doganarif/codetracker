<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MyEvents extends Component
{
    public $events = [];

    public function mount()
    {
        $this->events = Auth::user()->events;
    }

    public function render()
    {
        return view('livewire.my-events');
    }

    public function refreshEvents()
    {
        Auth::user()->fetchGithubData();
        $this->events = Auth::user()->events;
    }
}
