@extends('layouts.managerlayout')

@section('content')
<div class="manage-card">
    <div class="card-header mb-4">
        <h2 class="mb-0"><i class="fas fa-edit text-primary me-2"></i> Edit Cost Record</h2>
        <p class="text-muted">Update the details for this expense entry.</p>
    </div>

    <form action="{{ route('manager.costs.update', $cost->id) }}" method="POST" class="row g-4">
        @csrf
        @method('PUT')

        <div class="col-md-6">
            <label class="form-label fw-bold">Amount (৳) <span class="text-danger">*</span></label>
            <input type="number" name="amount" step="0.01" class="input-form @error('amount') is-invalid @enderror" 
                   value="{{ old('amount', $cost->amount) }}" required>
            @error('amount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label fw-bold">Cost Date <span class="text-danger">*</span></label>
            <input type="date" name="cost_date" class="input-form @error('cost_date') is-invalid @enderror" 
                   value="{{ old('cost_date', $cost->cost_date->format('Y-m-d')) }}" required>
            @error('cost_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
            <select name="category" class="input-form @error('category') is-invalid @enderror" required>
                @foreach(['office', 'transport', 'salary', 'maintenance', 'product', 'utility', 'marketing', 'miscellaneous'] as $cat)
                    <option value="{{ $cat }}" {{ old('category', $cost->category) == $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                @endforeach
            </select>
            @error('category') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label fw-bold">Short Description <span class="text-danger">*</span></label>
            <input type="text" name="description" class="input-form @error('description') is-invalid @enderror" 
                   value="{{ old('description', $cost->description) }}" required>
            @error('description') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        <div class="col-12">
            <label class="form-label fw-bold">Notes / Reference (Optional)</label>
            <textarea name="notes" class="input-form @error('notes') is-invalid @enderror" 
                      rows="3">{{ old('notes', $cost->notes) }}</textarea>
            @error('notes') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        <div class="col-12 d-flex gap-2 justify-content-end mt-4">
            <a href="{{ route('manager.costs.index') }}" class="btn-smart btn-red">Cancel</a>
            <button type="submit" class="btn-smart btn-green">Update Cost Record</button>
        </div>
    </form>
</div>
@endsection
