@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.productUnit.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.product-units.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.productUnit.fields.id') }}
                        </th>
                        <td>
                            {{ $productUnit->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productUnit.fields.product') }}
                        </th>
                        <td>
                            {{ $productUnit->product->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productUnit.fields.name') }}
                        </th>
                        <td>
                            {{ $productUnit->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productUnit.fields.description') }}
                        </th>
                        <td>
                            {{ $productUnit->description }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productUnit.fields.information') }}
                        </th>
                        <td>
                            {!! $productUnit->information !!}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productUnit.fields.detail') }}
                        </th>
                        <td>
                            {{ $productUnit->detail }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productUnit.fields.price') }}
                        </th>
                        <td>
                            {{ $productUnit->price }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productUnit.fields.is_active') }}
                        </th>
                        <td>
                            <input type="checkbox" disabled="disabled" {{ $productUnit->is_active ? 'checked' : '' }}>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.product-units.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        {{ trans('global.relatedData') }}
    </div>
    <ul class="nav nav-tabs" role="tablist" id="relationship-tabs">
        <li class="nav-item">
            <a class="nav-link" href="#product_unit_item_product_units" role="tab" data-toggle="tab">
                {{ trans('cruds.itemProductUnit.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#product_unit_order_details" role="tab" data-toggle="tab">
                {{ trans('cruds.orderDetail.title') }}
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane" role="tabpanel" id="product_unit_item_product_units">
            @includeIf('admin.productUnits.relationships.productUnitItemProductUnits', ['itemProductUnits' => $productUnit->productUnitItemProductUnits])
        </div>
        <div class="tab-pane" role="tabpanel" id="product_unit_order_details">
            @includeIf('admin.productUnits.relationships.productUnitOrderDetails', ['orderDetails' => $productUnit->productUnitOrderDetails])
        </div>
    </div>
</div>

@endsection