<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GithubEventChart extends Component
{
    public array $eventTypeChartData = [];

    public array $activityOverTimeChartData = [];

    public array $topRepositoriesChartData = [];

    public string $firstEventDate = '';

    public int $uniqueRepos = 0;

    public array $eventTypes = ['PushEvent', 'IssuesEvent', 'PullRequestEvent'];

    public array $selectedEventTypes = ['PushEvent', 'IssuesEvent', 'PullRequestEvent'];

    public int $dateRange = 30;

    public int $totalEvents = 0;

    public float $averageEventsPerDay = 0;

    public ?string $mostActiveDay = null;

    public ?string $mostActiveRepo = null;

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $startDate = Carbon::now()->subDays($this->dateRange);

        $events = $user->events()
            ->whereIn('type', $this->selectedEventTypes)
            ->where('event_date', '>=', $startDate)
            ->get();

        $this->totalEvents = $events->count();
        $this->averageEventsPerDay = $this->totalEvents > 0
            ? round($this->totalEvents / $this->dateRange, 2)
            : 0;

        // Get the first event date
        $firstEvent = $events->sortBy('event_date')->first();
        $this->firstEventDate = $firstEvent ? $firstEvent->event_date->format('Y-m-d') : 'No Events';

        // Get unique repositories
        $this->uniqueRepos = $events->pluck('repo_name')->unique()->count();

        // Populate charts and other stats
        $this->generateEventTypeChartData($events);
        $this->generateActivityOverTimeChartData($events);
        $this->generateTopRepositoriesChartData($events);
        $this->mostActiveDay = $this->calculateMostActiveDay($events);
        $this->mostActiveRepo = $this->calculateMostActiveRepo($events);
    }

    private function generateEventTypeChartData($events): void
    {
        $eventCounts = $this->groupAndCount($events, 'type');

        $this->eventTypeChartData = [
            'labels' => $eventCounts->keys()->toArray(),
            'datasets' => [
                [
                    'data' => $eventCounts->values()->toArray(),
                    'backgroundColor' => ['#FF6384', '#36A2EB', '#FFCE56'],
                ],
            ],
        ];
    }

    private function generateActivityOverTimeChartData($events): void
    {
        $dailyActivity = $this->groupAndCount($events, fn ($event) => $event->event_date->format('Y-m-d'));

        $this->activityOverTimeChartData = [
            'labels' => $dailyActivity->keys()->toArray(),
            'datasets' => [
                [
                    'label' => 'Daily Activity',
                    'data' => $dailyActivity->values()->toArray(),
                    'borderColor' => '#4BC0C0',
                    'fill' => false,
                ],
            ],
        ];
    }

    private function generateTopRepositoriesChartData($events): void
    {
        $topRepos = $this->groupAndCount($events, 'repo_name')
            ->sortDesc()
            ->take(5);

        $this->topRepositoriesChartData = [
            'labels' => $topRepos->keys()->toArray(),
            'datasets' => [
                [
                    'label' => 'Events per Repository',
                    'data' => $topRepos->values()->toArray(),
                    'backgroundColor' => ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'],
                ],
            ],
        ];
    }

    private function calculateMostActiveDay($events): ?string
    {
        return $this->groupAndCount($events, fn ($event) => $event->event_date->format('Y-m-d'))
            ->sort()
            ->keys()
            ->last();
    }

    private function calculateMostActiveRepo($events): ?string
    {
        return $this->groupAndCount($events, 'repo_name')
            ->sort()
            ->keys()
            ->last();
    }

    private function groupAndCount($events, $groupBy): \Illuminate\Support\Collection
    {
        return $events->groupBy($groupBy)
            ->map(fn ($group) => $group->count());
    }

    public function updatedSelectedEventTypes(): void
    {
        $this->loadData();
    }

    public function updatedDateRange(): void
    {
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.github-event-chart');
    }
}
