@extends('layouts.app')

@section('title', 'Price Analysis')
@section('page-title', 'Price Analysis')

@push('styles')
    @vite('resources/css/price-analysis.css')
@endpush

@section('content')
<div class="price-analysis-page">

    <section class="price-hero">
        <h2>Property Valuation</h2>
    </section>

    <section class="price-kpi-grid">

        <div class="price-kpi-card">
            <div class="price-kpi-icon blue">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div>
                <span>Average Price</span>
                <h3>
                    €<span class="counter" data-target="{{ round($averagePrice ?? 0) }}">0</span>
                </h3>
            </div>
        </div>

        <div class="price-kpi-card">
            <div class="price-kpi-icon purple">
                <i class="bi bi-gem"></i>
            </div>
            <div>
                <span>Highest Price</span>
                <h3>
                    €<span class="counter" data-target="{{ round($highestPrice ?? 0) }}">0</span>
                </h3>
            </div>
        </div>

        <div class="price-kpi-card">
            <div class="price-kpi-icon green">
                <i class="bi bi-stars"></i>
            </div>
            <div>
                <span>Luxury Properties</span>
                <h3 class="counter" data-target="{{ $luxuryProperties ?? 0 }}">0</h3>
            </div>
        </div>

        <div class="price-kpi-card">
            <div class="price-kpi-icon orange">
                <i class="bi bi-rulers"></i>
            </div>
            <div>
                <span>Avg. Price per sqm</span>
                <h3>
                    €<span class="counter"
                        data-target="{{ round($avgPricePerSqm ?? 0) }}">
                        0
                    </span>
                </h3>
            </div>
        </div>

    </section>

    <section class="price-chart-grid">

        <div class="price-card">
            <div class="price-card-header">
                <h3>Average Price by Property Type</h3>
            </div>

            <div class="price-chart-box chart-grow">
                <canvas id="avgPriceTypeChart"></canvas>
            </div>
        </div>

        <div class="price-card">
            <div class="price-card-header">
                <h3>Price Category Distribution</h3>
            </div>

            <div class="price-chart-box chart-grow">
                <canvas id="priceCategoryChart"></canvas>
            </div>
        </div>

    </section>

    <section class="price-card">
        <div class="price-card-header">
            <h3>Price Category Overview</h3>
        </div>

        <div class="table-responsive">
            <table class="price-table">
                <thead>
                    <tr>
                        <th>Price Category</th>
                        <th>Total Properties</th>
                        <th>Avg. Price</th>
                        <th>Min Price</th>
                        <th>Max Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($priceCategoryStats as $category)
                        <tr>
                            <td>
                                <strong>{{ $category->Price_Category }}</strong>
                            </td>
                            <td>{{ number_format($category->total_properties) }}</td>
                            <td>€{{ number_format($category->avg_price, 0, ',', '.') }}</td>
                            <td>€{{ number_format($category->min_price, 0, ',', '.') }}</td>
                            <td>€{{ number_format($category->max_price, 0, ',', '.') }}</td>
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
    const propertyTypeLabels = @json($avgPriceByPropertyType->pluck('property_type'));
    const propertyTypePrices = @json($avgPriceByPropertyType->pluck('avg_price')->map(fn ($value) => round($value)));

    const categoryLabels = @json($priceCategoryStats->pluck('Price_Category'));
    const categoryTotals = @json($priceCategoryStats->pluck('total_properties'));

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

    const avgPriceCanvas = document.getElementById('avgPriceTypeChart');

    if (avgPriceCanvas) {
        new Chart(avgPriceCanvas, {
            type: 'bar',
            data: {
                labels: propertyTypeLabels,
                datasets: [{
                    label: 'Average Price',
                    data: propertyTypePrices,
                    backgroundColor: '#4F46E5',
                    hoverBackgroundColor: '#4338CA',
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

    const categoryCanvas = document.getElementById('priceCategoryChart');

    if (categoryCanvas) {
        new Chart(categoryCanvas, {
            type: 'doughnut',
            data: {
                labels: categoryLabels,
                datasets: [{
                    data: categoryTotals,
                    backgroundColor: [
                        '#F59E0B',
                        '#915dea',
                        '#4F46E5'
                    ],
                    hoverOffset: 10,
                    borderColor: '#FFFFFF',
                    borderWidth: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
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