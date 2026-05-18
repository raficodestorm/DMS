@extends(getLayout())

@section('content')

<div class="container justify-center">
    <div class="form-card">
        <h2>Edit Customer</h2>
        <h4>ID : BRC200{{ $customer->id }}</h4>
        @include('components.alert')

        <form class="adduser-form" method="POST" action="{{ route('admin.customers.update', $customer->id) }}">
            @csrf
            @method('PUT')
            <div>
                <label>Shop Name</label>
                <input type="text" class="input-form" name="shop_name"
                    value="{{ old('shop_name', $customer->shop_name) }}" required>
                @error('shop_name')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div>
                <label>Manager</label>
                <input type="text" class="input-form" name="manager" value="{{ old('manager', $customer->manager) }}"
                    required>
                @error('manager')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div>
                <label>Phone</label>
                <input type="text" class="input-form" name="phone" value="{{ old('phone', $customer->phone) }}"
                    required>
                @error('phone')<div class="error-text">{{ $message }}</div>@enderror
            </div>


            <div>
                <label>Address</label>
                <textarea class="input-form" name="address">{{ old('address', $customer->address) }}</textarea>
                @error('address')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div>
                <label>Zone</label>
                <select class="input-form" name="branch_id">
                    <option value="">--Select Zone--</option>

                    @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ old('branch_id', $customer->branch_id) == $branch->id ?
                        'selected'
                        : '' }}>
                        {{ $branch->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label>Due</label>
                <input class="input-form" name="due" value="{{ old('due', $customer->due) }}"
                    required>
                @error('due')<div class="error-text">{{ $message }}</div>@enderror
            </div>


            <div>
                <button class="btn-submit" type="submit">Update</button>
            </div>
        </form>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary px-4 mt-4">Back</a>
    </div>
</div>
@endsection