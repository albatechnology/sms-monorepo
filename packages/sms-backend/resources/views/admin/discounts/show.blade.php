@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.discount.title') }}
    </div>
    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.discounts.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                <x-show-row :model="$discount" key="id"></x-show-row>
                <x-show-row :model="$discount" key="name"></x-show-row>
                <x-show-row :model="$discount" key="description"></x-show-row>
                <x-show-row :model="$discount" key="type" value="{{ $discount->type->description }}"></x-show-row>
                <x-show-row :model="$discount" key="activation_code"></x-show-row>
                <x-show-row :model="$discount" key="value" value="{{rupiah($discount->value)}}"></x-show-row>
                <x-show-row :model="$discount" key="scope" value="{{ $discount->scope->description }}"></x-show-row>
                <x-show-row :model="$discount" key="start_time"></x-show-row>
                <x-show-row :model="$discount" key="end_time"></x-show-row>
                <tr>
                    <th>
                        {{ trans('cruds.discount.fields.is_active') }}
                    </th>
                    <td>
                        <input type="checkbox" disabled="disabled" {{ $discount->is_active ? 'checked' : '' }}>
                    </td>
                </tr>
                <x-show-row :model="$discount" key="max_discount_price_per_order" type="price"></x-show-row>
                <x-show-row :model="$discount" key="max_use_per_customer"></x-show-row>
                <x-show-row :model="$discount" key="min_order_price" type="price"></x-show-row>
                <x-show-row :model="$discount" key="company" value="{{ $discount->subscribtionUser?->name ?? '' }}"></x-show-row>
                {{-- <x-show-row :model="$discount" key="product_brand" value="{{ $discount->productBrand?->name }}"></x-show-row> --}}
                {{-- <x-show-row :model="$discount" key="product_category" value="{{ $discount->product_category?->description }}"></x-show-row> --}}
                @if (!empty($discount->product_unit_ids))
                    <tr>
                        <th>Product Units</th>
                        <td>
                            <ol>
                                @php
                                    $productUnits = \App\Models\ProductUnit::whereIn('id', $discount->product_unit_ids)->pluck('name','id')->all();
                                @endphp
                                @foreach ($productUnits as $id => $name)
                                <li><a href="{{ route('admin.product-units.show', $id) }}">{{$name}}</a></li>
                                @endforeach
                            </ol>
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.discounts.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
