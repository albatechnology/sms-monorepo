@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.itemProductUnit.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.item-product-units.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.itemProductUnit.fields.id') }}
                        </th>
                        <td>
                            {{ $itemProductUnit->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.itemProductUnit.fields.product_unit') }}
                        </th>
                        <td>
                            {{ $itemProductUnit->product_unit->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.itemProductUnit.fields.item') }}
                        </th>
                        <td>
                            {{ $itemProductUnit->item->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.itemProductUnit.fields.uom') }}
                        </th>
                        <td>
                            {{ $itemProductUnit->uom }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.item-product-units.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection