@forelse($categories as $category)
<tr>
    <td scope="row">{{ $categories->firstItem() ? $categories->firstItem() + $loop->index : $loop->iteration }}</td>
    <td class="name">{{ $category->name }}</td>
    <td>{{ $category->description }}</td>
    <td class="action-icons">
        <a href="{{ route('admin.categories.show', $category) }}" class="icon-btn view-icon">
            <i class="fa-solid fa-eye"></i>
        </a>
    </td>
</tr>
@empty
<tr>
    <td colspan="4" class="text-center text-muted">No records found.</td>
</tr>
@endforelse
