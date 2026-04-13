@extends('layouts.adminlayout')
@section('content')

<div class="container-fluid main-area">
    <div class="index-card shadow">
        <div class="card-header p-2 mb-3 text-white fw-bold" style="background-color: #ff0000">Edit cost</div>
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

            <form action="{{ route('admin.costs.update', $cost->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-semibold">Amount</label>
                    <input type="number" name="amount" value="{{$cost->amount}}" class="form-control"
                        placeholder="Enter cost amount" required>
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

                <div class="mb-3">
                    <label class="form-label fw-semibold">Staff Name & rank</label>
                    <input type="text" name="staff_name" value="{{$cost->staff_name}}" class="form-control"
                        placeholder="Enter staff name with rank">
                </div>

                <div class="mb-3">
                    <<label class="form-label fw-semibold">Details</label>
                        <input type="text" name="details" value="{{$cost->details}}" class="form-control">
                </div>

                <button type="submit" class="btn btn-success px-4">Update</button>
                <a href="{{ route('admin.costs.index') }}" class="btn btn-secondary px-4">Back</a>
            </form>
        </div>
    </div>
</div>

@endsection