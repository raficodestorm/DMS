@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">
    <div class="card-header mb-4">
        <h2 class="mb-0"><i class="fas fa-edit text-primary me-2"></i> Edit Global Cost Record</h2>
        <p class="text-muted">Update the details for this global company expense.</p>
    </div>

    {{-- Admin Cash Balance Strip --}}
    <div style="background: linear-gradient(135deg, #f8faff 0%, #eef2ff 100%); border: 1px solid #c7d2fe; border-radius: 12px; padding: 14px 18px; margin-bottom: 22px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 40px; height: 40px; border-radius: 10px; background: var(--primary, #3131ff); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; box-shadow: 0 4px 10px rgba(49,49,255,0.25);">
                <i class="fa-solid fa-vault"></i>
            </div>
            <div>
                <div style="font-size: 0.76rem; text-transform: uppercase; color: #4338ca; font-weight: 700; letter-spacing: 0.5px;">Current Admin Cash Balance</div>
                <div style="font-size: 1.2rem; font-weight: 800; color: #1e1b4b;">
                    ৳ {{ number_format($totalAdminCash ?? 0, 2) }}
                </div>
            </div>
        </div>
        <div style="font-size: 0.82rem; color: #64748b; display: flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-circle-info text-primary"></i>
            <span>Cost amount changes will automatically adjust Admin Cash.</span>
        </div>
    </div>

    <form action="{{ route('admin.company_costs.update', $cost->id) }}" method="POST" class="row g-4">
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
                @foreach(['office', 'transport', 'staff', 'maintenance', 'product', 'utility', 'marketing', 'miscellaneous'] as $cat)
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
            <a href="{{ route('admin.company_costs.index') }}" class="btn-smart btn-red">Cancel</a>
            <button type="submit" class="btn-smart btn-green">Update Global Record</button>
        </div>
    </form>
</div>
@endsection