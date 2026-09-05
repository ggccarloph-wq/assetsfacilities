<input type="hidden" name="item_type" value="{{ old('item_type', $item->item_type ?? $type ?? request('type', 'CAPEX')) }}">
@php
    $fixedType = old('item_type', $item->item_type ?? $type ?? request('type', 'CAPEX'));
@endphp
@php
    $isCapex = $fixedType === 'CAPEX';
@endphp
@php
    $existingItem = ($item ?? null);
@endphp
@php
    $categoryTypeMap = collect($categories ?? [])->mapWithKeys(fn($cat) => [$cat->id => (($assetTypeOptions ?? [])[$cat->name] ?? [])]);
@endphp
@php
    $roomsByFloor = $roomOptions ?? [];
@endphp

<div class="form-section">
  <div class="form-section-head">
    <div class="form-section-icon"><i class="bi bi-tag"></i></div>
    <div>
      <p class="form-section-title">Identification</p>
      <div class="form-section-sub">What is this {{ $isCapex ? 'asset' : 'item' }} called, and how is it tagged?</div>
    </div>
    <span class="step-badge">1</span>
  </div>
  <div class="form-section-body">
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">{{ $isCapex ? 'Asset Tag ID' : 'Item Code' }}</label>
        @if($isCapex)
          <input class="form-control" value="{{ $existingItem->item_code ?? 'Generated automatically on save' }}" readonly disabled>
          <div class="tiny mt-1">Auto-generated from the selected floor — random and duplicate-checked. No typing.</div>
        @else
          <input name="item_code" class="form-control" value="{{ old('item_code', $item->item_code ?? ($suggestedCode ?? '')) }}" placeholder="Leave blank to auto-generate">
        @endif
      </div>
      <div class="col-md-4">
        <label class="form-label">{{ $isCapex ? 'Asset Name / Model' : 'Name' }}</label>
        <input name="name" class="form-control" value="{{ old('name', $item->name ?? '') }}" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">Type</label>
        <input class="form-control" value="{{ $fixedType }}" readonly>
      </div>
    </div>
  </div>
</div>

<div class="form-section">
  <div class="form-section-head">
    <div class="form-section-icon"><i class="bi bi-diagram-3"></i></div>
    <div>
      <p class="form-section-title">Classification &amp; Location</p>
      <div class="form-section-sub">Category, department, and {{ $isCapex ? 'where this asset is kept' : 'stock details' }}.</div>
    </div>
    <span class="step-badge">2</span>
  </div>
  <div class="form-section-body">
    <div class="row g-3">
      <div class="col-md-6">
        @if($isCapex)
          <label class="form-label">Category &amp; Asset Type</label>
          <div class="tree-select" id="category_tree">
            <button type="button" class="tree-select-trigger form-select">
              <span class="tree-select-label">Select category</span>
              <span class="chevron">&#9662;</span>
            </button>
            <div class="tree-select-panel"></div>
          </div>
          <input type="hidden" name="category_id" id="category_select" value="{{ old('category_id', $item->category_id ?? '') }}">
          <input type="hidden" name="asset_type_name" id="asset_type_choice" value="{{ old('asset_type_name', $item->asset_type_name ?? '') }}">
          <div class="tiny mt-1">Don't see the right one? Ask your Super Admin to add it under Reference Data.</div>
        @else
          <label class="form-label">Category</label>
          <div class="tree-select" id="category_tree_opex">
            <button type="button" class="tree-select-trigger form-select">
              <span class="tree-select-label">Select category</span>
              <span class="chevron">&#9662;</span>
            </button>
            <div class="tree-select-panel"></div>
          </div>
          <input type="hidden" name="category_id" id="category_select" value="{{ old('category_id', $item->category_id ?? '') }}">
          <div class="tiny mt-1">Don't see the right one? Ask your Super Admin to add it under Reference Data.</div>
        @endif
      </div>
      <div class="col-md-6">
        <label class="form-label">Assigned Department</label>
        <select name="assigned_department_id" class="form-select">
          <option value="">Select department</option>
          @foreach(($departments ?? []) as $department)
            <option value="{{ $department->id }}" @selected(old('assigned_department_id', $item->assigned_department_id ?? '') == $department->id)>{{ $department->name }}</option>
          @endforeach
        </select>
      </div>

      @if($isCapex)
        <input type="hidden" name="quantity" value="{{ old('quantity', $item->quantity ?? 1) }}">
        <input type="hidden" name="unit" value="{{ old('unit', $item->unit ?? 'unit') }}">
        <input type="hidden" name="unit_price" value="{{ old('unit_price', $item->unit_price ?? 0) }}">
        <input type="hidden" name="availability_status" value="{{ old('availability_status', $item->availability_status ?? 'Available') }}">
        <input type="hidden" name="low_stock_threshold" value="{{ old('low_stock_threshold', $item->low_stock_threshold ?? 0) }}">
        <div class="col-md-4">
          <label class="form-label">Date Acquired</label>
          <input type="date" name="acquisition_date" class="form-control" value="{{ old('acquisition_date', optional($item->acquisition_date ?? null)->format('Y-m-d') ?? '') }}">
        </div>
        <div class="col-md-4">
          <label class="form-label">Floor</label>
          @if($existingItem?->exists)
            <input class="form-control" value="{{ $existingItem->floorRef->name ?? $existingItem->floor }}" readonly disabled>
            <input type="hidden" name="floor_id" id="floor_select" value="{{ $existingItem->floor_id }}">
            <div class="tiny mt-1">Floor is locked after creation to keep the asset tag consistent.</div>
          @else
            <div class="tree-select" id="floor_tree">
              <button type="button" class="tree-select-trigger form-select">
                <span class="tree-select-label">Select floor</span>
                <span class="chevron">&#9662;</span>
              </button>
              <div class="tree-select-panel"></div>
            </div>
            <input type="hidden" name="floor_id" id="floor_select" value="{{ old('floor_id') }}">
            <input type="hidden" name="room_id" id="room_select" value="{{ old('room_id') }}">
          @endif
        </div>
        @if($existingItem?->exists)
        <div class="col-md-4" id="room_wrap">
          <label class="form-label">Assigned Room</label>
          <select name="room_id" id="room_select_edit" class="form-select" required>
            <option value="">Select room</option>
          </select>
          <div class="tiny mt-1">Missing room? Ask your Super Admin to add it under Reference Data.</div>
        </div>
        @else
        <div class="col-md-4">
          <label class="form-label">How Many Units?</label>
          <input type="number" name="unit_count" class="form-control" min="1" max="300" value="{{ old('unit_count', 1) }}" required>
          <div class="tiny mt-1">Creates that many separate assets in this room, each with its own auto-generated asset tag ID.</div>
        </div>
        @endif
        <div class="col-12">
          <label class="form-label">Brand</label>
          <input name="brand" class="form-control" value="{{ old('brand', $item->brand ?? '') }}" placeholder="e.g. Dell, HP, Epson">
        </div>
      @else
        <div class="col-md-3"><label class="form-label">Quantity</label><input type="number" name="quantity" class="form-control" value="{{ old('quantity', $item->quantity ?? 0) }}" required></div>
        <div class="col-md-3"><label class="form-label">Unit</label><input name="unit" class="form-control" value="{{ old('unit', $item->unit ?? '') }}" required></div>
        <div class="col-md-3"><label class="form-label">Unit Price</label><input type="number" step="0.01" min="0" name="unit_price" class="form-control" value="{{ old('unit_price', $item->unit_price ?? 0) }}"></div>
        <div class="col-md-3"><label class="form-label">Brand</label><input name="brand" class="form-control" value="{{ old('brand', $item->brand ?? '') }}"></div>
        <div class="col-md-6"><label class="form-label">Availability</label><select name="availability_status" class="form-select"><option value="Available" @selected(old('availability_status', $item->availability_status ?? 'Available')==='Available')>Available</option><option value="Limited Stock" @selected(old('availability_status', $item->availability_status ?? '')==='Limited Stock')>Limited Stock</option><option value="Out of Stock" @selected(old('availability_status', $item->availability_status ?? '')==='Out of Stock')>Out of Stock</option></select></div>
        <div class="col-md-6"><label class="form-label">Low Stock Threshold</label><input type="number" name="low_stock_threshold" class="form-control" value="{{ old('low_stock_threshold', $item->low_stock_threshold ?? 0) }}" required><div class="tiny mt-1">Trigger a low-stock alert when quantity falls to this number.</div></div>
      @endif
    </div>
  </div>
</div>

<div class="form-section">
  <div class="form-section-head">
    <div class="form-section-icon"><i class="bi bi-card-image"></i></div>
    <div>
      <p class="form-section-title">Media &amp; Description</p>
      <div class="form-section-sub">Optional photo, specs, and notes.</div>
    </div>
    <span class="step-badge">3</span>
  </div>
  <div class="form-section-body">
    <div class="row g-3">
      <div class="col-md-6"><label class="form-label">Item Image</label><input type="file" name="image_file" class="form-control" accept=".jpg,.jpeg,.png,.webp,image/*"><div class="tiny mt-1">Upload a JPG, PNG, or WEBP file up to 4MB.</div></div>
      <div class="col-md-6"><label class="form-label">Specifications</label><textarea name="specifications" class="form-control" rows="3">{{ old('specifications', $item->specifications ?? '') }}</textarea></div>
  @if(!empty($item?->display_image))
    <div class="col-md-6">
      <label class="form-label d-block">Current Image</label>
      <img src="{{ $item->display_image }}" alt="{{ $item->name ?? 'Item image' }}" style="width:140px;height:140px;object-fit:cover;border-radius:14px;border:1px solid var(--line);background:var(--surface-2)">
      @if(!empty($item?->image_path))
      <div class="form-check mt-2">
        <input class="form-check-input" type="checkbox" value="1" name="remove_image" id="remove_image">
        <label class="form-check-label" for="remove_image">Remove current image</label>
      </div>
      @endif
    </div>
  @endif
      <div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="4">{{ old('description', $item->description ?? '') }}</textarea></div>
      <div class="col-12 form-check ms-2"><input class="form-check-input" type="checkbox" value="1" name="is_active" id="is_active" @checked(old('is_active', $item->is_active ?? true))><label class="form-check-label" for="is_active">Active item</label></div>
    </div>
  </div>
</div>

  <script>
  (function () {
    const categoryTypeMap = @json($categoryTypeMap);
    const roomsByFloor = @json($roomsByFloor);
    const categoryList = @json($categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->values());
    const floorList = @json(collect($floors ?? [])->map(fn($f) => ['id' => $f->id, 'name' => $f->name])->values());

    const presetCategoryId = @json(old('category_id', $item->category_id ?? ''));
    const presetAssetType = @json(old('asset_type_name', $item->asset_type_name ?? ''));
    const presetFloorId = @json(old('floor_id', $existingItem?->floor_id ?? ''));
    const presetRoomId = @json(old('room_id', $item->room_id ?? ''));

    /**
     * A single dropdown "panel" that behaves like a tree: top-level groups
     * (Category, or Floor) are listed; clicking one expands its children
     * (Asset Types, or Rooms) inline right underneath it, while other groups
     * stay collapsed. Clicking a child selects it and closes the panel.
     * With opts.flat, it's just a plain single-level clickable list (used for
     * OPEX Category, which has no Asset Type sub-level).
     * Reused for both CAPEX's Category > Asset Type, Floor > Room, and OPEX's
     * flat Category picker -- one shared component, one consistent look.
     */
    function initTreeSelect(rootEl, groups, opts) {
      const trigger = rootEl.querySelector('.tree-select-trigger');
      const labelEl = trigger.querySelector('.tree-select-label');
      const panel = rootEl.querySelector('.tree-select-panel');

      function render() {
        panel.innerHTML = '';
        if (!groups.length) {
          panel.innerHTML = '<div class="tree-select-empty">' + (opts.emptyText || 'Nothing set up yet.') + '</div>';
          return;
        }
        if (opts.flat) {
          groups.forEach(function (group) {
            const leafEl = document.createElement('div');
            leafEl.className = 'tree-select-leaf';
            leafEl.textContent = group.name;
            if (opts.selectedGroupId !== undefined && String(opts.selectedGroupId) === String(group.id)) {
              leafEl.classList.add('selected');
              labelEl.textContent = group.name;
            }
            leafEl.addEventListener('click', function (e) {
              e.stopPropagation();
              panel.querySelectorAll('.tree-select-leaf').forEach(l => l.classList.remove('selected'));
              leafEl.classList.add('selected');
              labelEl.textContent = group.name;
              rootEl.classList.remove('open');
              opts.onSelect(group.id, null, group.name, null);
            });
            panel.appendChild(leafEl);
          });
          return;
        }
        groups.forEach(function (group) {
          const groupEl = document.createElement('div');
          groupEl.className = 'tree-select-group';
          const groupLabel = document.createElement('div');
          groupLabel.className = 'tree-select-group-label';
          groupLabel.innerHTML = '<span>' + group.name + '</span><span class="caret">&#9656;</span>';
          groupLabel.addEventListener('click', function (e) {
            e.stopPropagation();
            const wasExpanded = groupEl.classList.contains('expanded');
            panel.querySelectorAll('.tree-select-group').forEach(g => g.classList.remove('expanded'));
            if (!wasExpanded) groupEl.classList.add('expanded');
          });
          groupEl.appendChild(groupLabel);

          const childrenEl = document.createElement('div');
          childrenEl.className = 'tree-select-children';
          const children = opts.childrenFor(group.id);
          if (!children.length) {
            childrenEl.innerHTML = '<div class="tree-select-empty">' + (opts.emptyChildText || 'None yet.') + '</div>';
          }
          children.forEach(function (child) {
            const leafEl = document.createElement('div');
            leafEl.className = 'tree-select-leaf';
            leafEl.textContent = child.name;
            if (opts.selectedChildId !== undefined && String(opts.selectedChildId) === String(child.id) && String(opts.selectedGroupId) === String(group.id)) {
              leafEl.classList.add('selected');
              groupEl.classList.add('expanded');
              labelEl.textContent = group.name + ' \u203A ' + child.name;
            }
            leafEl.addEventListener('click', function (e) {
              e.stopPropagation();
              panel.querySelectorAll('.tree-select-leaf').forEach(l => l.classList.remove('selected'));
              leafEl.classList.add('selected');
              labelEl.textContent = group.name + ' \u203A ' + child.name;
              rootEl.classList.remove('open');
              opts.onSelect(group.id, child.id, group.name, child.name);
            });
            childrenEl.appendChild(leafEl);
          });
          groupEl.appendChild(childrenEl);
          panel.appendChild(groupEl);
        });
      }

      trigger.addEventListener('click', function (e) {
        e.stopPropagation();
        document.querySelectorAll('.tree-select.open').forEach(el => { if (el !== rootEl) el.classList.remove('open'); });
        rootEl.classList.toggle('open');
      });
      panel.addEventListener('click', function (e) { e.stopPropagation(); });
      render();
    }

    document.addEventListener('click', function () {
      document.querySelectorAll('.tree-select.open').forEach(el => el.classList.remove('open'));
    });

    // --- CAPEX: Category > Asset Type ---
    const categoryTree = document.getElementById('category_tree');
    if (categoryTree) {
      const categoryHidden = document.getElementById('category_select');
      const typeHidden = document.getElementById('asset_type_choice');
      initTreeSelect(categoryTree, categoryList, {
        childrenFor: (catId) => (categoryTypeMap[catId] || []).map(name => ({ id: name, name })),
        selectedGroupId: presetCategoryId,
        selectedChildId: presetAssetType,
        emptyText: 'No categories set up yet.',
        emptyChildText: 'No asset types in this category yet.',
        onSelect: (catId, typeName) => {
          categoryHidden.value = catId;
          typeHidden.value = typeName;
        },
      });
    }

    // --- OPEX: flat Category picker (no Asset Type sub-level) ---
    const categoryTreeOpex = document.getElementById('category_tree_opex');
    if (categoryTreeOpex) {
      const categoryHidden = document.getElementById('category_select');
      initTreeSelect(categoryTreeOpex, categoryList, {
        flat: true,
        selectedGroupId: presetCategoryId,
        emptyText: 'No categories set up yet.',
        onSelect: (catId) => { categoryHidden.value = catId; },
      });
    }

    // --- CAPEX: Floor > Room (create mode only; edit mode uses the fixed floor + plain room select below) ---
    const floorTree = document.getElementById('floor_tree');
    if (floorTree) {
      const floorHidden = document.getElementById('floor_select');
      const roomHidden = document.getElementById('room_select');
      initTreeSelect(floorTree, floorList, {
        childrenFor: (floorId) => roomsByFloor[floorId] || [],
        selectedGroupId: presetFloorId,
        selectedChildId: presetRoomId,
        emptyText: 'No floors set up yet.',
        emptyChildText: 'No rooms on this floor yet.',
        onSelect: (floorId, roomId) => {
          floorHidden.value = floorId;
          roomHidden.value = roomId;
        },
      });
    }

    // --- CAPEX edit mode: floor is fixed, just populate the plain Room dropdown ---
    const roomSelectEdit = document.getElementById('room_select_edit');
    if (roomSelectEdit) {
      const fixedFloorId = document.getElementById('floor_select').value;
      const options = roomsByFloor[fixedFloorId] || [];
      roomSelectEdit.innerHTML = options.length ? '<option value="">Select room</option>' : '<option value="">No rooms set up for this floor yet</option>';
      options.forEach(function (room) {
        const o = document.createElement('option');
        o.value = room.id; o.textContent = room.name;
        if (String(presetRoomId) === String(room.id)) o.selected = true;
        roomSelectEdit.appendChild(o);
      });
    }
  })();
  </script>

  <style>
    .tree-select{position:relative}
    .tree-select-trigger{width:100%;text-align:left;display:flex;justify-content:space-between;align-items:center;cursor:pointer}
    .tree-select-trigger .chevron{opacity:.6;transition:transform .15s ease;margin-left:8px}
    .tree-select.open .tree-select-trigger{box-shadow:0 0 0 3px rgba(227,176,78,.18);border-color:var(--gold-500)}
    .tree-select.open .tree-select-trigger .chevron{transform:rotate(180deg)}
    .tree-select-panel{display:none;position:absolute;top:calc(100% + 6px);left:0;right:0;background:var(--surface-2);border:1px solid var(--line);border-radius:var(--r-md);box-shadow:var(--shadow-lg);z-index:50;max-height:320px;overflow-y:auto;padding:6px}
    .tree-select.open .tree-select-panel{display:block}
    .tree-select-group{border-radius:var(--r-sm);margin-bottom:2px}
    .tree-select-group-label{display:flex;align-items:center;justify-content:space-between;padding:9px 10px;cursor:pointer;font-size:13px;font-weight:600;color:var(--ink-900);border-radius:var(--r-sm)}
    .tree-select-group-label:hover{background:var(--surface)}
    .tree-select-group.expanded > .tree-select-group-label{background:var(--navy-800);color:#fff}
    .tree-select-group-label .caret{font-size:10px;opacity:.6;transition:transform .15s ease}
    .tree-select-group.expanded > .tree-select-group-label .caret{transform:rotate(90deg)}
    .tree-select-children{display:none;padding-left:16px;border-left:1px solid var(--line-2);margin:2px 0 4px 10px}
    .tree-select-group.expanded > .tree-select-children{display:block}
    .tree-select-leaf{padding:8px 10px;font-size:12.5px;cursor:pointer;border-radius:var(--r-sm);color:var(--ink-700)}
    .tree-select-leaf:hover{background:var(--surface);color:var(--ink-900)}
    .tree-select-leaf.selected{background:var(--gold-500);color:#141b1b;font-weight:700}
    .tree-select-empty{padding:9px 10px;font-size:12px;color:var(--ink-500)}
  </style>
