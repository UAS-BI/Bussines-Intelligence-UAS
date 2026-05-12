@extends('layouts.app')

@section('title', 'Dashboard - Paris Housing BI')

@section('content')

<div class="dashboard-header">
    <div>
        <h1>Paris Housing Overview</h1>
    </div>
</div>

<div class="kpi-grid">

    <div class="kpi-card">
        <div class="kpi-icon blue">
            <i class="bi bi-houses-fill"></i>
        </div>
        <div>
            <span>Total Properties</span>
            <h3 class="counter" data-target="{{ $totalProperties ?? 0 }}">
                0
            </h3>
            <small>Total data warehouse records</small>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon green">
            <i class="bi bi-currency-euro"></i>
        </div>
        <div>
            <span>Average Price</span>
            <h3>
                €<span class="counter" data-target="{{ round($averagePrice ?? 0) }}">0</span>
            </h3>
            <small>Mean property price</small>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon purple">
            <i class="bi bi-aspect-ratio-fill"></i>
        </div>
        <div>
            <span>Average Size</span>
            <h3>
                <span class="counter" data-target="{{ round($averageSize ?? 0) }}">0</span> sqm
            </h3>
            <small>Mean living area</small>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon blue">
            <i class="bi bi-door-open-fill"></i>
        </div>
        <div>
            <span>Average Rooms</span>
            <h3>
                <span class="counter" data-target="{{ round($averageRooms ?? 0, 1) }}">0</span>
            </h3>
            <small>Mean room count</small>
        </div>
    </div>

</div>

    <div class="analytics-grid">

        <div class="analytics-card analytics-large">
            <div class="analytics-header">
                <div>
                    <h5>Average Housing Prices by Paris District</h5>
                </div>

            </div>

            <div class="chart-box">
                <canvas id="avgPriceArrondissementChart"></canvas>
            </div>
        </div>

        <div class="analytics-side">

            <div class="analytics-card">
                <div class="analytics-header">
                    <div>
                        <h5>Property Type Distribution</h5>
                    </div>
                </div>

                <div class="mini-chart-box">
                    <canvas id="propertyTypeChart"></canvas>
                </div>
            </div>

            <div class="analytics-card">
                <div class="analytics-header">
                    <div>
                        <h5>Property Condition</h5>
                    </div>
                </div>

                <div class="condition-list">
                    @foreach($conditionDistribution as $condition)
                        @php
                            $percentage = ($condition->total / $totalProperties) * 100;
                        @endphp

                        <div class="condition-item">
                            <div class="condition-top">
                                <span>{{ $condition->property_condition }}</span>
                                <strong>{{ number_format($percentage, 0) }}%</strong>
                            </div>

                            <div class="condition-progress">
                                <div
                                    class="condition-progress-bar"
                                    style="width: {{ $percentage }}%">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

    </div>

    <div class="price-category-section">

        <div class="section-heading">
            <div>
                <h4>Property Price Segments</h4>
            </div>
        </div>

        <div class="price-category-grid">

            @foreach($priceCategoryDistribution as $category)
                @php
                    $percentage = ($category->total / $totalProperties) * 100;
                @endphp

                <div class="price-category-card">

                <div class="price-category-top">
                    <div class="price-category-info">
                        <span class="badge-label">
                            {{ $category->Price_Category }}
                        </span>
                        <h3>{{ number_format($category->total) }}</h3>
                        <p>
                            {{ number_format($percentage, 0) }}% of total properties
                        </p>
                    </div>
                    <div class="category-icon">
                        <i class="bi bi-gem"></i>
                    </div>
                </div>

                    <div class="price-category-footer">
                        <div>
                            <small>Average Price</small>
                            <strong>
                                €{{ number_format($category->avg_price, 0, ',', '.') }}
                            </strong>
                        </div>
                        <div class="category-insight">
                            @if($category->Price_Category == 'Luxury')
                                Prime investment cluster
                            @elseif($category->Price_Category == 'High')
                                Strong market demand
                            @else
                                Affordable housing segment
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const avgPriceLabels = @json($avgPriceByArrondissement->pluck('arrondissement'));
    const avgPriceValues = @json($avgPriceByArrondissement->pluck('avg_price'));

    const avgPriceCanvas = document.getElementById('avgPriceArrondissementChart');

    if (avgPriceCanvas) {
        const avgPriceCtx = avgPriceCanvas.getContext('2d');
        
        const avgPriceChart = new Chart(avgPriceCtx, {
            type: 'bar',
            data: {
                labels: avgPriceLabels,
                datasets: [{
                    label: 'Average Price (€)',
                    data: avgPriceValues.map(() => 1300000),
                    borderRadius: 16,
                    borderSkipped: false,
                    backgroundColor: '#2563EB',
                    hoverBackgroundColor: '#4F46E5',
                    hoverBorderRadius: 16,
                    barThickness: 20,
                    maxBarThickness: 24,
                }]
            },
            options: {
                layout: {
                    padding: {
                        top: 4
                    }
                },
                responsive: true,
                maintainAspectRatio: false,
                onHover: (event, chartElement) => {
                    event.native.target.style.cursor =
                        chartElement[0] ? 'pointer' : 'default';
                },
                animation: {
                    duration: 1200,
                    easing: 'easeOutQuart'
                },

                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15,23,42,0.94)',
                        titleColor: '#FFFFFF',
                        bodyColor: '#CBD5E1',
                        borderColor: '#334155',
                        borderWidth: 1,
                        padding: 14,
                        cornerRadius: 14,
                        displayColors: false,
                        caretSize: 6,
                        callbacks: {
                            title: function(context) {
                                return 'Paris District ' + context[0].label;
                            },
                            label: function(context) {
                                return 'Average Price: €' +
                                    Number(context.raw).toLocaleString();
                            }
                        }
                    }
                },

                scales: {
                    y: {
                        min: 1300000,
                        max: 1750000,
                        border: {
                            display: false
                        },
                        ticks: {
                            color: '#64748B',
                            font: {
                                size: 12
                            },
                            callback: function(value) {
                                return '€' + value.toLocaleString();
                            }
                        },

                        grid: {
                            color: '#E2E8F0',
                            drawTicks: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#64748B',
                            font: {
                                size: 12,
                                weight: '600'
                            }
                        }
                    }
                }
            }
        });
        setTimeout(() => {
            avgPriceChart.data.datasets[0].data = avgPriceValues;
            avgPriceChart.update();
        }, 250);
    }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const propertyTypeLabels = @json(
        $propertyTypeDistribution->pluck('property_type')
    );
    const propertyTypeValues = @json(
        $propertyTypeDistribution->pluck('total')
    );
    const propertyTypeCanvas =
        document.getElementById('propertyTypeChart');
    if (!propertyTypeCanvas) return;
    const propertyTypeCtx =
        propertyTypeCanvas.getContext('2d');
    new Chart(propertyTypeCtx, {
        type: 'doughnut',
        data: {
            labels: propertyTypeLabels,
            datasets: [{
                data: propertyTypeValues,
                backgroundColor: [
                    '#2563EB',
                    '#7C3AED',
                    '#10B981',
                    '#F59E0B',
                    '#EC4899'
                ],
                borderWidth: 0,
                hoverOffset: 16,
                spacing: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,

            interaction: {
                mode: 'nearest',
                intersect: true
            },

            cutout: '72%',

            onHover: (event, chartElement) => {
                event.native.target.style.cursor =
                    chartElement.length ? 'pointer' : 'default';
            },

            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 18
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(15,23,42,0.94)',
                    titleColor: '#FFFFFF',
                    bodyColor: '#CBD5E1',
                    borderColor: '#334155',
                    borderWidth: 1,
                    padding: 14,
                    cornerRadius: 14,
                    displayColors: false
                }
            },

            animation: {
                animateRotate: true,
                animateScale: true,
                duration: 1400,
                easing: 'easeOutQuart'
            }
        }
    });

});
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const counters = document.querySelectorAll('.counter');

        counters.forEach(counter => {
            const target = parseFloat(counter.dataset.target);
            const duration = 1200;
            const startTime = performance.now();

            const formatNumber = (value) => {
                if (target % 1 !== 0) {
                    return value.toFixed(1);
                }

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

@endsection