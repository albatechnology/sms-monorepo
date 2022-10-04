<div class="m-3">
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.shipment.title_singular') }} {{ trans('global.list') }}
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th width="10">No</th>
                            <th>Name</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($cartDemand && count($cartDemand->items) > 0)
                            @php
                                $no = 1;
                            @endphp
                            @foreach ($cartDemand->items as $item)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>
                                        {{ $item['name'] ?? '' }}
                                    </td>
                                    <td>
                                        {{ $item['quantity'] ?? '' }}
                                    </td>
                                    <td>
                                        {{ rupiah($item['price']) ?? '' }}
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger" data-productunitid="{{ $item['id'] }}" data-productname="{{ $item['name'] }}" data-productprice="{{ $item['price'] }}" data-toggle="modal" data-target="#mdlInsertProductUnit">
                                            <i class="fa fa-plus"></i> Insert
                                        </button>
                                        <button type="button" class="btn btn-info" data-productunitid="{{ $item['id'] }}" data-toggle="modal" data-target="#mdlUpdateProductUnit">
                                            <i class="fa fa-edit"></i> Update
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal fade" id="mdlUpdateProductUnit">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add Product Unit</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('admin.orders.updateProductUnit', $cartDemand->id) }}">
                        @csrf
                        <input type="hidden" name="tmp_product_unit_id" class="tmp_product_unit_id">
                        <input type="hidden" name="is_active" value="1">
                        <input type="hidden" name="company_id" value="{{ $company['id'] }}">
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="required">{{ trans('global.company') }}</label>
                                <input type="text" class="form-control" readonly value="{{ $company['name'] }}">
                            </div>
                            <div class="form-group">
                                <label class="required" for="product_unit_id">Product Unit</label>
                                <select class="form-control {{ $errors->has('product_unit_id') ? 'is-invalid' : '' }}" name="product_unit_id" id="product_unit_id" required style="width: 100%">
                                    <option value="">Please select</option>
                                </select>
                                @if ($errors->has('product_unit_id'))
                                    <span class="text-danger">{{ $errors->first('product_unit_id') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button class="btn btn-primary" type="submit">{{ trans('global.save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="mdlInsertProductUnit">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add Product Unit</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('admin.orders.createProductUnit', $cartDemand->id) }}">
                        @csrf
                        <input type="hidden" name="tmp_product_unit_id" class="tmp_product_unit_id">
                        <input type="hidden" name="is_active" value="1">
                        <input type="hidden" name="company_id" value="{{ $company['id'] }}">
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="required">{{ trans('global.company') }}</label>
                                <input type="text" class="form-control" readonly value="{{ $company['name'] }}">
                            </div>
                            <div class="form-group">
                                <label class="required" for="product_id">{{ trans('cruds.productUnit.fields.product') }}</label>
                                <select class="form-control select2 product_id {{ $errors->has('product') ? 'is-invalid' : '' }}" name="product_id" id="product_id" required style="width: 100%">
                                    <option value="">Please select</option>
                                </select>
                                @if ($errors->has('product'))
                                    <span class="text-danger">{{ $errors->first('product') }}</span>
                                @endif
                                <span class="help-block">{{ trans('cruds.productUnit.fields.product_helper') }}</span>
                            </div>

                            <x-input key='name' :model='app(\App\Models\ProductUnit::class)' required=1></x-input>
                            <x-input key='description' :model='app(\App\Models\ProductUnit::class)' required=0></x-input>

                            <div class="form-group">
                                <label class="required" for="colour_id">{{ trans('cruds.productUnit.fields.colour') }}</label>
                                <select class="form-control select2 {{ $errors->has('colour_id') ? 'is-invalid' : '' }}" name="colour_id" id="colour_id" required style="width: 100%">
                                    <option value="">Please select</option>
                                </select>
                                @if ($errors->has('colour_id'))
                                    <span class="text-danger">{{ $errors->first('colour_id') }}</span>
                                @endif
                                <span class="help-block">{{ trans('cruds.productUnit.fields.colour_helper') }}</span>
                            </div>
                            <div class="form-group">
                                <label class="required" for="covering_id">{{ trans('cruds.productUnit.fields.covering') }}</label>
                                <select class="form-control select2 {{ $errors->has('covering_id') ? 'is-invalid' : '' }}" name="covering_id" id="covering_id" required style="width: 100%">
                                    <option value="">Please select</option>
                                </select>
                                @if ($errors->has('covering_id'))
                                    <span class="text-danger">{{ $errors->first('covering_id') }}</span>
                                @endif
                                <span class="help-block">{{ trans('cruds.productUnit.fields.covering_helper') }}</span>
                            </div>
                            <x-input key='sku' :model='app(\App\Models\ProductUnit::class)' required=1></x-input>
                            <div class="form-group">
                                <label class="required">Price</label>
                                <input class="form-control {{ $errors->has('price') ? 'is-invalid' : '' }}" type="number" name="price" id="price" value="0" step="1" required readonly>
                                @if ($errors->has('price'))
                                    <span class="text-danger">{{ $errors->first('price') }}</span>
                                @endif
                            </div>

                            <x-input key='production_cost' :model='app(\App\Models\ProductUnit::class)' required=1 type="number"></x-input>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button class="btn btn-primary" type="submit">{{ trans('global.save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@section('scripts')
    @parent
    <script>
        var companyId = '{{ $company['id'] }}';

        $(document).ready(function(){
            $('#product_unit_id').select2({
                dropdownParent: $("#mdlUpdateProductUnit"),
                placeholder: 'Select an product',
                minimumInputLength: 4,
                ajax: {
                    url: '{{ url("admin/product-units/get-product-unit-suggestion") }}/'+companyId,
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
        });

        $('#mdlUpdateProductUnit').on('show.bs.modal', function(e){
            var data = $(e.relatedTarget);
            $('#mdlUpdateProductUnit .tmp_product_unit_id').val(data.data('productunitid'));
        });

        $('#mdlInsertProductUnit').on('show.bs.modal', function(e){
            var data = $(e.relatedTarget);
            $('#mdlInsertProductUnit .tmp_product_unit_id').val(data.data('productunitid'));
            $('#mdlInsertProductUnit #name').val(data.data('productname'));
            $('#mdlInsertProductUnit #price').val(data.data('productprice'));
        });

        $(document).on('keyup', '.select2-search__field', function(e) {
            var selectItem = $('.select2-container--open').prev();
            var index = selectItem.index();
            var id = selectItem.attr('id');

            if ($('.select2-search__field').val().length >= 4) {
                if (id == "product_id") {
                    $.ajax({
                        url: "{{ route('admin.products.getProductSuggestion') }}",
                        type: "get",
                        data: {
                            name: $('.select2-search__field').val(),
                            company_id: companyId,
                        },
                        success: function(response) {
                            $(`#${id}`).empty()
                            $(`#${id}`).append(`<option value="">Please select</option>`);

                            $.each(response, function(i, value) {
                                $(`#${id}`).append(`<option value="${i}">${value}</option>`);
                            });

                            $(`#${id}`).select2('destroy');
                            $(`#${id}`).select2({
                                dropdownParent: $('#mdlInsertProductUnit')
                            });
                            $(`#${id}`).select2('open');
                        },
                        error: function(response) {
                            console.log(id + ' : ' + response);
                        }
                    });
                }
            }
        });

        $('#product_id').on('change', function() {
            if ($('#product_id').val()) {
                $.get(`{{ route('admin.product-units.index') }}/get-colour/${$('#product_id').val()}`, function(
                    response) {
                    if (response) {
                        $('#colour_id').empty()
                        $('#colour_id').append(`<option value="">Please select</option>`);

                        $.each(response, function(i, value) {
                            $('#colour_id').append(`<option value="${i}">${value}</option>`);
                        });
                    } else {
                        console.log('get-colour :' + response);
                    }
                });

                $.get(`{{ route('admin.product-units.index') }}/get-covering/${$('#product_id').val()}`,
                    function(response) {
                        if (response) {
                            $('#covering_id').empty()
                            $('#covering_id').append(`<option value="">Please select</option>`);

                            $.each(response, function(i, value) {
                                $('#covering_id').append(`<option value="${i}">${value}</option>`);
                            });
                        } else {
                            console.log('get-covering :' + response);
                        }
                    });
            }
        });
    </script>
@endsection
