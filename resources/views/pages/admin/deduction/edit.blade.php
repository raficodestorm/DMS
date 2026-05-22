@extends('layouts.adminlayout')

@section('content')

<div class="container justify-center">
    <div class="form-card">
        <h2>Edit your deduction</h2>
        <p style="color: gray;">Always input decimal number and it will count as percentage in the software system.</p>
        @include('components.alert')

        <form class="adduser-form" method="POST" action="{{ route('admin.deductions.update', $deduction->id) }}">
            @csrf
            @method('PUT')


            <div>
                <label>Deduction Type</label>
                <select class="input-form" name="type" required>
                    <option value="main" {{ (old('type', $deduction->type)=='main') ? 'selected' : '' }}>Main (%)
                    </option>

                    {{-- <option value="specific" {{ (old('type', $deduction->type)=='specific') ? 'selected' : ''
                        }}>Specific (%)</option>
                    <option value="console" {{ (old('type', $deduction->type)=='console') ? 'selected' : '' }}>Console
                        (%)</option> --}}

                </select>
                @error('type')<div class="error-text">{{ $message }}</div>@enderror
            </div>


            <div class="input-box">
                <label>Customer Deduction</label>
                <input type="number" step="0.01" class="input-form" name="customer_deduction" placeholder="0.00"
                    required value="{{ old('customer_deduction', $deduction->customer_deduction) }}">
                @error('customer_deduction')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="input-box">
                <label>Own Deduction</label>
                <input type="number" step="0.01" class="input-form" name="my_deduction" placeholder="0.00" required
                    value="{{ old('my_deduction', $deduction->my_deduction) }}">
                @error('my_deduction')<div class="error-text">{{ $message }}</div>@enderror
            </div>


            <div class="mt-4">
                <button class="btn-submit" type="submit">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection