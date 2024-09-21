<?php

namespace App\Livewire;

use App\Models\UserEvent;
use Illuminate\Support\Str;
use Livewire\Component;

class EventDetail extends Component
{
    public UserEvent $event;

    public function mount($eventId)
    {
        $this->event = UserEvent::findOrFail($eventId);
    }

    public function render()
    {
        return view('livewire.event-detail', [
            'parsedDescription' => $this->event->description
                ? Str::markdown($this->event->description)
                : null,
        ]);
    }
}
