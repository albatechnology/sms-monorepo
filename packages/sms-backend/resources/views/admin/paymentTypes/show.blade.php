@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.paymentType.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.payment-types.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.paymentType.fields.id') }}
                        </th>
                        <td>
                            {{ $paymentType->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.paymentType.fields.name') }}
                        </th>
                        <td>
                            {{ $paymentType->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.paymentType.fields.payment_category') }}
                        </th>
                        <td>
                            {{ $paymentType->payment_category->name ?? '' }}
                        </td>
                    </tr>
                    {{-- <tr>
                        <th>
                            {{ trans('cruds.paymentType.fields.require_approval') }}
                        </th>
                        <td>
                            <input type="checkbox" disabled="disabled" {{ $paymentType->require_approval ? 'checked' : '' }}>
                        </td>
                    </tr> --}}
                    {{-- <tr>
                        <th>
                            {{ trans('cruds.paymentType.fields.company') }}
                        </th>
                        <td>
                            {{ $paymentType->company->name ?? '' }}
                        </td>
                    </tr> --}}
                    <tr>
                        <th>
                            {{ trans('cruds.paymentType.fields.photo') }}
                        </th>
                        <td>
                            @foreach($paymentType->photo as $key => $media)
                                <a href="{{ $media->getUrl() }}" target="_blank" style="display: inline-block">
                                    <img src="{{ $media->getUrl('thumb') }}">
                                </a>
                            @endforeach
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.payment-types.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        {{ trans('global.relatedData') }}
    </div>
    <ul class="nav nav-tabs" role="tablist" id="relationship-tabs">
        <li class="nav-item">
            <a class="nav-link" href="#payment_type_payments" role="tab" data-toggle="tab">
                {{ trans('cruds.payment.title') }}
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane" role="tabpanel" id="payment_type_payments">
            @includeIf('admin.paymentTypes.relationships.paymentTypePayments', ['payments' => $paymentType->paymentTypePayments])
        </div>
    </div>
</div>
@endsection
