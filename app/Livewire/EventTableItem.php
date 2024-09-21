<?php

namespace App\Livewire;

use Livewire\Component;

class EventTableItem extends Component
{
    public $eventId;

    public string $eventType = '';

    public string $eventDate = '';

    public string $eventTitle = '';

    public ?string $eventDescription = null;

    public string $repoName = '';

    public function mount($eventType, $eventDate, $eventTitle, $eventDescription, $repoName, $eventId)
    {
        $this->eventType = $eventType;
        $this->eventDate = $eventDate;
        $this->eventTitle = $eventTitle;
        $this->eventDescription = $eventDescription;
        $this->repoName = $repoName;
        $this->eventId = $eventId;
    }

    public function viewDetails()
    {
        $this->emit('openEventDetails', $this->eventType, $this->eventDate, $this->eventTitle, $this->eventDescription, $this->repoName);
    }

    public function render()
    {
        return view('livewire.event-table-item');
    }
}
