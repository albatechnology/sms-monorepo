@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.shipment.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.shipments.update", [$shipment->id]) }}"
              enctype="multipart/form-data">
            @method('PUT')
            @csrf

            <div class="form-group">
                <label for="reference">{{ trans('cruds.shipment.fields.order') }}</label>
                <input disabled class="form-control" type="text" id="reference"
                       value="{{ $shipment->order->invoice_number }}">
                <span class="help-block">{{ trans('cruds.shipment.fields.order_helper') }}</span>
            </div>

            <div class="card">
                <div class="card-header">
                    {{ trans('cruds.orderDetail.title') }}
                </div>

                <div class="card-body">

                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Name</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Handle</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($shipment->orderDetails as $detail)
                            <tr>
                                <th scope="row">{{ $detail->id }}</th>
                                <td>{{ $detail->records['product']['name'] }}</td>
                                <td>{{ $detail->quantity }}</td>
                                <td>
                                    <input disabled type="number" value="{{ $detail->pivot->quantity }}">
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <x-enum key='status' :model='$shipment'></x-enum>

            <div class="form-group">
                <label for="note">{{ trans('cruds.shipment.fields.note') }}</label>
                <textarea class="form-control {{ $errors->has('note') ? 'is-invalid' : '' }}" name="note"
                          id="note">{{ old('note', $shipment->note) }}</textarea>
                @if($errors->has('note'))
                    <span class="text-danger">{{ $errors->first('note') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.shipment.fields.note_helper') }}</span>
            </div>

            <div class="form-group">
                <label for="reference">{{ trans('cruds.shipment.fields.reference') }}</label>
                <input class="form-control {{ $errors->has('reference') ? 'is-invalid' : '' }}" type="text"
                       name="reference"
                       id="reference" value="{{ old('reference', $shipment->reference) }}">
                @if($errors->has('reference'))
                    <span class="text-danger">{{ $errors->first('reference') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.shipment.fields.reference_helper') }}</span>
            </div>

            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection