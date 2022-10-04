<div>
    <div class="form-group">
        <label class="required" for="order_id">{{ trans('cruds.shipment.fields.order') }}</label>

        <div wire:ignore>
            <select class="form-control select2 {{ $errors->has('order') ? 'is-invalid' : '' }}" name="order_id"
                id="orders" required>
                <option value="" disabled selected>-- select order --</option>
                @foreach ($orders as $day => $orderCollection)
                    <optgroup label="{{ $day }}">
                        @foreach ($orderCollection as $order)
                            <option value="{{ $order['id'] }}"
                                {{ old('order_id') == $order['id'] ? 'selected' : '' }}>
                                {{ $order['channel']['name'] }}
                                | {{ $order['customer']['first_name'] }} {{ $order['customer']['last_name'] }}
                                | {{ $order['invoice_number'] }}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>
        @if ($errors->has('order_id'))
            <span class="text-danger">{{ $errors->first('order_id') }}</span>
        @endif
        <span class="help-block">{{ trans('cruds.shipment.fields.order_helper') }}</span>
    </div>

    @if ($orderDetails)

        <table class="table table-bordered table-striped">
            <tbody>
                <tr>
                    <th>
                        {{ trans('cruds.order.fields.invoice_number') }}
                    </th>
                    <td>
                        {{ $order['invoice_number'] }}
                    </td>
                </tr>
                <tr>
                    <th>
                        {{ trans('cruds.order.fields.expected_delivery_date') }}
                    </th>
                    <td>
                        {{ $order['expected_shipping_datetime'] ? date('d-m-Y H:i:s', strtotime($order['expected_shipping_datetime'])) : '' }}
                    </td>
                </tr>
                <tr>
                    <th>
                        {{ trans('cruds.order.fields.customer') }}
                    </th>
                    <td>
                        {{ $order['customer']['first_name'] }} {{ $order['customer']['last_name'] }}
                    </td>
                </tr>
                <tr>
                    <th>
                        {{ trans('cruds.order.fields.channel') }}
                    </th>
                    <td>
                        {{ $order['channel']['name'] ?? '' }}
                    </td>
                </tr>

                <tr>
                    <th>
                        {{ trans('cruds.order.fields.shipping_address') }}
                    </th>
                    <td>
                        {{ \App\Services\HelperService::jsonAddressToString($order['records']['shipping_address']) ?? '' }}
                    </td>
                </tr>
            </tbody>
        </table>

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
                            <th scope="col">Order Quantity</th>
                            <th scope="col">Total Processed (Preparing/Delivering/Arrived)</th>
                            <th scope="col">Handle</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orderDetails as $detail)
                            <tr>
                                <th scope="row">{{ $detail->id }}</th>
                                <td>{{ $detail->records['product']['name'] }}</td>
                                <td>{{ $detail->quantity }}</td>
                                <td>
                                    {{ $detail->sumShippingQuantity() }} (
                                    {{ $detail->sumShipmentQuantity(\App\Enums\ShipmentStatus::PREPARING()) }} /
                                    {{ $detail->sumShipmentQuantity(\App\Enums\ShipmentStatus::DELIVERING()) }} /
                                    {{ $detail->sumShipmentQuantity(\App\Enums\ShipmentStatus::ARRIVED()) }} )

                                </td>
                                <td>
                                    <input required type="hidden" name="detail[{{ $loop->iteration }}][id]"
                                        value="{{ $detail->id }}">
                                    <input required type="number" min="0"
                                        name="detail[{{ $loop->iteration }}][quantity]"
                                        max="{{ $detail->quantity - $detail->sumShippingQuantity() }}" value="0">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <x-enum key='status' :model='app(\App\Models\Shipment::class)'></x-enum>

        <div class="form-group">
            <label for="note">{{ trans('cruds.shipment.fields.note') }}</label>
            <textarea class="form-control {{ $errors->has('note') ? 'is-invalid' : '' }}" name="note"
                id="note">{{ old('note') }}</textarea>
            @if ($errors->has('note'))
                <span class="text-danger">{{ $errors->first('note') }}</span>
            @endif
            <span class="help-block">{{ trans('cruds.shipment.fields.note_helper') }}</span>
        </div>

        <div class="form-group">
            <label for="reference">{{ trans('cruds.shipment.fields.reference') }}</label>
            <input class="form-control {{ $errors->has('reference') ? 'is-invalid' : '' }}" type="text"
                name="reference" id="reference" value="{{ old('reference', '') }}">
            @if ($errors->has('reference'))
                <span class="text-danger">{{ $errors->first('reference') }}</span>
            @endif
            <span class="help-block">{{ trans('cruds.shipment.fields.reference_helper') }}</span>
        </div>

        <div class="form-group">
            <button class="btn btn-danger" type="submit">
                {{ trans('global.save') }}
            </button>
        </div>


    @endif


</div>

@push('js')
    <script>
        document.addEventListener("livewire:load", () => {
            let el = $('#orders')
            initSelect()

            Livewire.hook('message.processed', (message, component) => {
                initSelect()
            })

            Livewire.on('setCategoriesSelect', values => {
                el.val(values).trigger('change.select2')
            })

            el.on('change', function(e) {
                //@this.set('order', el.select2("val"))
                Livewire.emit('setOrder', el.select2("val"))
            })

            function initSelect() {
                el.select2({
                    //placeholder: '{{ __('Select your option') }}',
                    allowClear: !el.attr('required'),
                })
            }
        })
    </script>
@endpush
