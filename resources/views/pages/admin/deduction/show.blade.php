@extends('layouts.adminlayout')

@section('content')
<p class="show-head">Deduction Details</p>
<div class="show-card">
    <div class="header-accent">

    </div>

    <div class="content-area">

        <div class="info-list">
            <div class="info-group">
                <span class="i-label">Type</span>
                <span class="i-value">{{ $deduction->name }}</span>
            </div>
            <div class="info-group">
                <span class="i-label">Cust-Deduction</span>
                <span class="i-value">{{ $deduction->customer_deduction }}</span>
            </div>
            <div class="info-group">
                <span class="i-label">Own-Deduction</span>
                <span class="i-value">{{ $deduction->my_deduction }}</span>
            </div>
            <div class="info-group">
                <span class="i-label">Tree-Deduction</span>
                <span class="i-value">{{ $deduction->tree_deduction }}</span>
            </div>

            <div class="info-group">
                <span class="i-label">Created at</span>
                <span class="i-value">{{ $deduction->created_at->timezone(auth()->user()->timezone)->format('d M Y, h:i
                    A')
                    }}</span>
            </div>

        </div>

        <div class="statement">
            <p class="statement-text">
                <strong>deduction Summary</strong><br>
                Deduction always count in percentage in the software system. Customer deduction will cut from selected
                products while saling. Own deduction will count oly for calculating profit and loss.
            </p>
        </div>
    </div>

    <div class="card-footer-actions">
        <a href="{{ route('admin.deductions.edit', $deduction->id) }}" class="icon-btn edit-icon">
            <i class="fa-solid fa-pen"></i>
        </a>

        <form action="{{ route('admin.deductions.destroy', $deduction->id) }}" method="POST" class="d-inline"
            onsubmit="return confirm('Are you sure you want to delete this deduction?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="icon-btn delete-icon" style="border: none; background: none;">
                <i class="fa-solid fa-trash"></i>
            </button>
        </form>
    </div>

</div>

<div style="text-align: center; margin-top: 20px;">
    <a href="{{ route('admin.deductions.index') }}" style="color: var(--text-muted); text-decoration: none;">
        <i class="fas fa-arrow-left"></i> Back to List
    </a>
</div>

@endsection