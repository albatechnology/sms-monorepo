@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.promo.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.promos.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>

                    <x-show-row :model="$promo" key="id"></x-show-row>
                    <x-show-row :model="$promo" key="name"></x-show-row>
                    <x-show-row :model="$promo" key="description"></x-show-row>
                    <tr>
                        <th>
                            {{ trans('cruds.promo.fields.image') }}
                        </th>
                        <td>
                            @foreach($promo->image as $key => $media)
                                <a href="{{ $media->getUrl() }}" target="_blank" style="display: inline-block">
                                    <img src="{{ $media->getUrl('thumb') }}">
                                </a>
                            @endforeach
                        </td>
                    </tr>
                    <x-show-row :model="$promo" key="start_time"></x-show-row>
                    <x-show-row :model="$promo" key="end_time"></x-show-row>
                    {{-- <tr>
                        <th>
                            {{ trans('cruds.promo.fields.company') }}
                        </th>
                        <td>
                            {{ $promo->company->name ?? '' }}
                        </td>
                    </tr> --}}
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.promos.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection
