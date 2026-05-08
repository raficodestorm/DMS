@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">
    <div class="card-header mb-4">
        <h2 class="mb-0"><i class="fas fa-plus-circle text-primary me-2"></i> Add New Bonus</h2>
        <p class="text-muted">Enter details for additional company income.</p>
    </div>

    <form action="{{ route('admin.bonuses.store') }}" method="POST" class="row g-4">
        @csrf

        <div class="col-md-6">
            <label class="form-label fw-bold">Bonus Title</label>
            <input type="text" name="title" class="input-form @error('title') is-invalid @enderror" 
                   placeholder="e.g. Monthly Sales Incentive" value="{{ old('title') }}" required>
            @error('title') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label fw-bold">Amount (৳)</label>
            <input type="number" name="amount" step="0.01" class="input-form @error('amount') is-invalid @enderror" 
                   placeholder="0.00" value="{{ old('amount') }}" required>
            @error('amount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label fw-bold">Bonus Date</label>
            <input type="date" name="bonus_date" class="input-form @error('bonus_date') is-invalid @enderror" 
                   value="{{ old('bonus_date', date('Y-m-d')) }}" required>
            @error('bonus_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label fw-bold">Bonus Type</label>
            <select name="type" class="input-form @error('type') is-invalid @enderror" required>
                <option value="incentive" {{ old('type') == 'incentive' ? 'selected' : '' }}>Incentive</option>
                <option value="cashback" {{ old('type') == 'cashback' ? 'selected' : '' }}>Cashback</option>
                <option value="special" {{ old('type') == 'special' ? 'selected' : '' }}>Special Bonus</option>
                <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Other Income</option>
            </select>
            @error('type') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        <div class="col-12">
            <label class="form-label fw-bold">Description (Optional)</label>
            <textarea name="description" class="input-form @error('description') is-invalid @enderror" 
                      rows="4" placeholder="Provide more context about this bonus...">{{ old('description') }}</textarea>
            @error('description') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        <div class="col-12 d-flex gap-2 justify-content-end mt-4">
            <a href="{{ route('admin.bonuses.index') }}" class="btn-smart btn-red">Cancel</a>
            <button type="submit" class="btn-smart btn-blue">Save Bonus Entry</button>
        </div>
    </form>
</div>
@endsection
