@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.promoCategory.title_singular') }}
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route("admin.promo-categories.store") }}" enctype="multipart/form-data">
                @csrf
                <x-input key='name' :model='app(\App\Models\Promo::class)'></x-input>
                <div class="form-group">
                    <label for="description">{{ trans('cruds.promoCategory.fields.description') }}</label>
                    <textarea class="form-control ckeditor {{ $errors->has('description') ? 'is-invalid' : '' }}"
                              name="description" id="description">{!! old('description') !!}</textarea>
                    @if($errors->has('description'))
                        <span class="text-danger">{{ $errors->first('description') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.promoCategory.fields.description_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="image">{{ trans('cruds.promoCategory.fields.image') }}</label>
                    <div class="needsclick dropzone {{ $errors->has('image') ? 'is-invalid' : '' }}"
                         id="image-dropzone">
                    </div>
                    @if($errors->has('image'))
                        <span class="text-danger">{{ $errors->first('image') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.promoCategory.fields.image_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="company_id">{{ trans('cruds.promoCategory.fields.company') }}</label>
                    <select class="form-control select2 {{ $errors->has('company') ? 'is-invalid' : '' }}"
                            name="company_id" id="company_id" required>
                        @foreach($companies as $id => $company)
                            <option value="{{ $id }}" {{ old('company_id') == $id ? 'selected' : '' }}>{{ $company }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('company'))
                        <span class="text-danger">{{ $errors->first('company') }}</span>
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
                                        xhr.open('POST', '/admin/promo-categories/ckmedia', true);
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
                                        data.append('crud_id', '{{ $promo->id ?? 0 }}');
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
        var uploadedImageMap = {}
        Dropzone.options.imageDropzone = {
            url: '{{ route('admin.promo-categories.storeMedia') }}',
            maxFilesize: 5, // MB
            acceptedFiles: '.jpeg,.jpg,.png,.gif',
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            params: {
                size: 5,
            },
            success: function (file, response) {
                $('form').append('<input type="hidden" name="image[]" value="' + response.name + '">')
                uploadedImageMap[file.name] = response.name
            },
            removedfile: function (file) {

                file.previewElement.remove()
                var name = ''
                if (typeof file.file_name !== 'undefined') {
                    name = file.file_name
                } else {
                    name = uploadedImageMap[file.name]
                }
                $('form').find('input[name="image[]"][value="' + name + '"]').remove()
            },
            init: function () {
                @if(isset($promo) && $promo->image)
                var files =
                {!! json_encode($promo->image) !!}
                    for (var i in files) {
                    var file = files[i]
                    this.options.addedfile.call(this, file)
                    this.options.thumbnail.call(this, file, file.preview_url)
                    file.previewElement.classList.add('dz-complete')
                    $('form').append('<input type="hidden" name="image[]" value="' + file.file_name + '">')
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
