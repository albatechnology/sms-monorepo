@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.payment.title') }}
    </div>
    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.customer-deposits.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.payment.fields.id') }}
                        </th>
                        <td>
                            {{ $customerDeposit->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.payment.fields.amount') }}
                        </th>
                        <td>
                            {{ $customerDeposit->value }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.payment.fields.payment_type') }}
                        </th>
                        <td>
                            {{ $customerDeposit->payment_type->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.payment.fields.reference') }}
                        </th>
                        <td>
                            {{ $customerDeposit->description }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.payment.fields.added_by') }}
                        </th>
                        <td>
                            {{ $customerDeposit->added_by->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.payment.fields.approved_by') }}
                        </th>
                        <td>
                            {{ $customerDeposit->approved_by->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.payment.fields.proof') }}
                        </th>
                        <td>
                            @foreach($customerDeposit->proof as $key => $media)
                                <a href="{{ $media->getUrl() }}" target="_blank" style="display: inline-block">
                                    <img src="{{ $media->getUrl('thumb') }}" width="300">
                                </a>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.payment.fields.status') }}
                        </th>
                        <td>
                            {{ $customerDeposit->status?->description ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.payment.fields.reason') }}
                        </th>
                        <td>
                            {{ $customerDeposit->description }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.payment.fields.invoice') }}
                        </th>
                        <td>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.customer-deposits.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
