@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.productUnit.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.product-units.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label class="required" for="company_id">{{ trans('cruds.productUnit.fields.company') }}</label>
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
                    <span class="help-block">{{ trans('cruds.productUnit.fields.company_helper') }}</span>
                </div>

                <div class="form-group">
                    <label class="required" for="product_id">{{ trans('cruds.productUnit.fields.product') }}</label>
                    <select class="form-control select2 product_id {{ $errors->has('product') ? 'is-invalid' : '' }}"
                        name="product_id" id="product_id" required>
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
                    <select class="form-control select2 {{ $errors->has('colour_id') ? 'is-invalid' : '' }}"
                        name="colour_id" id="colour_id" required>
                        <option value="">Please select</option>
                    </select>
                    @if ($errors->has('colour_id'))
                        <span class="text-danger">{{ $errors->first('colour_id') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.productUnit.fields.colour_helper') }}</span>
                </div>

                <div class="form-group">
                    <label class="required"
                        for="covering_id">{{ trans('cruds.productUnit.fields.covering') }}</label>
                    <select class="form-control select2 {{ $errors->has('covering_id') ? 'is-invalid' : '' }}"
                        name="covering_id" id="covering_id" required>
                        <option value="">Please select</option>
                    </select>
                    @if ($errors->has('covering_id'))
                        <span class="text-danger">{{ $errors->first('covering_id') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.productUnit.fields.covering_helper') }}</span>
                </div>

                <x-input key='sku' :model='app(\App\Models\ProductUnit::class)' required=1></x-input>
                <div class="form-group">
                    <label class="required" for="volume">Volume (&#13221;)</label>
                    <input name="volume" class="form-control" type="number" min="0" step="any">
                    @if ($errors->has('volume'))
                        <span class="text-danger">{{ $errors->first('volume') }}</span>
                    @endif
                </div>
                <x-input key='price' :model='app(\App\Models\ProductUnit::class)' required=1 type="number"></x-input>
                <x-input key='production_cost' :model='app(\App\Models\ProductUnit::class)' required=1 type="number"></x-input>
                <x-input key='purchase_price' :model='app(\App\Models\ProductUnit::class)' required=1 type="number"></x-input>
                <div class="form-group">
                    <label class="" for="product_unit_category">{{ trans('cruds.discount.fields.product_unit_category') }}</label>
                    <select class="form-control {{ $errors->has('product_unit_category') ? 'is-invalid' : '' }}" name="product_unit_category" id="product_unit_category" style="width: 100%">
                        <option selected value="">-- Select Product Unit Category --</option>
                        @foreach(\App\Enums\ProductCategory::getInstances() as $productCategory)
                            <option value="{{ $productCategory->value }}" {{ $productCategory == old('product_unit_category') ? 'selected' : '' }}>{{ $productCategory->description }}</option>
                        @endforeach
                    </select>

                    @if($errors->has('product_unit_category'))
                        <span class="text-danger">{{ $errors->first('product_unit_category') }}</span>
                    @endif

                    <span class="help-block">{{ trans('cruds.discount.fields.product_unit_category_helper') }}</span>
                </div>
                <div class="form-group">
                    <div class="form-check {{ $errors->has('is_active') ? 'is-invalid' : '' }}">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                            {{ old('is_active', 0) == 1 || old('is_active') === null ? 'checked' : '' }}>
                        <label class="form-check-label"
                            for="is_active">{{ trans('cruds.productUnit.fields.is_active') }}</label>
                    </div>
                    @if ($errors->has('is_active'))
                        <span class="text-danger">{{ $errors->first('is_active') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.productUnit.fields.is_active_helper') }}</span>
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

@section('scripts')

    <script>
        $(document).on('keyup', '.select2-search__field', function(e) {
            var selectItem = $('.select2-container--open').prev();
            var index = selectItem.index();
            var id = selectItem.attr('id');

            if ($('.select2-search__field').val().length >= 3) {
                if (id == "product_id") {
                    $.ajax({
                        url: "{{ route('admin.products.getProductSuggestion') }}",
                        type: "get",
                        data: {
                            name: $('.select2-search__field').val()
                        },
                        success: function(response) {
                            $(`#${id}`).empty()
                            $(`#${id}`).append(`<option value="">Please select</option>`);

                            $.each(response, function(i, value) {
                                $(`#${id}`).append(`<option value="${i}">${value}</option>`);
                            });

                            $(`#${id}`).select2('destroy');
                            $(`#${id}`).select2();
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


        $(document).ready(function() {
            function SimpleUploadAdapter(editor) {
                editor.plugins.get('FileRepository').createUploadAdapter = function(loader) {
                    return {
                        upload: function() {
                            return loader.file
                                .then(function(file) {
                                    return new Promise(function(resolve, reject) {
                                        // Init request
                                        var xhr = new XMLHttpRequest();
                                        xhr.open('POST', '/admin/product-units/ckmedia', true);
                                        xhr.setRequestHeader('x-csrf-token', window._token);
                                        xhr.setRequestHeader('Accept', 'application/json');
                                        xhr.responseType = 'json';

                                        // Init listeners
                                        var genericErrorText =
                                            `Couldn't upload file: ${ file.name }.`;
                                        xhr.addEventListener('error', function() {
                                            reject(genericErrorText)
                                        });
                                        xhr.addEventListener('abort', function() {
                                            reject()
                                        });
                                        xhr.addEventListener('load', function() {
                                            var response = xhr.response;

                                            if (!response || xhr.status !== 201) {
                                                return reject(response && response
                                                    .message ?
                                                    `${genericErrorText}\n${xhr.status} ${response.message}` :
                                                    `${genericErrorText}\n ${xhr.status} ${xhr.statusText}`
                                                    );
                                            }

                                            $('form').append(
                                                '<input type="hidden" name="ck-media[]" value="' +
                                                response.id + '">');

                                            resolve({
                                                default: response.url
                                            });
                                        });

                                        if (xhr.upload) {
                                            xhr.upload.addEventListener('progress', function(
                                            e) {
                                                if (e.lengthComputable) {
                                                    loader.uploadTotal = e.total;
                                                    loader.uploaded = e.loaded;
                                                }
                                            });
                                        }

                                        // Send request
                                        var data = new FormData();
                                        data.append('upload', file);
                                        data.append('crud_id',
                                        '{{ $productUnit->id ?? 0 }}');
                                        xhr.send(data);
                                    });
                                })
                        }
                    };
                }
            }

            var allEditors = document.querySelectorAll('.ckeditor');
            for (var i = 0; i < allEditors.length; ++i) {
                ClassicEditor.create(
                    allEditors[i], {
                        extraPlugins: [SimpleUploadAdapter]
                    }
                );
            }
        });
    </script>

@endsection
