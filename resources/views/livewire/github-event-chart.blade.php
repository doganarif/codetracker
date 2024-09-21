<div id="github-insights-dashboard" class="p-6 bg-white rounded-lg shadow-lg" x-data="{
    showEventTypeDropdown: false,
    eventTypeChart: null,
    activityOverTimeChart: null,
    topRepositoriesChart: null,
    eventTypeData: @entangle('eventTypeChartData'),
    activityOverTimeData: @entangle('activityOverTimeChartData'),
    topRepositoriesData: @entangle('topRepositoriesChartData'),
    initCharts() {
        this.createEventTypeChart();
        this.createActivityOverTimeChart();
        this.createTopRepositoriesChart();
    },
    createEventTypeChart() {
        const ctx = document.getElementById('eventTypeChart').getContext('2d');
        this.eventTypeChart = new Chart(ctx, {
            type: 'pie',
            data: this.eventTypeData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                },
                maintainAspectRatio: false,
            },
        });
    },
    createActivityOverTimeChart() {
        const ctx = document.getElementById('activityOverTimeChart').getContext('2d');
        this.activityOverTimeChart = new Chart(ctx, {
            type: 'line',
            data: this.activityOverTimeData,
            options: {
                responsive: true,
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: 'day',
                        },
                    },
                    y: {
                        beginAtZero: true,
                    },
                },
                maintainAspectRatio: false,
            },
        });
    },
    createTopRepositoriesChart() {
        const ctx = document.getElementById('topRepositoriesChart').getContext('2d');
        this.topRepositoriesChart = new Chart(ctx, {
            type: 'bar',
            data: this.topRepositoriesData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false,
                    },
                },
                scales: {
                    x: {
                        beginAtZero: true,
                    },
                    y: {
                        beginAtZero: true,
                    },
                },
                maintainAspectRatio: false,
            },
        });
    },
    updateCharts() {
        if (this.eventTypeChart) {
            this.eventTypeChart.data = this.eventTypeData;
            this.eventTypeChart.update();
        }
        if (this.activityOverTimeChart) {
            this.activityOverTimeChart.data = this.activityOverTimeData;
            this.activityOverTimeChart.update();
        }
        if (this.topRepositoriesChart) {
            this.topRepositoriesChart.data = this.topRepositoriesData;
            this.topRepositoriesChart.update();
        }
    }
}" x-init="initCharts(); $watch('eventTypeData', () => updateCharts()); $watch('activityOverTimeData', () => updateCharts()); $watch('topRepositoriesData', () => updateCharts())">

    @auth
        <h2 class="text-2xl font-bold mb-6 text-center">Your GitHub Insights</h2>

        <!-- Statistics and Insights Section -->
        @if($totalEvents > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-4 mb-6">
                <div class="bg-blue-100 p-4 rounded-lg text-center">
                    <h3 class="text-lg font-semibold">Total Events</h3>
                    <p class="text-3xl font-bold">{{ $totalEvents }}</p>
                </div>
                <div class="bg-green-100 p-4 rounded-lg text-center">
                    <h3 class="text-lg font-semibold">Avg Events/Day</h3>
                    <p class="text-3xl font-bold">{{ $averageEventsPerDay }}</p>
                </div>
                <div class="bg-yellow-100 p-4 rounded-lg text-center">
                    <h3 class="text-lg font-semibold">First Event Date</h3>
                    <p class="text-2xl font-bold">{{ $firstEventDate }}</p>
                </div>
                <div class="bg-yellow-100 p-4 rounded-lg text-center">
                    <h3 class="text-lg font-semibold">Most Active Day</h3>
                    <p class="text-3xl font-bold">{{ $mostActiveDay }}</p>
                </div>
                <div class="bg-purple-100 p-4 rounded-lg text-center">
                    <h3 class="text-lg font-semibold">Most Active Repo</h3>
                    <p class="text-xl font-bold">{{ $mostActiveRepo }}</p>
                </div>
                <div class="bg-indigo-100 p-4 rounded-lg text-center">
                    <h3 class="text-lg font-semibold">Total Unique Repositories</h3>
                    <p class="text-3xl font-bold">{{ $uniqueRepos }}</p>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="h-64">
                    <h3 class="text-xl font-semibold mb-2 text-center">Event Types Distribution</h3>
                    <canvas id="eventTypeChart"></canvas>
                </div>
                <div class="h-64">
                    <h3 class="text-xl font-semibold mb-2 text-center">Activity Over Time</h3>
                    <canvas id="activityOverTimeChart"></canvas>
                </div>
            </div>

            <div class="h-64 mb-6 mt-12">
                <h3 class="text-xl font-semibold mb-2 text-center">Top 5 Active Repositories</h3>
                <canvas id="topRepositoriesChart"></canvas>
            </div>
        @else
            <div class="text-center py-8">
                <p class="text-xl text-gray-600">No GitHub events found for your account.</p>
                <p class="mt-2 text-gray-500">Make sure you've connected your GitHub account and have some recent activity.</p>
            </div>
        @endif
    @else
        <div class="text-center py-8">
            <p class="text-xl text-gray-600">Please log in to view your GitHub insights.</p>
            <a href="{{ route('login') }}" class="mt-4 inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                Log In
            </a>
        </div>
    @endauth
</div>
