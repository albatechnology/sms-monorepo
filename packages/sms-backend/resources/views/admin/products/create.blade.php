@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.product.title_singular') }}
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route("admin.products.store") }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="is_active" value="1">
                <div class="form-group">
                    <label class="required" for="product_brand_id">{{ trans('cruds.product.fields.brand') }}</label>
                    <select class="form-control select2 {{ $errors->has('product_brand_id') ? 'is-invalid' : '' }}"
                            name="product_brand_id" id="product_brand_id" required>
                        @foreach($productBrands as $id => $productBrand)
                            <option value="{{ $id }}" {{ old('product_brand_id') == $id ? 'selected' : '' }}>{{ $productBrand }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('product_brand_id'))
                        <span class="text-danger">{{ $errors->first('product_brand_id') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.product.fields.brand_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.product.fields.name') }}</label>
                    <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name"
                           id="name" value="{{ old('name', '') }}" required>
                    @if($errors->has('name'))
                        <span class="text-danger">{{ $errors->first('name') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.product.fields.name_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="description">{{ trans('cruds.product.fields.description') }}</label>
                    <textarea class="form-control ckeditor {{ $errors->has('description') ? 'is-invalid' : '' }}"
                              name="description" id="description">{!! old('description') !!}</textarea>
                    @if($errors->has('description'))
                        <span class="text-danger">{{ $errors->first('description') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.product.fields.description_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="product_model_id">{{ trans('cruds.product.fields.model') }}</label>
                    <select class="form-control {{ $errors->has('product_model_id') ? 'is-invalid' : '' }}"
                            name="product_model_id" id="product_model_id" required>
                            <option value=""></option>
                    </select>
                    @if($errors->has('product_model_id'))
                        <span class="text-danger">{{ $errors->first('product_model_id') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.product.fields.model_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="product_version_id">{{ trans('cruds.product.fields.version') }}</label>
                    <select class="form-control {{ $errors->has('product_version_id') ? 'is-invalid' : '' }}"
                            name="product_version_id" id="product_version_id" required>
                            <option value=""></option>
                    </select>
                    @if($errors->has('product_version_id'))
                        <span class="text-danger">{{ $errors->first('product_version_id') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.product.fields.version_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="product_category_code_id">{{ trans('cruds.product.fields.category_code') }}</label>
                    <select class="form-control {{ $errors->has('product_category_code_id') ? 'is-invalid' : '' }}"
                            name="product_category_code_id" id="product_category_code_id" required>
                            <option value=""></option>
                    </select>
                    @if($errors->has('product_category_code_id'))
                        <span class="text-danger">{{ $errors->first('product_category_code_id') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.product.fields.category_code_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="categories">{{ trans('cruds.product.fields.category') }}</label>
                    <div style="padding-bottom: 4px">
                        <span class="btn btn-info btn-xs select-all"
                              style="border-radius: 0">{{ trans('global.select_all') }}</span>
                        <span class="btn btn-info btn-xs deselect-all"
                              style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                    </div>
                    <select class="form-control select2 {{ $errors->has('categories') ? 'is-invalid' : '' }}"
                            name="categories[]" id="categories" multiple>
                        @foreach($categories as $id => $category)
                            <option value="{{ $id }}" {{ in_array($id, old('categories', [])) ? 'selected' : '' }}>{{ $category }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('categories'))
                        <span class="text-danger">{{ $errors->first('categories') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.product.fields.category_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="tags">{{ trans('cruds.product.fields.tag') }}</label>
                    <div style="padding-bottom: 4px">
                        <span class="btn btn-info btn-xs select-all"
                              style="border-radius: 0">{{ trans('global.select_all') }}</span>
                        <span class="btn btn-info btn-xs deselect-all"
                              style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                    </div>
                    <select class="form-control select2 {{ $errors->has('tags') ? 'is-invalid' : '' }}" name="tags[]"
                            id="tags" multiple>
                        @foreach($tags as $id => $tag)
                            <option value="{{ $id }}" {{ in_array($id, old('tags', [])) ? 'selected' : '' }}>{{ $tag }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('tags'))
                        <span class="text-danger">{{ $errors->first('tags') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.product.fields.tag_helper') }}</span>
                </div>
                {{-- <div class="form-group">
                    <label for="price">{{ trans('cruds.product.fields.price') }}</label>
                    <input class="form-control {{ $errors->has('price') ? 'is-invalid' : '' }}" type="number"
                           name="price" id="price" value="{{ old('price', '') }}" step="0.01">
                    @if($errors->has('price'))
                        <span class="text-danger">{{ $errors->first('price') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.product.fields.price_helper') }}</span>
                </div> --}}
                <div class="form-group">
                    <label for="photo">{{ trans('cruds.product.fields.photo') }}</label>
                    <div class="needsclick dropzone {{ $errors->has('photo') ? 'is-invalid' : '' }}"
                         id="photo-dropzone">
                    </div>
                    @if($errors->has('photo'))
                        <span class="text-danger">{{ $errors->first('photo') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.product.fields.photo_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="company_id">{{ trans('cruds.product.fields.company') }}</label>
                    <select class="form-control select2 {{ $errors->has('company') ? 'is-invalid' : '' }}"
                            name="company_id" id="company_id" required>
                        @foreach($companies as $id => $company)
                            <option value="{{ $id }}" {{ old('company_id') == $id ? 'selected' : '' }}>{{ $company }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('company'))
                        <span class="text-danger">{{ $errors->first('company') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.product.fields.company_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="video_url">Video URL</label>
                    <input class="form-control {{ $errors->has('video_url') ? 'is-invalid' : '' }}" type="text" name="video_url" id="video_url" value="{{ old('video_url', '') }}">
                    @if($errors->has('video_url'))
                        <span class="text-danger">{{ $errors->first('video_url') }}</span>
                    @endif
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
    <script type="text/javascript">
        $(function () {
            $('#product_model_id').select2({
                placeholder: 'Please select',
                minimumInputLength: 3,
                ajax: {
                    url: '{{ route("admin.products.getModels") }}',
                    dataType: 'json',
                    delay: 250,
                    processResults: function (data) {
                        console.log(data);
                        return {
                            results:  $.map(data, function (item) {
                                return {
                                    text: `${item.name} - ${item.description}`,
                                    id: item.id
                                }
                            })
                        };
                    },
                    cache: true
                }
            });

            $('#product_version_id').select2({
                placeholder: 'Please select',
                minimumInputLength: 3,
                ajax: {
                    url: '{{ route("admin.products.getVersions") }}',
                    dataType: 'json',
                    delay: 250,
                    processResults: function (data) {
                        console.log(data);
                        return {
                            results:  $.map(data, function (item) {
                                return {
                                    text: `${item.name } (L: ${item.length }, W: ${item.width }, H: ${item.height})`,
                                    id: item.id
                                }
                            })
                        };
                    },
                    cache: true
                }
            });

            $('#product_category_code_id').select2({
                placeholder: 'Please select',
                minimumInputLength: 3,
                ajax: {
                    url: '{{ route("admin.products.getCategoryCodes") }}',
                    dataType: 'json',
                    delay: 250,
                    processResults: function (data) {
                        console.log(data);
                        return {
                            results:  $.map(data, function (item) {
                                return {
                                    text: `${item.name}`,
                                    id: item.id
                                }
                            })
                        };
                    },
                    cache: true
                }
            });
        });
    </script>
@endpush
@section('scripts')
    <script>
        $(document).ready(function () {
            function SimpleUploadAdapter(editor) {
                editor.plugins.get('FileRepository').createUploadAdapter = function (loader) {
                    return {
                        upload: function () {
                            return loader.file
                                .then(function (file) {
                                    return new Promise(function (resolve, reject) {
                                        // Init request
                                        var xhr = new XMLHttpRequest();
                                        xhr.open('POST', '/admin/products/ckmedia', true);
                                        xhr.setRequestHeader('x-csrf-token', window._token);
                                        xhr.setRequestHeader('Accept', 'application/json');
                                        xhr.responseType = 'json';

                                        // Init listeners
                                        var genericErrorText = `Couldn't upload file: ${file.name}.`;
                                        xhr.addEventListener('error', function () {
                                            reject(genericErrorText)
                                        });
                                        xhr.addEventListener('abort', function () {
                                            reject()
                                        });
                                        xhr.addEventListener('load', function () {
                                            var response = xhr.response;

                                            if (!response || xhr.status !== 201) {
                                                return reject(response && response.message ? `${genericErrorText}\n${xhr.status} ${response.message}` : `${genericErrorText}\n ${xhr.status} ${xhr.statusText}`);
                                            }

                                            $('form').append('<input type="hidden" name="ck-media[]" value="' + response.id + '">');

                                            resolve({default: response.url});
                                        });

                                        if (xhr.upload) {
                                            xhr.upload.addEventListener('progress', function (e) {
                                                if (e.lengthComputable) {
                                                    loader.uploadTotal = e.total;
                                                    loader.uploaded = e.loaded;
                                                }
                                            });
                                        }

                                        // Send request
                                        var data = new FormData();
                                        data.append('upload', file);
                                        data.append('crud_id', '{{ $product->id ?? 0 }}');
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
    <script>
        var uploadedPhotoMap = {}
        Dropzone.options.photoDropzone = {
            url: '{{ route('admin.products.storeMedia') }}',
            maxFilesize: 2, // MB
            acceptedFiles: '.jpeg,.jpg,.png,.gif,.csv',
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            params: {
                size: 2,
                width: 4096,
                height: 4096
            },
            success: function (file, response) {
                $('form').append('<input type="hidden" name="photo[]" value="' + response.name + '">')
                uploadedPhotoMap[file.name] = response.name
            },
            removedfile: function (file) {
                console.log(file)
                file.previewElement.remove()
                var name = ''
                if (typeof file.file_name !== 'undefined') {
                    name = file.file_name
                } else {
                    name = uploadedPhotoMap[file.name]
                }
                $('form').find('input[name="photo[]"][value="' + name + '"]').remove()
            },
            init: function () {
                @if(isset($product) && $product->photo)
                var files =
                {!! json_encode($product->photo) !!}
                    for (var i in files) {
                    var file = files[i]
                    this.options.addedfile.call(this, file)
                    this.options.thumbnail.call(this, file, file.preview_url)
                    file.previewElement.classList.add('dz-complete')
                    $('form').append('<input type="hidden" name="photo[]" value="' + file.file_name + '">')
                }
                @endif
            },
            error: function (file, response) {
                if ($.type(response) === 'string') {
                    var message = response //dropzone sends it's own error messages in string
                } else {
                    var message = response.errors.file
                }
                file.previewElement.classList.add('dz-error')
                _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
                _results = []
                for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                    node = _ref[_i]
                    _results.push(node.textContent = message)
                }

                return _results
            }
        }
    </script>
@endsection
