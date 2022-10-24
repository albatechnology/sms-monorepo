@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.discount.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.discounts.store") }}" enctype="multipart/form-data">
            @csrf
            @livewire('discount-page')

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
        $('#scope').on('change', function(){
            if($(this).val() == 1){
                $('#select_product_unit_ids').show();
                $('#company_id').change();
            } else {
                $('#selectProduct').val('').change();
                $('#select_product_unit_ids').hide();
            }

            if($(this).val() == 3){
                $('#product_category_div').show();
                $('#product_category_label').addClass('required');
                $('#product_category').attr('required');
            } else {
                $('#product_category').val('').change();
                $('#product_category_label').removeClass('required');
                $('#product_category').removeAttr('required');
                $('#product_category_div').hide();
            }

            if($(this).val() == 4){
                $('#product_brand_label').addClass('required');
                $('#company_id').change();
                $('#product_brand').attr('required');
            } else {
                $('#product_brand').val('').change();
                $('#product_brand_label').removeClass('required');
                $('#product_brand').removeAttr('required');
            }
        });

        $('#company_id').on('change', function(){
            if($(this).val()){
                initializeProductUnits();
                initializeProductBrand($(this).val());
            } else {
                $('#product_brand').attr('disabled', true).val('').change();
            }
        });

        $('#product_brand').on('change', function(){
            initializeProductUnits();
        });

        function initializeProductUnits(){
            companyId = $('#company_id').val();
            productBrand = $('#product_brand').val();
            $('#selectProduct').val('').change().select2({
                placeholder: 'Select an product units',
                minimumInputLength: 4,
                ajax: {
                    url: '{{ route("admin.orders.getproduct") }}'+'?company_id='+companyId+'&product_brand='+productBrand,
                    dataType: 'json',
                    delay: 250,
                    processResults: function (data) {
                        return {
                            results:  $.map(data, function (item) {
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

            if($('#scope').val() == 1){
                $('#select_product_unit_ids').show();
            } else {
                $('#selectProduct').val('').change();
                $('#select_product_unit_ids').hide();
            }
        }

        function initializeProductBrand(company_id){
            $('#product_brand').attr('disabled', false).val('').change().select2({
                placeholder: 'Select Product Brand',
                allowClear: true,
                ajax: {
                    url: '{{ route("admin.orders.get.product-brand") }}'+'?company_id='+company_id,
                    dataType: 'json',
                    delay: 250,
                    processResults: function (data) {
                        return {
                            results:  $.map(data, function (item) {
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
