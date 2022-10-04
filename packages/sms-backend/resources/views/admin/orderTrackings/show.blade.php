@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.orderTracking.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.order-trackings.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.orderTracking.fields.id') }}
                        </th>
                        <td>
                            {{ $orderTracking->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.orderTracking.fields.order') }}
                        </th>
                        <td>
                            {{ $orderTracking->order->reference ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.orderTracking.fields.type') }}
                        </th>
                        <td>
                            {{ App\Models\OrderTracking::TYPE_SELECT[$orderTracking->type] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.orderTracking.fields.context') }}
                        </th>
                        <td>
                            {{ $orderTracking->context }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.orderTracking.fields.old_value') }}
                        </th>
                        <td>
                            {{ $orderTracking->old_value }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.orderTracking.fields.new_value') }}
                        </th>
                        <td>
                            {{ $orderTracking->new_value }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.order-trackings.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection