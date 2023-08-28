@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{ trans('cruds.lead.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.leads.update', [$lead->id]) }}" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                {{-- <div class="form-group">
                    <label class="required">{{ trans('cruds.lead.fields.sales') }}</label>
                    <select class="form-control select2 {{ $errors->has('sales') ? 'is-invalid' : '' }}" name="sales"
                        id="sales">
                        @foreach ($users as $id => $sales)
                            <option value="{{ $id }}"
                                {{ (old('sales') ? old('sales') : $lead->user->id ?? '') == $id ? 'selected' : '' }}>
                                {{ $sales }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('sales'))
                        <span class="text-danger">{{ $errors->first('sales') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.lead.fields.type_helper') }}</span>
                </div> --}}
                {{-- <div class="form-group">
                    <label class="required">{{ trans('cruds.lead.fields.type') }}</label>
                    <select class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}" name="type" id="type"
                        required>
                        <option value disabled {{ old('type', null) === null ? 'selected' : '' }}>
                            {{ trans('global.pleaseSelect') }}</option>
                        @foreach (App\Enums\LeadType::getInstances() as $enum)
                            <option value="{{ $enum->value }}"
                                {{ old('type', $lead->type->value) === (int) $enum->value ? 'selected' : '' }}>
                                {{ $enum->label }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('type'))
                        <span class="text-danger">{{ $errors->first('type') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.lead.fields.type_helper') }}</span>
                </div> --}}
                {{-- <div class="form-group">
                    <label class="required">Company</label>
                    <input type="text" class="form-control" value="{{ $lead->channel?->company->name ?? "" }}" disabled>
                </div> --}}
                <div class="form-group">
                    <label class="required">Channel</label>
                    <input type="text" class="form-control" value="{{ $lead->channel?->name ?? "" }}" disabled>
                </div>
                <div class="form-group">
                    <label class="required">{{ trans('cruds.lead.fields.type') }}</label>
                    <input type="text" class="form-control" value="{{ $lead->type?->description ?? "" }}" disabled>
                </div>
                <div class="form-group">
                    <label class="required">Product Brand</label>
                    <input type="text" class="form-control" value="{{ $lead->productBrand?->name ?? "" }}" disabled>
                </div>
                {{-- <div class="form-group">
                    <label for="user_referral_id">Sales Referral</label>
                    <select class="form-control select2 {{ $errors->has('user_referral_id') ? 'is-invalid' : '' }}"
                        name="user_referral_id" id="user_referral_id">
                        @foreach ($userReferrals as $id => $name)
                            <option value="{{ $id }}" {{ $lead->user_referral_id == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    @if ($errors->has('user_referral_id'))
                        <span class="text-danger">{{ $errors->first('user_referral_id') }}</span>
                    @endif
                </div> --}}
                <div class="form-group">
                    <label class="required">Customer</label>
                    <input type="text" class="form-control" value="{{ $lead->customer?->fullName ?? "" }}" disabled>
                </div>
                <div class="form-group">
                    <label class="required">{{ trans('cruds.lead.fields.status') }}</label>
                    <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status"
                        id="status" required>
                        <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>
                            {{ trans('global.pleaseSelect') }}</option>
                        @foreach (\App\Enums\LeadStatus::getInstances() as $enum)
                            <option value="{{ $enum->value }}"
                                {{ old('status', $lead->status->value) === (int) $enum->value ? 'selected' : '' }}>
                                {{ $enum->label }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('status'))
                        <span class="text-danger">{{ $errors->first('status') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.lead.fields.status_helper') }}</span>
                </div>
                {{-- <div class="form-group">
                    <div class="form-check {{ $errors->has('is_new_customer') ? 'is-invalid' : '' }}">
                        <input type="hidden" name="is_new_customer" value="0">
                        <input class="form-check-input" type="checkbox" name="is_new_customer" id="is_new_customer"
                            value="1" {{ $lead->is_new_customer || old('is_new_customer', 0) === 1 ? 'checked' : '' }}>
                        <label class="form-check-label"
                            for="is_new_customer">{{ trans('cruds.lead.fields.is_new_customer') }}</label>
                    </div>
                    @if ($errors->has('is_new_customer'))
                        <span class="text-danger">{{ $errors->first('is_new_customer') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.lead.fields.is_new_customer_helper') }}</span>
                </div> --}}
                <div class="form-group">
                    <label for="label">{{ trans('cruds.lead.fields.label') }}</label>
                    <input class="form-control {{ $errors->has('label') ? 'is-invalid' : '' }}" type="text" name="label"
                        id="label" value="{{ old('label', $lead->label) }}">
                    @if ($errors->has('label'))
                        <span class="text-danger">{{ $errors->first('label') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.lead.fields.label_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="interest">{{ trans('cruds.lead.fields.interest') }}</label>
                    <input class="form-control {{ $errors->has('interest') ? 'is-invalid' : '' }}" type="text"
                        name="interest" id="interest" value="{{ $lead->interest }}">
                    @if ($errors->has('interest'))
                        <span class="text-danger">{{ $errors->first('interest') }}</span>
                    @endif
                </div>
                {{-- <div class="form-group">
                    <label for="customer_id">{{ trans('cruds.lead.fields.customer') }}</label>
                    <select class="form-control select2 {{ $errors->has('customer') ? 'is-invalid' : '' }}" name="customer_id" id="customer_id">
                            <option value="{{ $lead->customer->id }}" selected>{{ $lead->customer->first_name .' '.$lead->customer->last_name }}</option>
                    </select>
                    @if ($errors->has('customer'))
                        <span class="text-danger">{{ $errors->first('customer') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.lead.fields.customer_helper') }}</span>
                </div> --}}
                {{-- <div class="form-group">
                    <label class="required" for="channel_id">{{ trans('cruds.lead.fields.channel') }}</label>
                    <select class="form-control select2 {{ $errors->has('channel') ? 'is-invalid' : '' }}"
                        name="channel_id" id="channel_id" required>
                        @foreach ($channels as $id => $channel)
                            <option value="{{ $id }}"
                                {{ (old('channel_id') ? old('channel_id') : $lead->channel->id ?? '') == $id ? 'selected' : '' }}>
                                {{ $channel }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('channel'))
                        <span class="text-danger">{{ $errors->first('channel') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.lead.fields.channel_helper') }}</span>
                </div> --}}
                <div class="form-group">
                    <button class="btn btn-danger" type="submit">
                        {{ trans('global.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $(document).ready(function() {
            // $('#customer_id').select2({
            //     placeholder: 'Search by name, email, or phone',
            //     minimumInputLength: 2,
            //     ajax: {
            //         url: "{{ url('admin/customers/ajax-get-customers') }}?has_address=1",
            //         dataType: 'json',
            //         delay: 250,
            //         processResults: function(data) {
            //             return {
            //                 results: $.map(data, function(item) {
            //                     return {
            //                         text: item.name,
            //                         id: item.id
            //                     }
            //                 })
            //             };
            //         },
            //         cache: true
            //     }
            // });
        });
    </script>
@endpush()
