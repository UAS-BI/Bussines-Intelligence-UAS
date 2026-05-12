@extends('layouts.app')

@section('title', 'Property Condition')
@section('page-title', 'Property Condition')

@push('styles')
    @vite('resources/css/property-condition.css')
@endpush

@section('content')
<div class="condition-page">

    <section class="condition-hero">
        <h2>Condition Analytics</h2>
    </section>

    <section class="condition-kpi-grid">

        <div class="condition-kpi-card">
            <div class="condition-kpi-icon emerald">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div>
                <span>Dominant Condition</span>
                <h3>{{ $mostCommonCondition->property_condition ?? '-' }}</h3>
            </div>
        </div>

        <div class="condition-kpi-card">
            <div class="condition-kpi-icon teal">
                <i class="bi bi-gem"></i>
            </div>
            <div>
                <span>Highest Value Condition</span>
                <h3>{{ $premiumCondition->property_condition ?? '-' }}</h3>
            </div>
        </div>

        <div class="condition-kpi-card">
            <div class="condition-kpi-icon amber">
                <i class="bi bi-tools"></i>
            </div>
            <div>
                <span>Needs Renovation</span>
                <h3 class="counter" data-target="{{ $needsAttention ?? 0 }}">0</h3>
            </div>
        </div>

        <div class="condition-kpi-card">
            <div class="condition-kpi-icon blue">
                <i class="bi bi-layers-fill"></i>
            </div>
            <div>
                <span>Condition Types</span>
                <h3 class="counter" data-target="{{ $totalConditionTypes ?? 0 }}">0</h3>
            </div>
        </div>

    </section>

    <section class="condition-chart-grid">

        <div class="condition-card">
            <div class="condition-card-header">
                <h3>Average Price by Condition</h3>
            </div>

            <div class="condition-chart-box chart-grow">
                <canvas id="conditionPriceChart"></canvas>
            </div>
        </div>

        <div class="condition-card">
            <div class="condition-card-header">
                <h3>Condition Distribution</h3>
            </div>

            <div class="condition-chart-box chart-grow">
                <canvas id="conditionDistributionChart"></canvas>
            </div>
        </div>

    </section>

    <section class="condition-card">
        <div class="condition-card-header">
            <h3>Condition Overview</h3>
        </div>

        <div class="table-responsive">
            <table class="condition-table">
                <thead>
                    <tr>
                        <th>Condition</th>
                        <th>Total Properties</th>
                        <th>Avg. Price</th>
                        <th>Avg. Size</th>
                        <th>Avg. Rooms</th>
                        <th>Highest Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($conditionStats as $condition)
                        <tr>
                            <td>
                                <strong>{{ $condition->property_condition }}</strong>
                            </td>
                            <td>{{ number_format($condition->total_properties) }}</td>
                            <td>€{{ number_format($condition->avg_price, 0, ',', '.') }}</td>
                            <td>{{ number_format($condition->avg_size, 1) }} sqm</td>
                            <td>{{ number_format($condition->avg_rooms, 1) }}</td>
                            <td>€{{ number_format($condition->highest_price, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const conditionLabels = @json($conditionStats->pluck('property_condition'));
    const conditionPrices = @json($conditionStats->pluck('avg_price')->map(fn ($value) => round($value)));
    const conditionTotals = @json($conditionStats->pluck('total_properties'));

    const tooltipOptions = {
        backgroundColor: '#0F172A',
        titleColor: '#FFFFFF',
        bodyColor: '#CBD5E1',
        borderColor: '#1E293B',
        borderWidth: 1,
        padding: 12,
        cornerRadius: 12,
        displayColors: false
    };

    const conditionPriceCanvas = document.getElementById('conditionPriceChart');

    if (conditionPriceCanvas) {
        new Chart(conditionPriceCanvas, {
            type: 'bar',
            data: {
                labels: conditionLabels,
                datasets: [{
                    label: 'Average Price',
                    data: conditionPrices,
                    backgroundColor: '#10B981',
                    hoverBackgroundColor: '#059669',
                    borderRadius: 14,
                    borderSkipped: false
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 1200,
                    easing: 'easeOutQuart'
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        ...tooltipOptions,
                        callbacks: {
                            label: function(context) {
                                return '€' + Number(context.raw).toLocaleString('de-DE');
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        suggestedMax: Math.max(...conditionPrices) * 1.12,
                        grid: {
                            color: '#E2E8F0'
                        },
                        ticks: {
                            color: '#64748B',
                            callback: function(value) {
                                return '€' + Number(value).toLocaleString('de-DE');
                            }
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#64748B'
                        }
                    }
                },
                onHover: function(event, elements) {
                    event.native.target.style.cursor = elements.length ? 'pointer' : 'default';
                }
            }
        });
    }

    const distributionCanvas = document.getElementById('conditionDistributionChart');

    if (distributionCanvas) {
        new Chart(distributionCanvas, {
            type: 'polarArea',
            data: {
                labels: conditionLabels,
                datasets: [{
                    data: conditionTotals,
                    backgroundColor: [
                        'rgba(16, 185, 129, 0.78)',
                        'rgba(20, 184, 166, 0.78)',
                        'rgba(245, 158, 11, 0.78)',
                        'rgba(37, 99, 235, 0.78)',
                        'rgba(124, 58, 237, 0.78)'
                    ],
                    borderColor: '#FFFFFF',
                    borderWidth: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 1200,
                    easing: 'easeOutQuart'
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#64748B',
                            usePointStyle: true,
                            padding: 18
                        }
                    },
                    tooltip: {
                        ...tooltipOptions,
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + Number(context.raw).toLocaleString('en-US') + ' properties';
                            }
                        }
                    }
                },
                scales: {
                    r: {
                        grid: {
                            color: 'rgba(148, 163, 184, 0.18)'
                        },
                        ticks: {
                            display: false
                        }
                    }
                },
                onHover: function(event, elements) {
                    event.native.target.style.cursor = elements.length ? 'pointer' : 'default';
                }
            }
        });
    }

    document.querySelectorAll('.counter').forEach(counter => {
        const target = Number(counter.dataset.target || 0);
        const duration = 1000;
        const startTime = performance.now();

        function animateCounter(currentTime) {
            const progress = Math.min((currentTime - startTime) / duration, 1);
            const currentValue = Math.floor(progress * target);

            counter.textContent = currentValue.toLocaleString('en-US');

            if (progress < 1) {
                requestAnimationFrame(animateCounter);
            }
        }

        requestAnimationFrame(animateCounter);
    });
});
</script>
@endpush