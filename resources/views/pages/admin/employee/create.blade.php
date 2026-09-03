@extends(getLayout())

@section('content')

<div class="container justify-center">
    <div class="form-card">
        <h2>Add New Employee</h2>
        @include('components.alert')

        <form class="adduser-form" method="POST" action="{{ route('admin.employees.store') }}"
            enctype="multipart/form-data">
            @csrf
            <div class="row">
            <div class="col-md-6">
                <label>Full name</label>
                <input class="input-form" name="name" required>
                @error('name')<div class="error-text">{{ $message }}</div>@enderror
            </div>

           

            <div class="col-md-6">
                <label>Father name</label>
                <input class="input-form" name="father">
                @error('father')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label>Phone</label>
                <input class="input-form" name="phone" required>
                @error('phone')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label>Email</label>
                <input class="input-form" name="email" type="email">
                @error('email')<div class="error-text">{{ $message }}</div>@enderror
            </div>

             <div class="col-md-6">
                <label>Rank</label>
                <select class="input-form" name="rank">
                    <option value="">--Select Rank--</option>
                    <option value="SR">SR</option>
                    <option value="TSM">TSM</option>
                    <option value="Manager">Manager</option>
                    <option value="DSO">DSO</option>
                    <option value="Cooperator">Cooperator</option>
                </select>
                @error('rank')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label>Branch</label>
                <select class="input-form" name="branch_id">
                    <option value="">--Select Branch--</option>
                    @foreach($branches as $branch)
                    <option value="{{$branch->id}}">{{$branch->name}}</option>
                    @endforeach
                </select>
            </div>


            <div class="photo-upload col-12">
                <div class="upload-left">
                    <label>Profile Picture</label>
                    <input class="input-form" type="file" name="photo" id="photoInput">
                    @error('photo')<div class="error-text">{{ $message }}</div>@enderror
                </div>

                <div class="upload-right">
                    <div class="preview-box">
                        <i class="fa-solid fa-user" id="defaultIcon"></i>
                        <img id="photoPreview" src="" alt="Preview">
                    </div>
                </div>
            </div>

            <div class="col-12">
                <label>Address</label>
                <textarea class="input-form" name="address"></textarea>
                @error('address')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            </div> <!--/row-->

            <div>
                <button class="btn-submit" type="submit">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
    document.getElementById('photoInput').addEventListener('change', function (e) {
    const file = e.target.files[0];
    const preview = document.getElementById('photoPreview');
    const icon = document.getElementById('defaultIcon');

    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();

        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            icon.style.display = 'none';
        }

        reader.readAsDataURL(file);
    }
});
</script>
@endpush