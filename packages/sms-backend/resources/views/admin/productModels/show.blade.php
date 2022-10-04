@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.productModel.title') }}
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.product-models.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.productModel.fields.id') }}
                        </th>
                        <td>
                            {{ $productModel->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productModel.fields.name') }}
                        </th>
                        <td>
                            {{ $productModel->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productModel.fields.description') }}
                        </th>
                        <td>
                            {{ $productModel->description }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productModel.fields.photo') }}
                        </th>
                        <td>
                            @foreach($productModel->photo as $key => $media)
                                <a href="{{ $media->getUrl() }}" target="_blank" style="display: inline-block">
                                    <img src="{{ $media->getUrl('thumb') }}">
                                </a>
                            @endforeach
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.product-models.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>



@endsection