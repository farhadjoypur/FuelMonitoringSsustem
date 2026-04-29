{{-- Shared form fields — used by both Create & Edit modals --}}

{{-- Server-side error banner --}}
<div x-show="serverErrors.length" x-cloak class="error-banner">
    <p class="fw-semibold mb-0" style="font-size:13px;color:#991b1b;">
        <i class="bi bi-exclamation-triangle-fill me-1"></i> Please fix the following:
    </p>
    <ul>
        <template x-for="e in serverErrors" :key="e"><li x-text="e"></li></template>
    </ul>
</div>

<div class="row g-3">

    <div class="col-md-6">
        <label class="field-label req">Depot Name</label>
        <input type="text" x-model.trim="form.depot_name"
               @blur="touch('depot_name')" :class="fieldClass('depot_name')"
               class="form-control" placeholder="e.g. Chittagong Main Depot">
        <p class="field-error" x-show="errors.depot_name" x-text="errors.depot_name"></p>
    </div>

    <div class="col-md-6">
        <label class="field-label req">Depot Code</label>
        <input type="text" x-model.trim="form.depot_code"
               @blur="touch('depot_code')" :class="fieldClass('depot_code')"
               class="form-control" placeholder="e.g. DEP-CTG-01">
        <p class="field-error" x-show="errors.depot_code" x-text="errors.depot_code"></p>
    </div>

    <div class="col-md-6">
        <label class="field-label req">District</label>
        <select x-model="form.district"
                @change="touch('district')" :class="fieldClass('district')"
                class="form-select">
            <option value="">— Select District —</option>
            @foreach ($locations['divisions'] as $div)
                <optgroup label="{{ $div['name_en'] }}">
                    @foreach ($div['districts'] as $d)
                        <option value="{{ $d['name_en'] }}">{{ $d['name_en'] }}</option>
                    @endforeach
                </optgroup>
            @endforeach
        </select>
        <p class="field-error" x-show="errors.district" x-text="errors.district"></p>
    </div>

    <div class="col-md-6">
        <label class="field-label req">Contact Number</label>
        <input type="text" x-model.trim="form.contact_number"
               @blur="touch('contact_number')" :class="fieldClass('contact_number')"
               class="form-control" placeholder="+8801XXXXXXXXX">
        <p class="field-error" x-show="errors.contact_number" x-text="errors.contact_number"></p>
    </div>

    <div class="col-md-6">
        <label class="field-label">Email <span class="text-muted fw-normal">(optional)</span></label>
        <input type="email" x-model.trim="form.email"
               @blur="touch('email')" :class="fieldClass('email')"
               class="form-control" placeholder="depot@example.com">
        <p class="field-error" x-show="errors.email" x-text="errors.email"></p>
    </div>

    <div class="col-md-6">
        <label class="field-label req">Capacity</label>
        <div class="input-group">
            <input type="number" step="0.01" min="1"
                   x-model="form.capacity"
                   @blur="touch('capacity')" :class="fieldClass('capacity')"
                   class="form-control" placeholder="50000">
            <span class="input-group-text" style="border-radius:0 8px 8px 0;font-size:.82rem;">Litres</span>
        </div>
        <p class="field-error" x-show="errors.capacity" x-text="errors.capacity"></p>
    </div>

    <div class="col-md-6">
        <label class="field-label">Number of Tanks</label>
        <input type="number" min="0" x-model="form.number_of_tanks"
               class="form-control" placeholder="e.g. 8">
    </div>

    <div class="col-md-6">
        <label class="field-label">Status</label>
        <select x-model="form.status" class="form-select">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
    </div>

    <div class="col-12">
        <label class="field-label">Full Address</label>
        <textarea x-model="form.full_address" class="form-control" rows="2"
                  placeholder="Full depot address…"></textarea>
    </div>

    <div class="col-12">
        <label class="field-label">Remarks / Notes</label>
        <textarea x-model="form.remarks" class="form-control" rows="2"
                  placeholder="Any additional notes…"></textarea>
    </div>

</div>