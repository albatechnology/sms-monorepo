@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.productCategoryCode.title') }}
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.product-category-codes.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.productCategoryCode.fields.id') }}
                        </th>
                        <td>
                            {{ $productCategoryCode->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productCategoryCode.fields.name') }}
                        </th>
                        <td>
                            {{ $productCategoryCode->name }}
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.product-category-codes.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>



@endsection