@extends(getLayout())

@section('content')

<div class="container justify-center">
    <div class="form-card">
        <h2>Add New Customer</h2>
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form class="adduser-form" method="POST" action="{{ route('customers.store') }}" enctype="multipart/form-data">
            @csrf

            <div>
                <label>Shop name</label>
                <input type="text" class="input-form" name="shop_name" required>
                @error('shop_name')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div>
                <label>Manager</label>
                <input type="text" class="input-form" name="manager" required>
                @error('manager')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div>
                <label>Phone</label>
                <input type="text" class="input-form" name="phone" required>
                @error('phone')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div>
                <label>Address</label>
                <textarea class="input-form" name="address"></textarea>
                @error('address')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            {{-- <div>
                <label>Zone</label>
                <select class="input-form" name="branch_id">
                    <option value="">--Select Zone--</option>
                    @foreach($branches as $branch)
                    <option value="{{$branch->id}}">{{$branch->name}}</option>
                    @endforeach
                </select>
            </div> --}}

            <div>
                <button class="btn-submit" type="submit">Submit</button>
            </div>
        </form>
    </div>
</div>
@endsection