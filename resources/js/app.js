import './bootstrap';

import Chart from 'chart.js/auto';

// GitHub Insights Dashboard Charts
let eventTypeChart, activityOverTimeChart, topRepositoriesChart;

document.addEventListener('livewire:load', function() {
    if (document.getElementById('eventTypeChart')) {
        createCharts();
    }

    Livewire.on('dataUpdated', function(data) {
        updateCharts(data);
    });
});

function createCharts() {
    const eventTypeCtx = document.getElementById('eventTypeChart').getContext('2d');
    eventTypeChart = new Chart(eventTypeCtx, {
        type: 'pie',
        data: window.Livewire.find(document.getElementById('github-insights-dashboard').getAttribute('wire:id')).get('eventTypeChart'),
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Event Types Distribution'
                }
            }
        }
    });

    const activityOverTimeCtx = document.getElementById('activityOverTimeChart').getContext('2d');
    activityOverTimeChart = new Chart(activityOverTimeCtx, {
        type: 'line',
        data: window.Livewire.find(document.getElementById('github-insights-dashboard').getAttribute('wire:id')).get('activityOverTimeChart'),
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Events'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Date'
                    }
                }
            }
        }
    });

    const topRepositoriesCtx = document.getElementById('topRepositoriesChart').getContext('2d');
    topRepositoriesChart = new Chart(topRepositoriesCtx, {
        type: 'bar',
        data: window.Livewire.find(document.getElementById('github-insights-dashboard').getAttribute('wire:id')).get('topRepositoriesChart'),
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Events'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Repository'
                    }
                }
            }
        }
    });
}

function updateCharts(data) {
    if (eventTypeChart) {
        eventTypeChart.data = data.eventTypeChart;
        eventTypeChart.update();
    }

    if (activityOverTimeChart) {
        activityOverTimeChart.data = data.activityOverTimeChart;
        activityOverTimeChart.update();
    }

    if (topRepositoriesChart) {
        topRepositoriesChart.data = data.topRepositoriesChart;
        topRepositoriesChart.update();
    }
}
