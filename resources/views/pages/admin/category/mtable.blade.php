@forelse($categories as $category)
<div class="manage-card">
    <div class="card-body">
        <div><span>S.No</span>
            <p>{{ $categories->firstItem() ? $categories->firstItem() + $loop->index : $loop->iteration }}</p>
        </div>
        <div><span>Category Name</span>
            <p>{{ $category->name }}</p>
        </div>
        <div><span>Description</span>
            <p>{{ $category->description }}</p>
        </div>
    </div>
    <div class="card-actions">
        <a href="{{ route('admin.categories.show', $category) }}" class="icon-btn view-icon">
            <i class="fa-solid fa-eye"></i>
        </a>
    </div>
</div>
@empty
<p class="text-center text-muted">No records found.</p>
@endforelse
