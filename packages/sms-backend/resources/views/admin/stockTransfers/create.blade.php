@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.stockTransfer.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.stock-transfers.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="required" for="company_id">{{ trans('global.company') }}</label>
                    <select class="form-control select2 {{ $errors->has('company_id') ? 'is-invalid' : '' }}"
                        name="company_id" id="company_id" required>
                        @foreach ($companies as $id => $company)
                            <option value="{{ $id }}" {{ old('company_id') == $id ? 'selected' : '' }}>
                                {{ $company }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('company_id'))
                        <span class="text-danger">{{ $errors->first('company_id') }}</span>
                    @endif
                </div>
                <div class="form-group">
                    <label class="required" for="from_channel_id">Origin Channel</label>
                    <select
                        class="form-control select2 selectChannels {{ $errors->has('from_channel_id') ? 'is-invalid' : '' }}"
                        name="from_channel_id" id="from_channel_id" required disabled>
                        <option>- Select company -</option>
                    </select>
                    @if ($errors->has('from_channel_id'))
                        <span class="text-danger">{{ $errors->first('from_channel_id') }}</span>
                    @endif
                </div>
                <div class="form-group">
                    <label class="required" for="to_channel_id">Destination Channel</label>
                    <select
                        class="form-control select2 selectChannels {{ $errors->has('to_channel_id') ? 'is-invalid' : '' }}"
                        name="to_channel_id" id="to_channel_id" required disabled>
                        <option>- Select company -</option>
                    </select>
                    @if ($errors->has('to_channel_id'))
                        <span class="text-danger">{{ $errors->first('to_channel_id') }}</span>
                    @endif
                    <span class="help-block text-danger">Origin channel and destination channel cannot be the same</span>
                </div>
                <div class="form-group">
                    <label class="required"
                        for="product_unit_id">{{ trans('cruds.product.title_singular') }}</label>
                    <select name="product_unit_id"
                        class="form-control {{ $errors->has('product_unit_id') ? 'is-invalid' : '' }}"
                        id="product_unit_id" required disabled></select>
                    @if ($errors->has('product_unit_id'))
                        <span class="text-danger">{{ $errors->first('product_unit_id') }}</span>
                    @endif
                </div>
                <table class="table table-bordered table-hover w-50">
                    <tr>
                        <th></th>
                        <th>Origin Channel</th>
                        <th>Destination Channel</th>
                    </tr>
                    <tr>
                        <th>Stock</th>
                        <td id="stockOrigin">0</td>
                        <td id="stockDestination">0</td>
                    </tr>
                </table>
                <div class="form-group">
                    <label class="required" for="amount">{{ trans('cruds.stockTransfer.fields.amount') }}</label>
                    <input class="form-control {{ $errors->has('amount') ? 'is-invalid' : '' }}" type="number"
                        name="amount" id="amount" value="{{ old('amount') }}" step="1" required min="1">
                    @if ($errors->has('amount'))
                        <span class="text-danger">{{ $errors->first('amount') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.stockTransfer.fields.amount_helper') }}</span>
                </div>
                <div class="form-group">
                    <div class="form-check">
                        <label class="form-check-label"><input name="cut_indent" class="form-check-input" type="checkbox"
                                value="1"> Potong Indent</label>
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
        $(document).ready(function() {
            $('#company_id').on('change', function() {
                $('#product_unit_id').val(null).trigger('change');
                if ($(this).val() == '') {
                    $('.selectChannels').attr('disabled', true).html('<option>- Select company -</option>');
                } else {
                    $.get("{{ url('admin/stock-transfers/getChannels') }}/" + $(this).val(), function(
                        html) {
                        $('.selectChannels').attr('disabled', false).html(html);
                        $('#product_unit_id').attr('disabled', false);
                        getDetailStock();
                    });
                }
            });
            $('.selectChannels').on('change', function() {
                getDetailStock();
            });

            $('#product_unit_id').select2({
                placeholder: 'Select an product',
                minimumInputLength: 4,
                ajax: {
                    headers: {
                        'x-csrf-token': _token
                    },
                    url: "{{ url('admin/stock-transfers/getProducts') }}",
                    type: "post",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            company_id: $('#company_id').val(),
                            q: params.term
                        };
                    },
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
            }).on('change', function() {
                getDetailStock();
            });

            function getDetailStock() {
                var companyId = $('#company_id').val();
                var fromChannelId = $('#from_channel_id').val();
                var toChannelId = $('#to_channel_id').val();
                var productId = $('#product_unit_id').val();
                if (companyId != null && fromChannelId != null && toChannelId != null && productId != null) {
                    $.get("{{ url('admin/stock-transfers/detailStock') }}/" + companyId + "/" +
                        fromChannelId + "/" + toChannelId + "/" + productId,
                        function(res) {
                            $('#stockOrigin').text(res[fromChannelId]);
                            $('#stockDestination').text(res[toChannelId]);
                        });
                } else {
                    $('#stockOrigin, #stockDestination').text(0);
                }
            }
        });
    </script>
@endpush
