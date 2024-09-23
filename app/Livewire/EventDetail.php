<?php

namespace App\Livewire;

use App\Models\UserEvent;
use App\Services\AzureOpenAIService;
use Illuminate\Support\Str;
use Livewire\Component;

class EventDetail extends Component
{
    public UserEvent $event;

    public ?string $aiAdvice = null;

    public function mount($eventId)
    {
        $this->event = UserEvent::findOrFail($eventId);
    }

    public function getAIAdvice()
    {
        // Call the AI service and pass the event details
        $service = new AzureOpenAIService;
        $response = $service->processEventInput($this->event->type, $this->event->title, $this->event->description);

        // Update the AI advice with the response from the service and convert it to markdown
        $this->aiAdvice = Str::markdown($response['title'] ?? 'No advice received.');
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
