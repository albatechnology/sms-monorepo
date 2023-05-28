@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{ trans('cruds.promo.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.promos.update", [$promo->id]) }}" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                {{-- <div class="form-group">
                    <label class="required" for="promo_category_id">{{ trans('cruds.promoCategory.title_singular') }}</label>
                    <select class="form-control select2 {{ $errors->has('promo_category_id') ? 'is-invalid' : '' }}"
                        name="promo_category_id" id="promo_category_id" required>
                        @foreach ($promoCategories as $id => $name)
                            <option value="{{ $id }}" {{ $promo->promo_category_id == $id ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('promo_category_id'))
                        <span class="text-danger">{{ $errors->first('promo_category_id') }}</span>
                    @endif
                </div> --}}
                <x-input key='name' :model='$promo' required=1></x-input>
                <div class="form-group">
                    <label for="description">{{ trans('cruds.promo.fields.description') }}</label>
                    <textarea class="form-control ckeditor {{ $errors->has('description') ? 'is-invalid' : '' }}"
                              name="description"
                              id="description">{!! old('description', $promo->description) !!}</textarea>
                    @if($errors->has('description'))
                        <span class="text-danger">{{ $errors->first('description') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.promo.fields.description_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="image">{{ trans('cruds.promo.fields.image') }}</label>
                    <div class="needsclick dropzone {{ $errors->has('image') ? 'is-invalid' : '' }}"
                         id="image-dropzone">
                    </div>
                    @if($errors->has('image'))
                        <span class="text-danger">{{ $errors->first('image') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.promo.fields.image_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="start_time">{{ trans('cruds.promo.fields.start_time') }}</label>
                    <input class="form-control datetime {{ $errors->has('start_time') ? 'is-invalid' : '' }}"
                           type="text" name="start_time" id="start_time"
                           value="{{ old('start_time', $promo->start_time) }}" required>
                    @if($errors->has('start_time'))
                        <span class="text-danger">{{ $errors->first('start_time') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.promo.fields.start_time_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="end_time">{{ trans('cruds.promo.fields.end_time') }}</label>
                    <input class="form-control datetime {{ $errors->has('end_time') ? 'is-invalid' : '' }}" type="text"
                           name="end_time" id="end_time" value="{{ old('end_time', $promo->end_time) }}">
                    @if($errors->has('end_time'))
                        <span class="text-danger">{{ $errors->first('end_time') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.promo.fields.end_time_helper') }}</span>
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
                                        xhr.open('POST', '/admin/promos/ckmedia', true);
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
            url: '{{ route('admin.promos.storeMedia') }}',
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
                console.log(file)
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
