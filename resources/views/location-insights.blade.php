@extends('layouts.app')

@section('title', 'Location Insights')
@section('page-title', 'Location Insights')
@push('styles')
    @vite('resources/css/location-insights.css')
@endpush

@section('content')
<div class="dashboard-page">

    <section class="section-header">
        <h2>Paris Housing Market Overview</h2>
    </section>

    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon blue">
                <i class="bi bi-buildings"></i>
            </div>
            <div>
                <span>Premium District</span>
                <h3>District {{ $mostExpensiveArrondissement->arrondissement }}</h3>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon purple">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div>
                <span>Highest Supply</span>
                <h3>District {{ $mostAvailableArrondissement->arrondissement }}</h3>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon blue">
                <i class="bi bi-house-check"></i>
            </div>
            <div>
                <span>Highest Avg. Price</span>
                <h3>
                    €<span class="counter" data-target="{{ round($mostExpensiveArrondissement->avg_price) }}">0</span>
                </h3>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon purple">
                <i class="bi bi-graph-up-arrow"></i>
            </div>
            <div>
            <span>Market Avg. Price</span>
                <h3>
                    €<span class="counter" data-target="{{ round($marketAveragePrice) }}">0</span>
                </h3>
            </div>
        </div>
    </div>

    <div class="analytics-grid location-grid">
        <div class="analytics-card wide">
            <div class="card-header">
                <div>
                    <h3>Highest Average Prices</h3>
                </div>
            </div>
            <canvas id="arrondissementPriceChart"></canvas>
        </div>

        <div class="analytics-card">
            <div class="card-header">
                <div>
                    <h3>Highest Property Supply</h3>
                </div>
            </div>
            <canvas id="topSupplyChart"></canvas>
        </div>
    </div>

    <div class="table-card">
        <div class="card-header">
            <div>
                <h3>District Market Overview</h3>
            </div>
        </div>

        <div class="table-responsive">
            <table class="location-table">
                <thead>
                    <tr>
                        <th>District</th>
                        <th>Total Properties</th>
                        <th>Avg. Price</th>
                        <th>Avg. Size</th>
                        <th>Highest Price</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($locationStats as $arrondissement)
                        <tr>
                            <td>District {{ $arrondissement->arrondissement }}</td>
                            <td class="text-right">{{ number_format($arrondissement->total_properties) }}</td>
                            <td class="text-right">€{{ number_format($arrondissement->avg_price, 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($arrondissement->avg_size, 1) }} m²</td>
                            <td class="text-right">€{{ number_format($arrondissement->highest_price, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-state">No location data available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
const arrondissementLabels = @json(
    $locationStats
        ->take(8)
        ->pluck('arrondissement')
        ->map(fn ($item) => 'D' . $item)
);

const averagePrices = @json(
    $locationStats
        ->take(8)
        ->pluck('avg_price')
        ->map(fn ($value) => round($value))
);

const arrondissementPriceChart = new Chart(document.getElementById('arrondissementPriceChart'), {
    type: 'bar',
    data: {
        labels: arrondissementLabels,
        datasets: [{
            label: 'Average Price',
            data: averagePrices.map(() => 1500000),
            backgroundColor: '#0EA5E9',
            hoverBackgroundColor: '#0284C7',
            borderRadius: 12,
            borderSkipped: false,
            maxBarThickness: 42
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            mode: 'nearest',
            intersect: true
        },
        animation: {
            duration: 1200,
            easing: 'easeOutQuart'
        },
        onHover: (event, chartElement) => {
            event.native.target.style.cursor =
                chartElement.length ? 'pointer' : 'default';
        },
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(15,23,42,0.94)',
                titleColor: '#FFFFFF',
                bodyColor: '#CBD5E1',
                borderColor: '#334155',
                borderWidth: 1,
                padding: 14,
                cornerRadius: 14,
                displayColors: false,
                callbacks: {
                    title: function(context) {
                        return 'District ' + context[0].label.replace('D', '');
                    },
                    label: function(context) {
                        return 'Average Price: €' + averagePrices[context.dataIndex].toLocaleString();
                    }
                }
            }
        },
        scales: {
            x: {
                grid: { display: false }
            },
            y: {
                beginAtZero: false,
                suggestedMin: 1500000,
                ticks: {
                    callback: function(value) {
                        return '€' + (value / 1000000).toFixed(2) + 'M';
                    }
                },
                grid: {
                    color: 'rgba(148, 163, 184, 0.12)'
                }
            }
        }
    }
});

const supplyLabels = @json(
    $topSupplyArrondissements
        ->pluck('arrondissement')
        ->map(fn ($item) => 'District ' . $item)
);

const supplyData = @json($topSupplyArrondissements->pluck('total_properties'));

const topSupplyChart = new Chart(document.getElementById('topSupplyChart'), {
    type: 'bar',
    data: {
        labels: supplyLabels,
        datasets: [{
            label: 'Total Properties',
            data: supplyData.map(() => 0),
            backgroundColor: '#7C3AED',
            hoverBackgroundColor: '#6D28D9',
            borderRadius: 12,
            borderSkipped: false,
            maxBarThickness: 34
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            mode: 'nearest',
            intersect: true
        },
        animation: {
            duration: 1200,
            easing: 'easeOutQuart'
        },
        onHover: (event, chartElement) => {
            event.native.target.style.cursor =
                chartElement.length ? 'pointer' : 'default';
        },
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(15,23,42,0.94)',
                titleColor: '#FFFFFF',
                bodyColor: '#CBD5E1',
                borderColor: '#334155',
                borderWidth: 1,
                padding: 14,
                cornerRadius: 14,
                displayColors: false,
                callbacks: {
                    label: function(context) {
                        return 'Property Listings: ' + supplyData[context.dataIndex].toLocaleString();
                    }
                }
            }
        },
        scales: {
            x: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(148, 163, 184, 0.12)'
                }
            },
            y: {
                grid: { display: false }
            }
        }
    }
});

setTimeout(() => {
    arrondissementPriceChart.data.datasets[0].data = averagePrices;
    arrondissementPriceChart.update();

    topSupplyChart.data.datasets[0].data = supplyData;
    topSupplyChart.update();
}, 250);
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const counters = document.querySelectorAll('.counter');

    counters.forEach(counter => {
        const target = parseFloat(counter.dataset.target);
        const duration = 1200;
        const startTime = performance.now();

        const formatNumber = (value) => {
            return Math.floor(value).toLocaleString('en-US');
        };

        const animate = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const easedProgress = 1 - Math.pow(1 - progress, 4);
            const currentValue = target * easedProgress;

            counter.textContent = formatNumber(currentValue);

            if (progress < 1) {
                requestAnimationFrame(animate);
            } else {
                counter.textContent = formatNumber(target);
            }
        };

        requestAnimationFrame(animate);
    });
});
</script>
@endpush