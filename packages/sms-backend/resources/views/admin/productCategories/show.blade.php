@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.productCategory.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.product-categories.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.productCategory.fields.id') }}
                        </th>
                        <td>
                            {{ $productCategory->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productCategory.fields.name') }}
                        </th>
                        <td>
                            {{ $productCategory->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productCategory.fields.description') }}
                        </th>
                        <td>
                            {{ $productCategory->description }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productCategory.fields.photo') }}
                        </th>
                        <td>
                            @if($productCategory->photo)
                                <a href="{{ $productCategory->photo->getUrl() }}" target="_blank" style="display: inline-block">
                                    <img src="{{ $productCategory->photo->getUrl('thumb') }}">
                                </a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productCategory.fields.parent') }}
                        </th>
                        <td>
                            {{ $productCategory->parent->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productCategory.fields.type') }}
                        </th>
                        <td>
                            {{ App\Models\ProductCategory::TYPE_SELECT[$productCategory->type] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productCategory.fields.company') }}
                        </th>
                        <td>
                            {{ $productCategory->company->name ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.product-categories.index') }}">
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
            <a class="nav-link" href="#parent_product_categories" role="tab" data-toggle="tab">
                {{ trans('cruds.productCategory.title') }}
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane" role="tabpanel" id="parent_product_categories">
            @includeIf('admin.productCategories.relationships.parentProductCategories', ['productCategories' => $productCategory->parentProductCategories])
        </div>
    </div>
</div>

@endsection