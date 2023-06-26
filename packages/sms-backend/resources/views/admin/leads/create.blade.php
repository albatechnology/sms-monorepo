@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.lead.title_singular') }}
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.leads.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" value="3">
                {{-- <div class="form-group">
                    <label class="required" for="company_id">{{ trans('cruds.user.fields.company') }}</label>
                    <select class="form-control select2 {{ $errors->has('company_id') ? 'is-invalid' : '' }}"
                        name="company_id" id="company_id" required>
                        @foreach ($companies as $id => $name)
                            <option value="{{ $id }}" {{ old('company_id') == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    @if ($errors->has('company_id'))
                        <span class="text-danger">{{ $errors->first('company_id') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.user.fields.company_helper') }}</span>
                </div> --}}
                <div class="form-group">
                    <label for="channel_id">{{ trans('cruds.user.fields.channels') }}</label>
                    <select class="form-control select2 {{ $errors->has('channel_id') ? 'is-invalid' : '' }}" name="channel_id" id="channel_id" data-placeholder="Select channel" data-allow-clear="true">
                        @foreach ($channels as $id => $name)
                        <option value="{{ $id }}" {{ old('channel_id') == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                    </select>
                    @if ($errors->has('channel_id'))
                        <span class="text-danger">{{ $errors->first('channel_id') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.user.fields.channels_helper') }}</span>
                </div>
                <div class="form-group">
                    <label>Product Brand</label>
                    <select name="product_brand_ids[]" id="product_brand_ids" class="form-control select2 @error('product_brand_ids') is-invalid @enderror" multiple data-placeholder="Select Product Brands">
                        @foreach ($productBrands as $id => $name)
                        <option value="{{ $id }}">
                            {{ $name }}
                        </option>
                    @endforeach
                    </select>
                    @error('product_brand_ids')
                        <span class="error invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                {{-- <div class="form-group">
                    <label for="user_referral_id">Sales Referral</label>
                    <select class="form-control select2 {{ $errors->has('user_referral_id') ? 'is-invalid' : '' }}"
                        name="user_referral_id" id="user_referral_id">
                        @foreach ($userReferrals as $id => $name)
                            <option value="{{ $id }}" {{ old('user_referral_id') == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    @if ($errors->has('user_referral_id'))
                        <span class="text-danger">{{ $errors->first('user_referral_id') }}</span>
                    @endif
                </div> --}}
                {{-- <div class="form-group">
                    <label class="required">{{ trans('cruds.lead.fields.type') }}</label>
                    <select class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}" name="type" id="type"
                        required>
                        <option value disabled {{ old('type', null) === null ? 'selected' : '' }}>
                            {{ trans('global.pleaseSelect') }}</option>
                        @foreach (App\Enums\LeadType::getInstances() as $enum)
                            <option value="{{ $enum->value }}"
                                {{ old('type', App\Enums\LeadType::getDefaultValue()) === (string) $enum->value ? 'selected' : '' }}>
                                {{ $enum->label }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('type'))
                        <span class="text-danger">{{ $errors->first('type') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.lead.fields.type_helper') }}</span>
                </div> --}}
                <div class="form-group">
                    <label for="label">{{ trans('cruds.lead.fields.label') }}</label>
                    <input class="form-control {{ $errors->has('label') ? 'is-invalid' : '' }}" type="text"
                        name="label" id="label" value="{{ old('label', '') }}">
                    @if ($errors->has('label'))
                        <span class="text-danger">{{ $errors->first('label') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.lead.fields.label_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="interest">{{ trans('cruds.lead.fields.interest') }}</label>
                    <input class="form-control {{ $errors->has('interest') ? 'is-invalid' : '' }}" type="text"
                        name="interest" id="interest" value="{{ old('interest', '') }}">
                    @if ($errors->has('interest'))
                        <span class="text-danger">{{ $errors->first('interest') }}</span>
                    @endif
                </div>
                <div class="form-group">
                    <label class="required">Lead Category</label>
                    <select class="form-control {{ $errors->has('lead_category_id') ? 'is-invalid' : '' }}"
                        name="lead_category_id" id="lead_category_id" required>
                        <option value disabled {{ old('lead_category_id', null) === null ? 'selected' : '' }}>
                            {{ trans('global.pleaseSelect') }}</option>
                        @foreach ($leadCategories as $id => $name)
                            <option value="{{ $id }}" {{ old('lead_category_id') == $id ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('lead_category_id'))
                        <span class="text-danger">{{ $errors->first('lead_category_id') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.lead.fields.type_helper') }}</span>
                </div>
                <div class="form-group">
                    <label>Sub Lead Category</label>
                    <select class="form-control {{ $errors->has('sub_lead_category_id') ? 'is-invalid' : '' }}"
                        name="sub_lead_category_id" id="sub_lead_category_id" disabled>
                    </select>
                    @if ($errors->has('sub_lead_category_id'))
                        <span class="text-danger">{{ $errors->first('sub_lead_category_id') }}</span>
                    @endif
                </div>
                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_new_customer" id="is_new_customer"
                            value="1">
                        <label class="form-check-label"
                            for="is_new_customer">{{ trans('cruds.lead.fields.is_new_customer') }}</label>
                    </div>
                    @if ($errors->has('is_new_customer'))
                        <span class="text-danger">{{ $errors->first('is_new_customer') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.lead.fields.is_new_customer_helper') }}</span>
                </div>
                <div class="form-group" id="select_customer_id">
                    <label for="customer_id">{{ trans('cruds.lead.fields.customer') }}</label>
                    <select class="form-control {{ $errors->has('customer_id') ? 'is-invalid' : '' }}" name="customer_id"
                        id="customer_id"></select>
                    @if ($errors->has('customer'))
                        <span class="text-danger">{{ $errors->first('customer') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.lead.fields.customer_helper') }}</span>
                </div>
                <div id="form_new_customer" style="display: none;">
                    <hr>
                    <h6>{{ trans('global.customer_information') }}</h6>
                    <div class="form-group">
                        <label>{{ trans('cruds.customer.fields.title') }}</label>
                        <select class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" name="title"
                            id="title">
                            <option value disabled {{ old('title', null) === null ? 'selected' : '' }}>
                                {{ trans('global.pleaseSelect') }}</option>
                            @foreach (\App\Enums\PersonTitle::getInstances() as $key => $pt)
                                <option value="{{ $pt->value }}" {{ old('title') == $pt->value ? 'selected' : '' }}>
                                    {{ $pt->description }}
                                </option>
                            @endforeach
                        </select>
                        @if ($errors->has('title'))
                            <span class="text-danger">{{ $errors->first('title') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.customer.fields.title_helper') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="required" for="first_name">{{ trans('cruds.customer.fields.first_name') }}</label>
                        <input class="form-control {{ $errors->has('first_name') ? 'is-invalid' : '' }}" type="text"
                            name="first_name" id="first_name" value="{{ old('first_name', '') }}">
                        @if ($errors->has('first_name'))
                            <span class="text-danger">{{ $errors->first('first_name') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.customer.fields.first_name_helper') }}</span>
                    </div>
                    <div class="form-group">
                        <label for="last_name">{{ trans('cruds.customer.fields.last_name') }}</label>
                        <input class="form-control {{ $errors->has('last_name') ? 'is-invalid' : '' }}" type="text"
                            name="last_name" id="last_name" value="{{ old('last_name', '') }}">
                        @if ($errors->has('last_name'))
                            <span class="text-danger">{{ $errors->first('last_name') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.customer.fields.last_name_helper') }}</span>
                    </div>
                    <div class="form-group">
                        <label for="date_of_birth">{{ trans('cruds.customer.fields.date_of_birth') }}</label>
                        <input class="form-control date {{ $errors->has('date_of_birth') ? 'is-invalid' : '' }}"
                            type="text" name="date_of_birth" id="date_of_birth"
                            value="{{ old('date_of_birth', '') }}">
                        @if ($errors->has('date_of_birth'))
                            <span class="text-danger">{{ $errors->first('date_of_birth') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="email">{{ trans('cruds.customer.fields.email') }}</label>
                        <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" type="text"
                            name="email" id="email" value="{{ old('email', '') }}">
                        @if ($errors->has('email'))
                            <span class="text-danger">{{ $errors->first('email') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.customer.fields.email_helper') }}</span>
                    </div>
                    <div class="form-group">
                        <label for="phone">{{ trans('cruds.customer.fields.phone') }}</label>
                        <input class="form-control {{ $errors->has('phone') ? 'is-invalid' : '' }}" type="text"
                            name="phone" id="phone" value="{{ old('phone', '') }}">
                        @if ($errors->has('phone'))
                            <span class="text-danger">{{ $errors->first('phone') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.customer.fields.phone_helper') }}</span>
                    </div>
                    <div class="form-group">
                        <label for="description">{{ trans('cruds.customer.fields.description') }}</label>
                        <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description"
                            id="description">{{ old('description') }}</textarea>
                        @if ($errors->has('description'))
                            <span class="text-danger">{{ $errors->first('description') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.customer.fields.description_helper') }}</span>
                    </div>
                    <hr>
                    <h6>{{ trans('global.address_information') }}</h6>
                    <div class="form-group">
                        <label class="required"
                            for="address_line_1">{{ trans('cruds.address.fields.address_line_1') }}</label>
                        <input class="form-control {{ $errors->has('address_line_1') ? 'is-invalid' : '' }}"
                            type="text" name="address_line_1" id="address_line_1"
                            value="{{ old('address_line_1', '') }}">
                        @if ($errors->has('address_line_1'))
                            <span class="text-danger">{{ $errors->first('address_line_1') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.address.fields.address_line_1_helper') }}</span>
                    </div>
                    <div class="form-group">
                        <label for="address_line_2">{{ trans('cruds.address.fields.address_line_2') }}</label>
                        <input class="form-control {{ $errors->has('address_line_2') ? 'is-invalid' : '' }}"
                            type="text" name="address_line_2" id="address_line_2"
                            value="{{ old('address_line_2', '') }}">
                        @if ($errors->has('address_line_2'))
                            <span class="text-danger">{{ $errors->first('address_line_2') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.address.fields.address_line_2_helper') }}</span>
                    </div>
                    <div class="form-group">
                        <label for="address_line_3">{{ trans('cruds.address.fields.address_line_3') }}</label>
                        <input class="form-control {{ $errors->has('address_line_3') ? 'is-invalid' : '' }}"
                            type="text" name="address_line_3" id="address_line_3"
                            value="{{ old('address_line_3', '') }}">
                        @if ($errors->has('address_line_3'))
                            <span class="text-danger">{{ $errors->first('address_line_3') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.address.fields.address_line_3_helper') }}</span>
                    </div>
                    <div class="form-group">
                        <label for="country">{{ trans('cruds.address.fields.country') }}</label>
                        <input class="form-control {{ $errors->has('country') ? 'is-invalid' : '' }}" type="text"
                            name="country" id="country" value="{{ old('country', '') }}">
                        @if ($errors->has('country'))
                            <span class="text-danger">{{ $errors->first('country') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.address.fields.country_helper') }}</span>
                    </div>
                    <div class="form-group">
                        <label for="province">{{ trans('cruds.address.fields.province') }}</label>
                        <input class="form-control {{ $errors->has('province') ? 'is-invalid' : '' }}" type="text"
                            name="province" id="province" value="{{ old('province', '') }}">
                        @if ($errors->has('province'))
                            <span class="text-danger">{{ $errors->first('province') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.address.fields.province_helper') }}</span>
                    </div>
                    <div class="form-group">
                        <label for="city">{{ trans('cruds.address.fields.city') }}</label>
                        <input class="form-control {{ $errors->has('city') ? 'is-invalid' : '' }}" type="text"
                            name="city" id="city" value="{{ old('city', '') }}">
                        @if ($errors->has('city'))
                            <span class="text-danger">{{ $errors->first('city') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.address.fields.city_helper') }}</span>
                    </div>
                    <div class="form-group">
                        <label for="postcode">{{ trans('cruds.address.fields.postcode') }}</label>
                        <input class="form-control {{ $errors->has('postcode') ? 'is-invalid' : '' }}" type="text"
                            name="postcode" id="postcode" value="{{ old('postcode', '') }}">
                        @if ($errors->has('postcode'))
                            <span class="text-danger">{{ $errors->first('postcode') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label class="required">{{ trans('cruds.address.fields.type') }}</label>
                        <select class="form-control {{ $errors->has('address_type') ? 'is-invalid' : '' }}"
                            name="address_type" id="address_type">
                            <option value disabled {{ old('address_type', null) === null ? 'selected' : '' }}>
                                {{ trans('global.pleaseSelect') }}</option>
                            @foreach (App\Enums\AddressType::getInstances() as $enum)
                                <option value="{{ $enum->value }}"
                                    {{ old('address_type', App\Enums\AddressType::ADDRESS) === (string) $enum->value ? 'selected' : '' }}>
                                    {{ $enum->description }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('type'))
                            <span class="text-danger">{{ $errors->first('type') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="address_phone">{{ trans('cruds.address.fields.phone') }}</label>
                        <input class="form-control {{ $errors->has('address_phone') ? 'is-invalid' : '' }}"
                            type="text" name="address_phone" id="address_phone"
                            value="{{ old('address_phone', '') }}">
                        @if ($errors->has('address_phone'))
                            <span class="text-danger">{{ $errors->first('address_phone') }}</span>
                        @endif
                        <span class="help-block">{{ trans('cruds.customer.fields.phone_helper') }}</span>
                    </div>
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
@push('js')
    <script>
        // function getchannels(companyId) {
        //     var options = '';
        //     $('#channel_id').attr('disabled', true).html(options).val('').change();
        //     if (companyId) {
        //         $.get("{{ url('admin/channels/get-channels') }}?company_id=" + companyId, function(
        //             res) {
        //             res.forEach(data => {
        //                 options += '<option value="' + data.id + '">' + data.name +
        //                     '</option>';
        //             });
        //             $('#channel_id').attr('disabled', false).html(options).change();
        //         })
        //     } else {
        //         $('#channel_id').attr('disabled', true).html(options).val('').change();
        //     }
        // }

        // function getProductBrands(companyId) {
        //     var options = '';
        //     $('#product_brand_ids').attr('disabled', true).html(options).val('').change();
        //     if (companyId) {
        //         $.get('{{ url('admin/product-brands/get-product-brands') }}?company_id=' + companyId,
        //             function(
        //                 res) {
        //                 res.forEach(data => {
        //                     options += '<option value="' + data.id + '">' + data.name +
        //                         '</option>';
        //                 });
        //                 $('#product_brand_ids').attr('disabled', false).html(options).change();
        //             })
        //     } else {
        //         $('#product_brand_ids').attr('disabled', true).html(options).val('').change();
        //     }
        // }

        $(document).ready(function() {
            $('#company_id').on('change', function() {
                // getchannels($(this).val());
                // getProductBrands($(this).val());
            });

            $('#customer_id').select2({
                placeholder: 'Search by name, email, or phone',
                minimumInputLength: 2,
                ajax: {
                    url: "{{ url('admin/customers/ajax-get-customers') }}?has_address=1",
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: item.name,
                                    id: item.id
                                }
                            })
                        };
                    },
                    cache: true
                }
            });

            $('#lead_category_id').on('change', function() {
                $('#sub_lead_category_id').attr('disabled', true).html('');
                var leadCategoryId = $(this).val();
                $.get('{{ url('admin/leads/get-sublead-categories') }}/' + leadCategoryId,
                    function(html) {
                        if (leadCategoryId) {
                            $('#sub_lead_category_id').attr('disabled', false).html(html);
                        } else {
                            $('#sub_lead_category_id').attr('disabled', true).html('');
                        }
                    });
            });

            $('#is_new_customer').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#select_customer_id').hide();
                    $('#form_new_customer').show();
                    $('#first_name').attr('required', true);
                    $('#address_line_1').attr('required', true);
                    $('#address_type').attr('required', true);
                } else {
                    $('#form_new_customer').hide();
                    $('#select_customer_id').show();
                    $('#first_name').attr('required', false);
                    $('#address_line_1').attr('required', false);
                    $('#address_type').attr('required', false);
                }
            });
        });
    </script>
@endpush()
