@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.promo.title') }}
        </div>
        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.promo-categories.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                        <x-show-row :model="$promoCategory" key="id"></x-show-row>
                        <x-show-row :model="$promoCategory" key="name"></x-show-row>
                        <x-show-row :model="$promoCategory" key="description"></x-show-row>
                        <tr>
                            <th>
                                {{ trans('cruds.promo.fields.image') }}
                            </th>
                            <td>
                                @foreach ($promoCategory->image as $key => $media)
                                    <a href="{{ $media->getUrl() }}" target="_blank" style="display: inline-block">
                                        <img src="{{ $media->getUrl('thumb') }}">
                                    </a>
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.promo.fields.company') }}
                            </th>
                            <td>
                                {{ $promoCategory->company->name ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>Promo</th>
                            <td>
                                @if (isset($promoCategory->promos) && count($promoCategory->promos) > 0)
                                    <ol>
                                        @foreach ($promoCategory->promos as $promo)
                                            <li><a href="{{ route('admin.promos.show', $promo->id) }}">{{ $promo->name }}</a></li>
                                        @endforeach
                                    </ol>
                                @else
                                    <h3>-</h3>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.promo-categories.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
