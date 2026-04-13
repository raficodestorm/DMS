@extends('layouts.adminlayout')
@section('content')

<div class="container-fluid main-area">
    <div class="index-card shadow">
        <div class="card-header text-white fw-bold p-2 mb-3" style="background-color: #ff0000">Cost Entry</div>
        <div class="card-body">

            @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('admin.costs.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">Amount</label>
                    <input type="number" name="amount" class="form-control" placeholder="Enter cost amount" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Purpose</label>
                    <select name="purpose" id="purpose" class="form-control" required>
                        <option value="snack">Snack</option>
                        <option value="buy-materials">Buy Materials</option>
                        <option value="salary">Salary</option>
                        <option value="trip-payment">Trip payment</option>
                        <option value="fine">Fine</option>
                    </select>
                </div>

                {{-- Hidden Staff Name Field --}}
                <div class="mb-3 d-none" id="staffField">
                    <label class="form-label fw-semibold">Staff Name & rank</label>
                    <input type="text" name="staff_name" class="form-control" placeholder="Enter staff name with rank">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Details</label>
                    <input type="text" name="details" class="form-control">
                </div>

                <button type="submit" class="btn btn-success px-4">Save</button>
                <a href="{{ route('admin.costs.index') }}" class="btn btn-secondary px-4">Back</a>
            </form>

        </div>
    </div>
</div>


{{-- SIMPLE JS TO TOGGLE STAFF NAME --}}
<script>
    const purpose = document.getElementById('purpose');
    const staffField = document.getElementById('staffField');

    purpose.addEventListener('change', function () {
        if (this.value === 'salary') {
            staffField.classList.remove('d-none');
        } else {
            staffField.classList.add('d-none');
        }
    });
</script>

@endsection