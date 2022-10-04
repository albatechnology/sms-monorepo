@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.itemProductUnit.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.item-product-units.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="product_unit_id">{{ trans('cruds.itemProductUnit.fields.product_unit') }}</label>
                <select class="form-control select2 {{ $errors->has('product_unit') ? 'is-invalid' : '' }}" name="product_unit_id" id="product_unit_id" required>
                    @foreach($product_units as $id => $product_unit)
                        <option value="{{ $id }}" {{ old('product_unit_id') == $id ? 'selected' : '' }}>{{ $product_unit }}</option>
                    @endforeach
                </select>
                @if($errors->has('product_unit'))
                    <span class="text-danger">{{ $errors->first('product_unit') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.itemProductUnit.fields.product_unit_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="item_id">{{ trans('cruds.itemProductUnit.fields.item') }}</label>
                <select class="form-control select2 {{ $errors->has('item') ? 'is-invalid' : '' }}" name="item_id" id="item_id" required>
                    @foreach($items as $id => $item)
                        <option value="{{ $id }}" {{ old('item_id') == $id ? 'selected' : '' }}>{{ $item }}</option>
                    @endforeach
                </select>
                @if($errors->has('item'))
                    <span class="text-danger">{{ $errors->first('item') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.itemProductUnit.fields.item_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="uom">{{ trans('cruds.itemProductUnit.fields.uom') }}</label>
                <input class="form-control {{ $errors->has('uom') ? 'is-invalid' : '' }}" type="number" name="uom" id="uom" value="{{ old('uom', '1') }}" step="1" required>
                @if($errors->has('uom'))
                    <span class="text-danger">{{ $errors->first('uom') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.itemProductUnit.fields.uom_helper') }}</span>
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