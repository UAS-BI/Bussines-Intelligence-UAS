@extends('layouts.app')

@section('title', 'Property Data')
@section('page-title', 'Property Data')

@push('styles')
    @vite('resources/css/property-data.css')
@endpush

@section('content')

<div class="dashboard-page">

    <section class="section-header">
        <h2>Property Data Management</h2>
    </section>

    <div class="table-card">

        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Property Listings</h3>

            <button class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#addModal">

                <i class="bi bi-plus-lg"></i>
                Add Property
            </button>
        </div>

        @if(session('success'))
            <div class="alert alert-success mb-3">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="table-responsive">

            <table class="location-table">

                <thead>
                    <tr>
                        <th>District</th>
                        <th>Type</th>
                        <th>Condition</th>
                        <th>Price</th>
                        <th>Size</th>
                        <th>Rooms</th>
                        <th>Category</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach ($properties as $property)

                        <tr>

                            <td>
                                District {{ $property->arrondissement }}
                            </td>

                            <td>
                                {{ $property->property_type }}
                            </td>

                            <td>
                                {{ $property->property_condition }}
                            </td>

                            <td>
                                €{{ number_format($property->Price_EUR, 0, ',', '.') }}
                            </td>

                            <td>
                                {{ number_format($property->Size_sqm, 1) }} sqm
                            </td>

                            <td>
                                {{ $property->Rooms }}
                            </td>

                            <td>
                                {{ $property->Price_Category }}
                            </td>

                            <td>
                                <div class="d-flex gap-2">

                                    <button class="btn btn-sm btn-warning"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal{{ $property->property_id }}">
                                        Edit
                                    </button>

                                    <button type="button"
                                        class="btn btn-sm btn-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteModal{{ $property->property_id }}">
                                        Delete
                                    </button>

                                </div>
                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

        <div class="mt-3">
            {{ $properties->links() }}
        </div>

    </div>

</div>

{{-- DELETE MODALS --}}
@foreach ($properties as $property)

<div class="modal fade"
    id="deleteModal{{ $property->property_id }}"
    tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form action="{{ route('property.data.delete', $property->property_id) }}"
                method="POST">

                @csrf
                @method('DELETE')

                <div class="modal-header">
                    <h5 class="modal-title">
                        Delete Property
                    </h5>

                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>
                </div>

                <div class="modal-body">

                    <p>
                        Are you sure you want to delete this property?
                    </p>

                    <div class="alert alert-light border">
                        <strong>
                            District {{ $property->arrondissement }}
                        </strong>

                        <br>

                        {{ $property->property_type }}
                        •
                        {{ $property->property_condition }}
                        •
                        €{{ number_format($property->Price_EUR, 0, ',', '.') }}
                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit"
                        class="btn btn-danger">
                        Delete Property
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>
@endforeach

@endsection

{{-- MODALS --}}
@push('modals')

{{-- EDIT MODALS --}}
@foreach ($properties as $property)

<div class="modal fade"
    id="editModal{{ $property->property_id }}"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <form action="{{ route('property.data.update', $property->property_id) }}"
                method="POST">

                @csrf
                @method('PUT')

                <div class="modal-header">

                    <h5 class="modal-title">
                        <i class="bi bi-pencil-square text-warning me-2"></i>
                        Edit Property
                    </h5>

                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <div class="row g-3">

                        <div class="col-md-6">

                            <label class="form-label">
                                District
                            </label>

                            <select name="location_id"
                                class="form-select"
                                required>

                                @foreach ($locations as $location)

                                    <option value="{{ $location->location_id }}"
                                        {{ $property->location_id == $location->location_id ? 'selected' : '' }}>

                                        District {{ $location->arrondissement }}

                                    </option>

                                @endforeach

                            </select>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">
                                Property Type
                            </label>

                            <select name="property_type_id"
                                class="form-select"
                                required>

                                @foreach ($propertyTypes as $type)

                                    <option value="{{ $type->property_type_id }}"
                                        {{ $property->property_type_id == $type->property_type_id ? 'selected' : '' }}>

                                        {{ $type->property_type }}

                                    </option>

                                @endforeach

                            </select>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">
                                Condition
                            </label>

                            <select name="condition_id"
                                class="form-select"
                                required>

                                @foreach ($conditions as $condition)

                                    <option value="{{ $condition->condition_id }}"
                                        {{ $property->condition_id == $condition->condition_id ? 'selected' : '' }}>

                                        {{ $condition->property_condition }}

                                    </option>

                                @endforeach

                            </select>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">
                                Price Category
                            </label>

                            <select name="Price_Category"
                                class="form-select"
                                required>

                                @foreach (['Medium', 'High', 'Luxury'] as $category)

                                    <option value="{{ $category }}"
                                        {{ $property->Price_Category == $category ? 'selected' : '' }}>

                                        {{ $category }}

                                    </option>

                                @endforeach

                            </select>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">
                                Price (€)
                            </label>

                            <input type="number"
                                step="0.01"
                                name="Price_EUR"
                                class="form-control"
                                value="{{ $property->Price_EUR }}"
                                required>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">
                                Size (sqm)
                            </label>

                            <input type="number"
                                step="0.1"
                                name="Size_sqm"
                                class="form-control"
                                value="{{ $property->Size_sqm }}"
                                required>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">
                                Rooms
                            </label>

                            <select name="Rooms"
                                class="form-select"
                                required>

                                @for ($room = 1; $room <= 6; $room++)

                                    <option value="{{ $room }}"
                                        {{ $property->Rooms == $room ? 'selected' : '' }}>

                                        {{ $room }}

                                    </option>

                                @endfor

                            </select>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                        Cancel
                    </button>

                    <button type="submit"
                        class="btn btn-primary">

                        Update
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endforeach

{{-- ADD MODAL --}}
<div class="modal fade"
    id="addModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <form action="{{ route('property.data.store') }}"
                method="POST">

                @csrf

                <div class="modal-header">

                    <h5 class="modal-title">
                        <i class="bi bi-plus-circle-fill text-primary me-2"></i>
                        Add Property
                    </h5>

                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <div class="row g-3">

                        <div class="col-md-6">

                            <label class="form-label">
                                District
                            </label>

                            <select name="location_id"
                                class="form-select"
                                required>

                                <option value="" selected disabled>
                                    Select District
                                </option>

                                @foreach ($locations as $location)

                                    <option value="{{ $location->location_id }}">
                                        District {{ $location->arrondissement }}
                                    </option>

                                @endforeach

                            </select>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">
                                Property Type
                            </label>

                            <select name="property_type_id"
                                class="form-select"
                                required>

                                <option value="" selected disabled>
                                    Select Property Type
                                </option>

                                @foreach ($propertyTypes as $type)

                                    <option value="{{ $type->property_type_id }}">
                                        {{ $type->property_type }}
                                    </option>

                                @endforeach

                            </select>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">
                                Condition
                            </label>

                            <select name="condition_id"
                                class="form-select"
                                required>

                                <option value="" selected disabled>
                                    Select Condition
                                </option>

                                @foreach ($conditions as $condition)

                                    <option value="{{ $condition->condition_id }}">
                                        {{ $condition->property_condition }}
                                    </option>

                                @endforeach

                            </select>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">
                                Price Category
                            </label>

                            <select name="Price_Category"
                                class="form-select"
                                required>

                                <option value="" selected disabled>
                                    Select Category
                                </option>

                                <option value="Medium">
                                    Medium
                                </option>

                                <option value="High">
                                    High
                                </option>

                                <option value="Luxury">
                                    Luxury
                                </option>

                            </select>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">
                                Price (€)
                            </label>

                            <input type="number"
                                step="0.01"
                                name="Price_EUR"
                                class="form-control"
                                required>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">
                                Size (sqm)
                            </label>

                            <input type="number"
                                step="0.1"
                                name="Size_sqm"
                                class="form-control"
                                required>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">
                                Rooms
                            </label>

                            <select name="Rooms"
                                class="form-select"
                                required>

                                <option value="" selected disabled>
                                    Select Rooms
                                </option>

                                @for ($room = 1; $room <= 6; $room++)

                                    <option value="{{ $room }}">
                                        {{ $room }}
                                    </option>

                                @endfor

                            </select>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                        Cancel
                    </button>

                    <button type="submit"
                        class="btn btn-primary">

                        Save Property
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const alert = document.querySelector('.alert-success');

    if (alert) {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-8px)';

            setTimeout(() => {
                alert.remove();
            }, 400);
        }, 2500);
    }
});
</script>
@endpush    

@endpush