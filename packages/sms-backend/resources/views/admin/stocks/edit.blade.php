@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{ trans('cruds.stock.title_singular') }}
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.stocks.update', [$stock->id]) }}" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="form-group">
                    <label for="stock">{{ trans('cruds.stock.fields.current_stock') }}</label>
                    <input class="form-control {{ $errors->has('stock') ? 'is-invalid' : '' }}" type="number" disabled
                        id="stock" value="{{ $stock->stock }}" step="1">
                    <span class="help-block">{{ trans('cruds.stock.fields.stock_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="indent">{{ trans('cruds.stock.fields.current_indent') }}</label>
                    <input class="form-control {{ $errors->has('indent') ? 'is-invalid' : '' }}" type="number" disabled
                        id="indent" value="{{ $stock->indent }}" step="1">
                </div>
                <x-input key="increment" value="0" :model="app(\App\Models\Stock::class)"
                    label-key="cruds.stock.fields.add_stock" type="number" min="0">
                </x-input>
                <div class="form-group">
                    <div class="form-check">
                        <label class="form-check-label"><input name="cut_indent" class="form-check-input" type="checkbox"
                                value="1"> Potong Indent</label>
                    </div>
                </div>
                {{-- <x-input key="increment_indent" value="0" :model="app(\App\Models\Stock::class)" label-key="cruds.stock.fields.add_indent" type="number" min="0"></x-input> --}}
                <div class="form-group">
                    <button class="btn btn-danger" type="submit">
                        {{ trans('global.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
