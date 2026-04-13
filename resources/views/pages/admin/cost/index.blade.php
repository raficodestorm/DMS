@extends('layouts.adminlayout')
@section('content')

<div class="container-fluid main-area">
    <div class="index-card shadow">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>All Costs</h3>
        </div>

        @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered align-middle" id="table-same">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User id</th>
                    <th>Counter</th>
                    <th>Amount</th>
                    <th>Purpose</th>
                    <th>Staff</th>
                    <th>Details</th>
                    <th>Action</th>

                </tr>
            </thead>
            <tbody>
                @forelse($costs as $cost)
                <tr class="text-center">
                    <td>{{ $cost->id }}</td>
                    <td>{{ $cost->user_id }}</td>
                    <td>{{ $cost->user->counter->name ?? '—' }}</td>
                    <td>{{ $cost->amount }}</td>
                    <td>{{ $cost->purpose }}</td>
                    <td>{{ $cost->staff_name }}</td>
                    <td>{{ $cost->details }}</td>
                    <td>

                        <a href="{{ route('admin.costs.edit', $cost->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('admin.costs.destroy', $cost->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Are you sure?')"
                                class="btn btn-danger btn-sm">Delete</button>

                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">No records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- {{ $counters->links() }} --}}
    </div>
</div>

@endsection