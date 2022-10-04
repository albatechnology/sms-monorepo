@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.orderDetail.title') }}
        </div>
        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.order-details.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                    <x-show-row :model="$orderDetail" key="id"></x-show-row>
                    <tr>
                        <th>
                            {{ trans('cruds.orderDetail.fields.order') }}
                        </th>
                        <td>
                            {{ $orderDetail->order->invoice_number ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.orderDetail.fields.product_unit') }}
                        </th>
                        <td>
                            {{ $orderDetail->product_unit->name ?? '' }}
                        </td>
                    </tr>
                    <x-show-row :model="$orderDetail" key="quantity"></x-show-row>
                    <x-show-row :model="$orderDetail" key="indent"></x-show-row>
                    <x-show-row :model="$orderDetail" key="unit_price" type="{{ \App\View\Components\ShowRow::TYPE_PRICE }}"></x-show-row>
                    <x-show-row :model="$orderDetail" key="total_discount" type="{{ \App\View\Components\ShowRow::TYPE_PRICE }}"></x-show-row>
                    <x-show-row :model="$orderDetail" key="total_price" type="{{ \App\View\Components\ShowRow::TYPE_PRICE }}"></x-show-row>
                    <x-show-row :model="$orderDetail" key="status"></x-show-row>
                    </tbody>
                </table>
                <div class="form-group">
                    @if(!$orderDetail->status->is(\App\Enums\OrderDetailStatus::FULFILLED))
                        <form method="POST" action="{{ route("admin.order-details.fulfil", $orderDetail->id) }}">
                            @csrf
                            <button class="btn btn-primary" type="submit"
                                    data-toggle="tooltip"
                                    title="Try to take stock to fulfil the indent">
                                Try Fulfil Indent
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{-- <div class="card">
        <div class="card-header">
            {{ trans('global.relatedData') }}
        </div>
        <ul class="nav nav-tabs" role="tablist" id="relationship-tabs">
            <li class="nav-item">
                <a class="nav-link" href="#order_details_targets" role="tab" data-toggle="tab">
                    {{ trans('cruds.target.title') }}
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane" role="tabpanel" id="order_details_targets">
                @includeIf('admin.orderDetails.relationships.orderDetailsTargets', ['targets' => $orderDetail->orderDetailsTargets])
            </div>
        </div>
    </div> --}}
@endsection
