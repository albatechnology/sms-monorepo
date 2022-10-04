<div>

    <div class="form-group">
        <label class="required" for="payment_category_id">{{ trans('cruds.payment.fields.payment_category') }}</label>
        <select wire:model="payment_category"
                class="form-control {{ $errors->has('payment_category') ? 'is-invalid' : '' }}"
                name="payment_category_id" id="payment_category_id" required>

            <option value="">-- choose payment_category --</option>
            @foreach ($payment_categories as $payment_category)
                <option value="{{ $payment_category->id }}" {{ old('payment_category_id') == $payment_category->id ? 'selected' : '' }}>{{ $payment_category->name }}</option>
            @endforeach
        </select>
        @if($errors->has('payment_category'))
            <span class="text-danger">{{ $errors->first('payment_category') }}</span>
        @endif
        <span class="help-block">{{ trans('cruds.payment.fields.payment_category_helper') }}</span>
    </div>


    <div class="form-group">
        <label class="required" for="payment_type_id">{{ trans('cruds.payment.fields.payment_type') }}</label>
        <select wire:model="payment_type"
                class="form-control select2 {{ $errors->has('payment_type') ? 'is-invalid' : '' }}"
                name="payment_type_id" id="payment_type_id" required>

            @if ($payment_types->count() == 0)
                <option value="">-- choose payment category first --</option>
            @endif
            @foreach($payment_types as $payment_type)
                <option value="{{ $payment_type->id }}" {{ old('payment_type_id') == $payment_type->id ? 'selected' : '' }} >{{ $payment_type->name }}</option>
            @endforeach
        </select>
        @if($errors->has('payment_type'))
            <span class="text-danger">{{ $errors->first('payment_type') }}</span>
        @endif
        <span class="help-block">{{ trans('cruds.payment.fields.payment_type_helper') }}</span>
    </div>
</div>