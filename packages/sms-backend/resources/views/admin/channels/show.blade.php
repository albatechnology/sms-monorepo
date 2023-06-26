@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.channel.title') }}
    </div>
    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.channels.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.channel.fields.id') }}
                        </th>
                        <td>
                            {{ $channel->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.channel.fields.name') }}
                        </th>
                        <td>
                            {{ $channel->name }}
                        </td>
                    </tr>
                    {{-- <tr>
                        <th>
                            {{ trans('cruds.channel.fields.channel_category') }}
                        </th>
                        <td>
                            {{ $channel->channel_category->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.channel.fields.company') }}
                        </th>
                        <td>
                            {{ $channel->company->name ?? '' }}
                        </td>
                    </tr> --}}
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.channels.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
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
            <a class="nav-link" href="#channel_catalogues" role="tab" data-toggle="tab">
                {{ trans('cruds.catalogue.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#channel_orders" role="tab" data-toggle="tab">
                {{ trans('cruds.order.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#channel_stocks" role="tab" data-toggle="tab">
                {{ trans('cruds.stock.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#channel_leads" role="tab" data-toggle="tab">
                {{ trans('cruds.lead.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#channels_products" role="tab" data-toggle="tab">
                {{ trans('cruds.product.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#channels_users" role="tab" data-toggle="tab">
                {{ trans('cruds.user.title') }}
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane" role="tabpanel" id="channel_catalogues">
            @includeIf('admin.channels.relationships.channelCatalogues', ['catalogues' => $channel->channelCatalogues])
        </div>
        <div class="tab-pane" role="tabpanel" id="channel_orders">
            @includeIf('admin.channels.relationships.channelOrders', ['orders' => $channel->channelOrders])
        </div>
        <div class="tab-pane" role="tabpanel" id="channel_stocks">
            @includeIf('admin.channels.relationships.channelStocks', ['stocks' => $channel->channelStocks])
        </div>
        <div class="tab-pane" role="tabpanel" id="channel_leads">
            @includeIf('admin.channels.relationships.channelLeads', ['leads' => $channel->channelLeads])
        </div>
        <div class="tab-pane" role="tabpanel" id="channels_products">
            @includeIf('admin.channels.relationships.channelsProducts', ['products' => $channel->channelsProducts])
        </div>
        <div class="tab-pane" role="tabpanel" id="channels_users">
            @includeIf('admin.channels.relationships.channelsUsers', ['users' => $channel->channelsUsers])
        </div>
    </div>
</div> --}}
@endsection
