@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.product.title') }}
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.products.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.product.fields.id') }}
                            </th>
                            <td>
                                {{ $product->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.product.fields.company') }}
                            </th>
                            <td>
                                {{ $product->company->name ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>Product Category</th>
                            <td>
                                {{ $product->productCategory->name ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>Product Brand</th>
                            <td>
                                {{ $product->brand->name ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.product.fields.name') }}
                            </th>
                            <td>
                                {{ $product->name }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.product.fields.description') }}
                            </th>
                            <td>
                                {!! $product->description !!}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.product.fields.tag') }}
                            </th>
                            <td>
                                @foreach (explode(',', $product->tags ?? []) as $tag)
                                    <span class="badge badge-info">{{$tag}}</span>
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.product.fields.price') }}
                            </th>
                            <td>
                                {{ rupiah($product->price) }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.product.fields.photo') }}
                            </th>
                            <td>
                                @foreach ($product->photo as $key => $media)
                                    <a href="{{ $media->getUrl() }}" target="_blank" style="display: inline-block">
                                        <img src="{{ $media->getUrl('thumb') }}">
                                    </a>
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.product.fields.is_active') }}
                            </th>
                            <td>
                                <input type="checkbox" disabled="disabled" {{ $product->is_active ? 'checked' : '' }}>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.products.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="card">
    <div class="card-header">
        {{ trans('global.relatedData') }}
    </div>
    <ul class="nav nav-tabs" role="tablist" id="relationship-tabs">
        <li class="nav-item">
            <a class="nav-link" href="#product_product_units" role="tab" data-toggle="tab">
                {{ trans('cruds.productUnit.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#products_activities" role="tab" data-toggle="tab">
                {{ trans('cruds.activity.title') }}
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane" role="tabpanel" id="product_product_units">
            @includeIf('admin.products.relationships.productProductUnits', ['productUnits' => $product->productProductUnits])
        </div>
        <div class="tab-pane" role="tabpanel" id="products_activities">
            @includeIf('admin.products.relationships.productsActivities', ['activities' => $product->productsActivities])
        </div>
    </div>
</div> --}}
@endsection
