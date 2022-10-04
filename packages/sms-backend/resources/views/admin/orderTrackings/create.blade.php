@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.orderTracking.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.order-trackings.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="order_id">{{ trans('cruds.orderTracking.fields.order') }}</label>
                <select class="form-control select2 {{ $errors->has('order') ? 'is-invalid' : '' }}" name="order_id" id="order_id">
                    @foreach($orders as $id => $order)
                        <option value="{{ $id }}" {{ old('order_id') == $id ? 'selected' : '' }}>{{ $order }}</option>
                    @endforeach
                </select>
                @if($errors->has('order'))
                    <span class="text-danger">{{ $errors->first('order') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.orderTracking.fields.order_helper') }}</span>
            </div>
            <div class="form-group">
                <label>{{ trans('cruds.orderTracking.fields.type') }}</label>
                <select class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}" name="type" id="type">
                    <option value disabled {{ old('type', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\OrderTracking::TYPE_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('type', '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('type'))
                    <span class="text-danger">{{ $errors->first('type') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.orderTracking.fields.type_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="context">{{ trans('cruds.orderTracking.fields.context') }}</label>
                <textarea class="form-control {{ $errors->has('context') ? 'is-invalid' : '' }}" name="context" id="context">{{ old('context') }}</textarea>
                @if($errors->has('context'))
                    <span class="text-danger">{{ $errors->first('context') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.orderTracking.fields.context_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="old_value">{{ trans('cruds.orderTracking.fields.old_value') }}</label>
                <textarea class="form-control {{ $errors->has('old_value') ? 'is-invalid' : '' }}" name="old_value" id="old_value">{{ old('old_value') }}</textarea>
                @if($errors->has('old_value'))
                    <span class="text-danger">{{ $errors->first('old_value') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.orderTracking.fields.old_value_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="new_value">{{ trans('cruds.orderTracking.fields.new_value') }}</label>
                <textarea class="form-control {{ $errors->has('new_value') ? 'is-invalid' : '' }}" name="new_value" id="new_value">{{ old('new_value') }}</textarea>
                @if($errors->has('new_value'))
                    <span class="text-danger">{{ $errors->first('new_value') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.orderTracking.fields.new_value_helper') }}</span>
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