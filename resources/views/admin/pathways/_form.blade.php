<div class="row g-3">
    <div class="col-lg-6">
        <div class="form-group">
            <label class="form-label">Title <span class="text-danger">*</span></label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $pathway->title) }}" placeholder="e.g. Gut Reset" required>
            @error('title') <div class="text-danger mt-1">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label class="form-label">Group <span class="text-danger">*</span></label>
            <select name="group" class="form-control select2" required>
                @foreach($groups as $key => $label)
                    <option value="{{ $key }}" {{ old('group', $pathway->group ?? 'core') == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @error('group') <div class="text-danger mt-1">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label class="form-label">Protocol Form</label>
            <select name="protocol_key" class="form-control select2">
                <option value="">No protocol form</option>
                @foreach($protocols as $key => $label)
                    <option value="{{ $key }}" {{ old('protocol_key', $pathway->protocol_key) == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <small class="text-muted">The questionnaire the client fills after selecting this pathway (defined in config/protocols.php).</small>
            @error('protocol_key') <div class="text-danger mt-1">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label class="form-label">Tags</label>
            <input type="text" name="tags" class="form-control" value="{{ old('tags', implode(', ', $pathway->tags ?? [])) }}" placeholder="e.g. 8-12 Weeks, Microbiome Tuning">
            <small class="text-muted">Comma separated chips shown on the pathway card.</small>
            @error('tags') <div class="text-danger mt-1">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="col-lg-4">
        <div class="form-group">
            <label class="form-label">Sort Order</label>
            <input type="number" name="sort_order" min="0" class="form-control" value="{{ old('sort_order', $pathway->sort_order ?? 0) }}">
            @error('sort_order') <div class="text-danger mt-1">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="col-lg-4">
        <div class="form-group">
            <label class="form-label">Status <span class="text-danger">*</span></label>
            <select name="status" class="form-control select2" required>
                <option value="Active" {{ old('status', $pathway->status ?? 'Active') == 'Active' ? 'selected' : '' }}>Active</option>
                <option value="Inactive" {{ old('status', $pathway->status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="form-group">
            <label class="form-label">Image / Icon</label>
            <input type="file" name="image" class="form-control" accept="image/*">
            @if($pathway->image)
                <img src="{{ getFileUrl($pathway->image) }}" alt="Pathway image" class="rounded border mt-2" width="60" height="60" style="object-fit:cover;">
            @endif
            @error('image') <div class="text-danger mt-1">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="col-lg-12">
        <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" rows="3" class="form-control" placeholder="Short description shown on the pathway card">{{ old('description', $pathway->description) }}</textarea>
            @error('description') <div class="text-danger mt-1">{{ $message }}</div> @enderror
        </div>
    </div>
</div>
