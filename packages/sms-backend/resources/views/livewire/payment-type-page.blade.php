<div>

    <div class="form-group">
        <label class="required" for="company_id">{{ trans('cruds.paymentType.fields.company') }}</label>
        <select wire:model="company" class="form-control {{ $errors->has('company') ? 'is-invalid' : '' }}"
                name="company_id" id="company_id" required>

            <option value="">-- choose company --</option>
            @foreach ($companies as $company)
                <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
            @endforeach
        </select>
        @if($errors->has('company'))
            <span class="text-danger">{{ $errors->first('company') }}</span>
        @endif
        <span class="help-block">{{ trans('cruds.paymentType.fields.company_helper') }}</span>
    </div>

    <div class="form-group">
        <label class="required"
               for="payment_category_id">{{ trans('cruds.paymentType.fields.payment_category') }}</label>
        <select required wire:model="payment_category"
                class="form-control {{ $errors->has('payment_category') ? 'is-invalid' : '' }}"
                name="payment_category_id" id="payment_category_id">
            @if ($payment_categories->count() == 0)
                <option value="">-- choose company first --</option>
            @endif
            @foreach ($payment_categories as $payment_category)
                <option value="{{ $payment_category->id }}" {{ old('payment_category_id') == $payment_category->id ? 'selected' : '' }}>{{ $payment_category->name }}</option>
            @endforeach
        </select>
        @if($errors->has('payment_category'))
            <span class="text-danger">{{ $errors->first('payment_category') }}</span>
        @endif
        <span class="help-block">{{ trans('cruds.paymentType.fields.payment_category_helper') }}</span>
    </div>


</div>
