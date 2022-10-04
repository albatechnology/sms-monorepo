@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.importBatch.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.import-batches.store") }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="required" for="companies">{{ trans('cruds.user.fields.company') }}</label>
                    <select class="form-control select2 {{ $errors->has('company') ? 'is-invalid' : '' }}"
                            name="company_id" id="company">
                        @foreach($companies as $id => $name)
                            <option value="{{ $id }}" {{ in_array($id, old('company', [])) ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('company'))
                        <span class="text-danger">{{ $errors->first('company') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.user.fields.company_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="companies">{{ trans('cruds.importBatch.fields.type') }}</label>
                    <select class="form-control select2 {{ $errors->has('type') ? 'is-invalid' : '' }}" name="type"
                            id="type">
                        @foreach($types as $type)
                            <option value="{{ $type->value }}" {{ $type->value == old('type', '') ? 'selected' : '' }}>{{ $type->description }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('type'))
                        <span class="text-danger">{{ $errors->first('type') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.importBatch.fields.type_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="file">{{ trans('cruds.importBatch.fields.file') }}</label>
                    <span class="d-inline-block" tabindex="0" data-toggle="tooltip"
                          title="{{ trans('cruds.importBatch.fields.file_helper') }}">
                    <i class="fa-fw nav-icon fas fa-info-circle">
                    </i>
                </span>
                    <div class="needsclick dropzone {{ $errors->has('file') ? 'is-invalid' : '' }}"
                         id="file-dropzone">
                    </div>
                    @if($errors->has('file'))
                        <span class="text-danger">{{ $errors->first('file') }}</span>
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
        var uploadedFileMap = {}
        Dropzone.options.fileDropzone = {
            url: '{{ route('admin.import-batches.storeMedia') }}',
            maxFilesize: 10, // MB
            acceptedFiles: '.csv',
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            params: {
                size: 10,
            },
            success: function (file, response) {
                $('form').append('<input type="hidden" name="file" value="' + response.name + '">')
                uploadedFileMap[file.name] = response.name
            },
            removedfile: function (file) {
                console.log(file)
                file.previewElement.remove()
                var name = ''
                if (typeof file.file_name !== 'undefined') {
                    name = file.file_name
                } else {
                    name = uploadedFileMap[file.name]
                }
                $('form').find('input[name="file"][value="' + name + '"]').remove()
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