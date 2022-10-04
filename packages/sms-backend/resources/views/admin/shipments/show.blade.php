@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.shipment.title') }}
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.shipments.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.shipment.fields.id') }}
                            </th>
                            <td>
                                {{ $shipment->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.shipment.fields.order') }}
                            </th>
                            <td>
                                {{ $shipment->order->reference ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.shipment.fields.fulfilled_by') }}
                            </th>
                            <td>
                                {{ $shipment->fulfilled_by->name ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.shipment.fields.status') }}
                            </th>
                            <td>
                                {{ App\Models\Shipment::STATUS_SELECT[$shipment->status->label] ?? $shipment->status->label }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.shipment.fields.note') }}
                            </th>
                            <td>
                                {{ $shipment->note }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.shipment.fields.reference') }}
                            </th>
                            <td>
                                {{ $shipment->reference }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.shipment.fields.estimated_delivery_date') }}
                            </th>
                            <td>
                                {{ $shipment->estimated_delivery_date }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.shipment.fields.arrived_at') }}
                            </th>
                            <td>
                                {{ $shipment->arrived_at }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.shipments.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>



@endsection
