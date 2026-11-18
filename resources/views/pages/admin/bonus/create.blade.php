@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h2 class="mb-0"><i class="fas fa-plus-circle text-primary me-2"></i> Add New Bonus</h2>
            <p class="text-muted mb-0">Enter details for additional company income.</p>
        </div>
        @if(isset($totalAdminCash))
        <div style="background: rgba(49, 49, 255, 0.08); color: var(--primary); padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; border: 1px solid rgba(49, 49, 255, 0.2);">
            <i class="fa-solid fa-vault me-1"></i> Current Admin Cash: ৳ {{ number_format($totalAdminCash, 2) }}
        </div>
        @endif
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

        <div class="col-12">
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 10px 14px;">
                <small class="text-success d-flex align-items-center gap-2" style="font-size: 0.82rem; font-weight: 500;">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>This bonus amount will be automatically added to <strong>Central Admin Cash</strong> and recorded in the <strong>Admin Cash Ledger</strong>.</span>
                </small>
            </div>
        </div>

        <div class="col-12 d-flex gap-2 justify-content-end mt-4">
            <a href="{{ route('admin.bonuses.index') }}" class="btn-smart btn-red">Cancel</a>
            <button type="submit" class="btn-smart btn-blue">Save Bonus Entry</button>
        </div>
    </form>
</div>
@endsection
