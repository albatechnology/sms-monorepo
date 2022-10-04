@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.productVersion.title') }}
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.product-versions.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.productVersion.fields.id') }}
                        </th>
                        <td>
                            {{ $productVersion->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productVersion.fields.name') }}
                        </th>
                        <td>
                            {{ $productVersion->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productVersion.fields.code') }}
                        </th>
                        <td>
                            {{ $productVersion->code }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productVersion.fields.height') }}
                        </th>
                        <td>
                            {{ $productVersion->height }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productVersion.fields.length') }}
                        </th>
                        <td>
                            {{ $productVersion->length }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productVersion.fields.width') }}
                        </th>
                        <td>
                            {{ $productVersion->width }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.productVersion.fields.photo') }}
                        </th>
                        <td>
                            @foreach($productVersion->photo as $key => $media)
                                <a href="{{ $media->getUrl() }}" target="_blank" style="display: inline-block">
                                    <img src="{{ $media->getUrl('thumb') }}">
                                </a>
                            @endforeach
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.product-versions.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>



@endsection