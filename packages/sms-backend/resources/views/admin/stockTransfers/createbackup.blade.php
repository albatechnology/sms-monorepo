@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.stockTransfer.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.stock-transfers.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="required"
                        for="stock_from_id">{{ trans('cruds.stockTransfer.fields.stock_from') }}</label>
                    <select class="form-control select2 {{ $errors->has('stock_from') ? 'is-invalid' : '' }}"
                        name="stock_from_id" id="stock_from_id" required>
                        @foreach ($stock_froms as $id => $stock_from)
                            <option value="{{ $id }}" {{ old('stock_from_id') == $id ? 'selected' : '' }}>
                                {{ $stock_from }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('stock_from'))
                        <span class="text-danger">{{ $errors->first('stock_from') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.stockTransfer.fields.stock_from_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required"
                        for="stock_to_id">{{ trans('cruds.stockTransfer.fields.stock_to') }}</label>
                    <select class="form-control select2 {{ $errors->has('stock_to') ? 'is-invalid' : '' }}"
                        name="stock_to_id" id="stock_to_id" required>
                        @foreach ($stock_tos as $id => $stock_to)
                            <option value="{{ $id }}" {{ old('stock_to_id') == $id ? 'selected' : '' }}>
                                {{ $stock_to }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('stock_to'))
                        <span class="text-danger">{{ $errors->first('stock_to') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.stockTransfer.fields.stock_to_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required"
                        for="requested_by_id">{{ trans('cruds.stockTransfer.fields.requested_by') }}</label>
                    <select class="form-control select2 {{ $errors->has('requested_by') ? 'is-invalid' : '' }}"
                        name="requested_by_id" id="requested_by_id" required>
                        @foreach ($requested_bies as $id => $requested_by)
                            <option value="{{ $id }}" {{ old('requested_by_id') == $id ? 'selected' : '' }}>
                                {{ $requested_by }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('requested_by'))
                        <span class="text-danger">{{ $errors->first('requested_by') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.stockTransfer.fields.requested_by_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="approved_by_id">{{ trans('cruds.stockTransfer.fields.approved_by') }}</label>
                    <select class="form-control select2 {{ $errors->has('approved_by') ? 'is-invalid' : '' }}"
                        name="approved_by_id" id="approved_by_id">
                        @foreach ($approved_bies as $id => $approved_by)
                            <option value="{{ $id }}" {{ old('approved_by_id') == $id ? 'selected' : '' }}>
                                {{ $approved_by }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('approved_by'))
                        <span class="text-danger">{{ $errors->first('approved_by') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.stockTransfer.fields.approved_by_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="amount">{{ trans('cruds.stockTransfer.fields.amount') }}</label>
                    <input class="form-control {{ $errors->has('amount') ? 'is-invalid' : '' }}" type="number"
                        name="amount" id="amount" value="{{ old('amount', '') }}" step="1" required>
                    @if ($errors->has('amount'))
                        <span class="text-danger">{{ $errors->first('amount') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.stockTransfer.fields.amount_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required"
                        for="item_from_id">{{ trans('cruds.stockTransfer.fields.item_from') }}</label>
                    <select class="form-control select2 {{ $errors->has('item_from') ? 'is-invalid' : '' }}"
                        name="item_from_id" id="item_from_id" required>
                        @foreach ($item_froms as $id => $item_from)
                            <option value="{{ $id }}" {{ old('item_from_id') == $id ? 'selected' : '' }}>
                                {{ $item_from }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('item_from'))
                        <span class="text-danger">{{ $errors->first('item_from') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.stockTransfer.fields.item_from_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required"
                        for="item_to_id">{{ trans('cruds.stockTransfer.fields.item_to') }}</label>
                    <select class="form-control select2 {{ $errors->has('item_to') ? 'is-invalid' : '' }}"
                        name="item_to_id" id="item_to_id" required>
                        @foreach ($item_tos as $id => $item_to)
                            <option value="{{ $id }}" {{ old('item_to_id') == $id ? 'selected' : '' }}>
                                {{ $item_to }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('item_to'))
                        <span class="text-danger">{{ $errors->first('item_to') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.stockTransfer.fields.item_to_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required"
                        for="item_code">{{ trans('cruds.stockTransfer.fields.item_code') }}</label>
                    <input class="form-control {{ $errors->has('item_code') ? 'is-invalid' : '' }}" type="text"
                        name="item_code" id="item_code" value="{{ old('item_code', '') }}" required>
                    @if ($errors->has('item_code'))
                        <span class="text-danger">{{ $errors->first('item_code') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.stockTransfer.fields.item_code_helper') }}</span>
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
