@extends('layouts.managerlayout')

@section('content')
<div class="manage-card">
    <div class="card-header mb-4">
        <h2 class="mb-0"><i class="fas fa-file-invoice-dollar text-primary me-2"></i> Record Branch Cost</h2>
        <p class="text-muted">Fill in the details to record a new expense for your branch.</p>
    </div>

    <form action="{{ route('manager.costs.store') }}" method="POST" class="row g-4">
        @csrf

        <div class="col-md-6">
            <label class="form-label fw-bold">Amount (৳) <span class="text-danger">*</span></label>
            <input type="number" name="amount" step="0.01" class="input-form @error('amount') is-invalid @enderror" 
                   placeholder="0.00" value="{{ old('amount') }}" required>
            @error('amount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label fw-bold">Cost Date <span class="text-danger">*</span></label>
            <input type="date" name="cost_date" class="input-form @error('cost_date') is-invalid @enderror" 
                   value="{{ old('cost_date', date('Y-m-d')) }}" required>
            @error('cost_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
            <select name="category" class="input-form @error('category') is-invalid @enderror" required>
                <option value="">Select Category</option>
                <option value="office" {{ old('category') == 'office' ? 'selected' : '' }}>Office Expenses</option>
                <option value="transport" {{ old('category') == 'transport' ? 'selected' : '' }}>Transport Cost</option>
                <option value="salary" {{ old('category') == 'salary' ? 'selected' : '' }}>Salary</option>
                <option value="maintenance" {{ old('category') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                <option value="product" {{ old('category') == 'product' ? 'selected' : '' }}>Product Related</option>
                <option value="utility" {{ old('category') == 'utility' ? 'selected' : '' }}>Utility Bills</option>
                <option value="marketing" {{ old('category') == 'marketing' ? 'selected' : '' }}>Marketing</option>
                <option value="miscellaneous" {{ old('category') == 'miscellaneous' ? 'selected' : '' }}>Miscellaneous</option>
            </select>
            @error('category') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label fw-bold">Short Description <span class="text-danger">*</span></label>
            <input type="text" name="description" class="input-form @error('description') is-invalid @enderror" 
                   placeholder="e.g. Electricity bill for March" value="{{ old('description') }}" required>
            @error('description') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        <div class="col-12">
            <label class="form-label fw-bold">Notes / Reference (Optional)</label>
            <textarea name="notes" class="input-form @error('notes') is-invalid @enderror" 
                      rows="3" placeholder="Additional details, voucher numbers, or references...">{{ old('notes') }}</textarea>
            @error('notes') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        <div class="col-12 d-flex gap-2 justify-content-end mt-4">
            <a href="{{ route('manager.costs.index') }}" class="btn-smart btn-red">Cancel</a>
            <button type="submit" class="btn-smart btn-blue">Save Cost Record</button>
        </div>
    </form>
</div>
@endsection
