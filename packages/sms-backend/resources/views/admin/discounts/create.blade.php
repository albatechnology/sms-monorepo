@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.discount.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.discounts.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="required" for="promo_id">Promo</label>
                    <select class="form-control select2 {{ $errors->has('promo_id') ? 'is-invalid' : '' }}"
                        name="promo_id" id="promo_id" required>
                        @foreach ($promos as $id => $name)
                            <option value="{{ $id }}" {{ old('promo_id', null) == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('promo_id'))
                        <span class="text-danger">{{ $errors->first('promo_id') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.product.fields.brand_helper') }}</span>
                </div>
                <x-input key='name' :model='app(\App\Models\Discount::class)'></x-input>
                <x-input key='description' :model='app(\App\Models\Discount::class)' required="0"></x-input>
                <x-enum key='type' :model='app(\App\Models\Discount::class)'></x-enum>
                <x-input key='activation_code' :model='app(\App\Models\Discount::class)' required="0"></x-input>
                <div class="form-group">
                    <label class="required" for="stock">Value</label>
                    <input class="form-control {{ $errors->has('value') ? 'is-invalid' : '' }}" type="number" name="value" id="value" value="{{ old('value') }}" step="any" required>
                    @if($errors->has('value'))
                        <span class="text-danger">{{ $errors->first('value') }}</span>
                    @endif
                </div>
                <x-enum key='scope' :model='app(\App\Models\Discount::class)'></x-enum>
                <div class="form-group">
                    <div class="form-check {{ $errors->has('is_active') ? 'is-invalid' : '' }}">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                        value="1" {{ old('is_active', 0) == 1 || old('is_active') === null ? 'checked' : '' }}>
                        <label class="form-check-label"
                        for="is_active">{{ trans('cruds.discount.fields.is_active') }}</label>
                    </div>
                    @if($errors->has('is_active'))
                    <span class="text-danger">{{ $errors->first('is_active') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.discount.fields.is_active_helper') }}</span>
                </div>
                <x-input key='max_discount_price_per_order' :model='app(\App\Models\Discount::class)' type="number"
                required="0"></x-input>
                <x-input key='max_use_per_customer' :model='app(\App\Models\Discount::class)' type="number"
                required="0"></x-input>
                <x-input key='min_order_price' :model='app(\App\Models\Discount::class)' type="number"
                required="0"></x-input>
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
    <script type="text/javascript">
        $(function() {
            var companyId = '';
            var productBrand = '';
            $('#scope').on('change', function() {
                if ($(this).val() == 1) {
                    $('#select_product_unit_ids').show();
                    $('#company_id').change();
                } else {
                    $('#selectProduct').val('').change();
                    $('#select_product_unit_ids').hide();
                }

                if ($(this).val() == 3) {
                    $('#product_category_div').show();
                    $('#product_category_label').addClass('required');
                    $('#product_category').attr('required');
                } else {
                    $('#product_category').val('').change();
                    $('#product_category_label').removeClass('required');
                    $('#product_category').removeAttr('required');
                    $('#product_category_div').hide();
                }

                if ($(this).val() == 4) {
                    $('#product_brand_label').addClass('required');
                    $('#company_id').change();
                    $('#product_brand').attr('required');
                } else {
                    $('#product_brand').val('').change();
                    $('#product_brand_label').removeClass('required');
                    $('#product_brand').removeAttr('required');
                }
            });

            $('#company_id').on('change', function() {
                if ($(this).val()) {
                    initializeProductUnits();
                    initializeProductBrand($(this).val());
                } else {
                    $('#product_brand').attr('disabled', true).val('').change();
                }
            });

            $('#product_brand').on('change', function() {
                initializeProductUnits();
            });

            function initializeProductUnits() {
                companyId = $('#company_id').val();
                productBrand = $('#product_brand').val();
                $('#selectProduct').val('').change().select2({
                    placeholder: 'Select an product units',
                    minimumInputLength: 4,
                    ajax: {
                        url: '{{ route('admin.orders.getproduct') }}' + '?company_id=' + companyId +
                            '&product_brand=' + productBrand,
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

                if ($('#scope').val() == 1) {
                    $('#select_product_unit_ids').show();
                } else {
                    $('#selectProduct').val('').change();
                    $('#select_product_unit_ids').hide();
                }
            }

            function initializeProductBrand(company_id) {
                $('#product_brand').attr('disabled', false).val('').change().select2({
                    placeholder: 'Select Product Brand',
                    allowClear: true,
                    ajax: {
                        url: '{{ route('admin.orders.get.product-brand') }}' + '?company_id=' + company_id,
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
            }
        });
    </script>
@endpush
