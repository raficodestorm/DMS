@extends('layouts.adminlayout')

@section('content')
<div class="container justify-center">
    <div class="form-card" style="max-width: 720px; width: 100%;">
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2" style="border-bottom: 1px solid var(--border-color, #e2e8f0);">
            <div>
                <h2 class="mb-0">Edit Category</h2>
                <p class="text-muted mb-0" style="font-size: 0.85rem;">Update category details and media</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.categories.show', $category) }}" class="btn-smart btn-gray">
                    <i class="fas fa-eye me-1"></i> View
                </a>
                <a href="{{ route('admin.categories.index') }}" class="btn-smart btn-gray">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>

        @include('components.alert')

        @php
            $catImg = $category->image 
                ? (str_starts_with($category->image, 'uploads/') ? asset($category->image) : asset('uploads/' . $category->image))
                : '';
        @endphp

        <form class="adduser-form" method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                {{-- Category Name --}}
                <div class="col-12 mb-3">
                    <label class="form-label" style="font-weight: 600;">Category Name <span class="text-danger">*</span></label>
                    <input type="text" class="input-form" name="name" value="{{ old('name', $category->name) }}" placeholder="e.g. Electrical Switches" required autofocus>
                    @error('name')<div class="error-text text-danger mt-1" style="font-size: 0.8rem;">{{ $message }}</div>@enderror
                </div>

                {{-- Description --}}
                <div class="col-12 mb-3">
                    <label class="form-label" style="font-weight: 600;">Description <span class="text-muted" style="font-weight: 400; font-size: 0.8rem;">(Optional)</span></label>
                    <textarea class="input-form" name="description" rows="3" placeholder="Brief summary of what products belong to this category..." style="resize: vertical; height: auto;">{{ old('description', $category->description) }}</textarea>
                    @error('description')<div class="error-text text-danger mt-1" style="font-size: 0.8rem;">{{ $message }}</div>@enderror
                </div>

                {{-- Featured Switch --}}
                <div class="col-12 mb-4">
                    <div style="background: var(--background, #f8fafc); border: 1px solid var(--border-color, #e2e8f0); border-radius: 10px; padding: 12px 16px; display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <span style="font-weight: 600; font-size: 0.9rem; color: var(--text-main); display: block;">
                                <i class="fas fa-star text-warning me-1"></i> Featured Category
                            </span>
                            <span style="font-size: 0.8rem; color: var(--text-muted);">Highlight this category on featured sections and quick filters</span>
                        </div>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="isFeaturedSwitch" {{ old('is_featured', $category->is_featured) ? 'checked' : '' }} style="cursor: pointer; width: 2.5em; height: 1.3em;">
                        </div>
                    </div>
                </div>

                {{-- Category Image Upload --}}
                <div class="photo-upload col-12 mb-4">
                    <div class="upload-left" style="flex: 1;">
                        <label class="form-label" style="font-weight: 600;">Category Banner / Icon <span class="text-muted" style="font-weight: 400; font-size: 0.8rem;">(Upload new to replace)</span></label>
                        <input class="input-form" type="file" name="image" id="catPhotoInput" accept="image/*">
                        <small class="text-muted d-block mt-1">Recommended format: JPG, PNG, WEBP or SVG. Max 3MB.</small>
                        @error('image')<div class="error-text text-danger mt-1" style="font-size: 0.8rem;">{{ $message }}</div>@enderror

                        @if($category->image)
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="remove_image" value="1" id="removeImageCheck">
                            <label class="form-check-label text-danger" for="removeImageCheck" style="font-size: 0.82rem; cursor: pointer;">
                                <i class="fas fa-trash-alt me-1"></i> Remove current image
                            </label>
                        </div>
                        @endif
                    </div>

                    <div class="upload-right ms-3" style="width: 100px; height: 100px; flex-shrink: 0;">
                        <div class="preview-box" style="width: 100%; height: 100%; position: relative;">
                            <i class="fa-solid fa-tags" id="catDefaultIcon" style="{{ $catImg ? 'display: none;' : 'display: block;' }} font-size: 32px; color: var(--primary);"></i>
                            <img id="catPhotoPreview" src="{{ $catImg }}" alt="Preview" style="{{ $catImg ? 'display: block !important;' : 'display: none !important;' }} width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="col-12 d-flex justify-content-end gap-2 pt-2" style="border-top: 1px solid var(--border-color, #e2e8f0);">
                    <a href="{{ route('admin.categories.index') }}" class="btn-smart btn-gray">
                        Cancel
                    </a>
                    <button class="btn-smart btn-blue" type="submit">
                        <i class="fas fa-sync-alt me-1"></i> Update Category
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input       = document.getElementById('catPhotoInput');
    const preview     = document.getElementById('catPhotoPreview');
    const icon        = document.getElementById('catDefaultIcon');
    const removeCheck = document.getElementById('removeImageCheck');
    const origSrc     = "{{ $catImg }}";

    if (input) {
        input.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.setProperty('display', 'block', 'important');
                    if (icon) icon.style.display = 'none';
                };
                reader.readAsDataURL(file);
                if (removeCheck) removeCheck.checked = false;
            } else if (origSrc && (!removeCheck || !removeCheck.checked)) {
                preview.src = origSrc;
                preview.style.setProperty('display', 'block', 'important');
                if (icon) icon.style.display = 'none';
            } else {
                preview.src = '';
                preview.style.setProperty('display', 'none', 'important');
                if (icon) icon.style.display = 'block';
            }
        });
    }

    if (removeCheck) {
        removeCheck.addEventListener('change', function () {
            if (this.checked) {
                preview.src = '';
                preview.style.setProperty('display', 'none', 'important');
                if (icon) icon.style.display = 'block';
                if (input) input.value = '';
            } else if (origSrc) {
                preview.src = origSrc;
                preview.style.setProperty('display', 'block', 'important');
                if (icon) icon.style.display = 'none';
            }
        });
    }
});
</script>
@endsection