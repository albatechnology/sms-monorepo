@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.voucher.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.vouchers.store') }}" class="form-loading">
                @csrf
                <div class="form-group">
                    <label class="required" for="company_id">{{ trans('cruds.generic.fields.company') }}</label>
                    <select wire:model="company_id" class="form-control {{ $errors->has('company_id') ? 'is-invalid' : '' }}"
                        name="company_id" id="company_id">
                        @foreach ($companies as $id => $name)
                            <option value="{{ $id }}" {{ $id == old('company_id') ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('company_id'))
                        <span class="text-danger">{{ $errors->first('company_id') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.generic.fields.company_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="id">Code</label>
                    <input type="text" name="id" required class="form-control" placeholder="Voucher Code" value="{{ old('id') }}">
                </div>
                <div class="form-group">
                    <label for="value">Value</label>
                    <input type="number" name="value" required class="form-control" placeholder="Value" value="{{ old('value') }}">
                </div>
                <x-input key='start_time' :model='app(\App\Models\Voucher::class)' type="datetime"></x-input>
                <x-input key='end_time' :model='app(\App\Models\Voucher::class)' type="datetime"></x-input>
                <div class="form-group">
                    <div class="form-check {{ $errors->has('is_active') ? 'is-invalid' : '' }}">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                        value="1" {{ old('is_active', 0) == 1 || old('is_active') === null ? 'checked' : '' }}>
                        <label class="form-check-label"
                        for="is_active">{{ trans('cruds.voucher.fields.is_active') }}</label>
                    </div>
                    @if($errors->has('is_active'))
                    <span class="text-danger">{{ $errors->first('is_active') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.voucher.fields.is_active_helper') }}</span>
                </div>
                <x-input key='min_order_price' :model='app(\App\Models\Voucher::class)' type="number"
                required="0"></x-input>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" class="form-control" id="description" rows="5" placeholder="Description">{{ old('description') }}</textarea>
                </div>
                <div class="form-group">
                    <button class="btn btn-danger" type="submit">
                        {{ trans('global.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
