@extends('layouts.app')

@section('title', 'Property Types')
@section('page-title', 'Property Types')

@push('styles')
    @vite('resources/css/property-types.css')
@endpush

@section('content')
<div class="property-types-page">

    <section class="property-hero">
        <h2>Property Type Analytics</h2>
    </section>

    <section class="property-kpi-grid">

        <div class="property-kpi-card">
            <div class="property-kpi-icon rose">
                <i class="bi bi-pie-chart-fill"></i>
            </div>
            <div>
                <span>Market Leader</span>
                <h3>{{ $dominantPropertyType->property_type ?? '-' }}</h3>
            </div>
        </div>

        <div class="property-kpi-card">
            <div class="property-kpi-icon coral">
                <i class="bi bi-gem"></i>
            </div>
            <div>
                <span>Premium Type</span>
                <h3>{{ $premiumPropertyType->property_type ?? '-' }}</h3>
            </div>
        </div>

        <div class="property-kpi-card">
            <div class="property-kpi-icon orange">
                <i class="bi bi-arrows-fullscreen"></i>
            </div>
            <div>
                <span>Largest Avg. Size</span>
                <h3>{{ $largestPropertyType->property_type ?? '-' }}</h3>
            </div>
        </div>

        <div class="property-kpi-card">
            <div class="property-kpi-icon peach">
                <i class="bi bi-grid-3x3-gap-fill"></i>
            </div>
            <div>
                <span>Property Categories</span>
                <h3 class="counter" data-target="{{ $totalPropertyTypes }}">0</h3>
            </div>
        </div>

    </section>

    <section class="property-chart-grid">

        <div class="property-card">
            <div class="property-card-header">
                <div>
                    <h3>Average Property Size</h3>
                </div>
            </div>

            <div class="property-chart-box chart-grow">
                <canvas id="avgPriceTypeChart"></canvas>
            </div>
        </div>

        <div class="property-card">
            <div class="property-card-header">
                <div>
                    <h3>Property Type Distribution</h3>
                </div>
            </div>

            <div class="property-chart-box chart-grow">
                <canvas id="propertySupplyChart"></canvas>
            </div>
        </div>

    </section>

    <section class="property-card">
        <div class="property-card-header">
            <div>
                <h3>Property Type Overview</h3>
            </div>
        </div>

        <div class="table-responsive">
            <table class="property-type-table">
                <thead>
                    <tr>
                        <th>Property Type</th>
                        <th>Total Properties</th>
                        <th>Avg. Price</th>
                        <th>Avg. Size</th>
                        <th>Avg. Rooms</th>
                        <th>Highest Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($propertyTypeStats as $type)
                        <tr>
                            <td>
                                <strong>{{ $type->property_type }}</strong>
                            </td>
                            <td>{{ number_format($type->total_properties) }}</td>
                            <td>€{{ number_format($type->avg_price, 0, ',', '.') }}</td>
                            <td>{{ number_format($type->avg_size, 1) }} sqm</td>
                            <td>{{ number_format($type->avg_rooms, 1) }}</td>
                            <td>€{{ number_format($type->highest_price, 0, ',', '.') }}</td>
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
    const propertyLabels = @json($propertyTypeStats->pluck('property_type'));
    const avgSizeData = @json($propertyTypeStats->pluck('avg_size')->map(fn ($value) => round($value, 1)));
    const supplyData = @json($propertyTypeStats->pluck('total_properties'));

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
                labels: propertyLabels,
                datasets: [{
                    label: 'Average Size',
                    data: avgSizeData,
                    backgroundColor: '#F97316',
                    hoverBackgroundColor: '#EA580C',
                    borderRadius: 14,
                    borderSkipped: false
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
                        display: false
                    },
                    tooltip: {
                        ...tooltipOptions,
                        callbacks: {
                            label: function(context) {
                                return Number(context.raw).toLocaleString('en-US') + ' sqm';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#64748B'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#E2E8F0'
                        },
                        ticks: {
                            color: '#64748B',
                                callback: function(value) {
                                    return value + ' sqm';
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

    const supplyCanvas = document.getElementById('propertySupplyChart');

    if (supplyCanvas) {
        new Chart(supplyCanvas, {
            type: 'doughnut',
            data: {
                labels: propertyLabels,
                datasets: [{
                    data: supplyData,
                    backgroundColor: [
                        '#F97316',
                        '#FB7185',
                        '#FDBA74',
                        '#F43F5E'
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