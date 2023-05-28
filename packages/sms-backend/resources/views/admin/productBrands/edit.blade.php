@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{ trans('cruds.productBrand.title_singular') }}
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route("admin.product-brands.update", [$productBrand->id]) }}"
                  enctype="multipart/form-data">
                @method('PUT')
                @csrf
                {{-- <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.productBrand.fields.name') }}</label>
                    <input class="form-control" type="text" value="{{ $productBrand->company->name }}" disabled>
                </div> --}}
                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.productBrand.fields.name') }}</label>
                    <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name"
                           id="name" value="{{ old('name', $productBrand->name) }}" required>
                    @if($errors->has('name'))
                        <span class="text-danger">{{ $errors->first('name') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.productBrand.fields.name_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="brand_category_id">{{ trans('cruds.productBrand.fields.brand_categories') }}</label>
                    <select class="form-control select2 {{ $errors->has('brand_category_id') ? 'is-invalid' : '' }}" name="brand_category_id" id="brand_category_id" required>
                        @foreach($brandCategories as $brandCategory)
                            <option value="{{ $brandCategory->id }}" {{ $productBrand->brand_category_id == $brandCategory->id ? 'selected' : '' }}>{{ $brandCategory->name }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('brand_category_id'))
                        <span class="text-danger">{{ $errors->first('brand_category_id') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.productBrand.fields.brand_categories_helper') }}</span>
                </div>
                {{-- <div class="form-group">
                    <label class="required" for="hpp_calculation">{{ trans('cruds.productBrand.fields.hpp_calculation') }}</label>
                    <input class="form-control {{ $errors->has('hpp_calculation') ? 'is-invalid' : '' }}" type="number" name="hpp_calculation" id="hpp_calculation" value="{{ $productBrand->hpp_calculation }}" step="1" min="1" max="100" required>
                    @if($errors->has('hpp_calculation'))
                        <span class="text-danger">{{ $errors->first('hpp_calculation') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.productBrand.fields.hpp_calculation_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="currency_id">Currency</label>
                    <select class="form-control select2 {{ $errors->has('currency_id') ? 'is-invalid' : '' }}" name="currency_id" id="currency_id">
                        @foreach($currencies as $c)
                            <option value="{{ $c->id }}" {{ $c->id == $productBrand->currency_id ? 'selected' : '' }}>{{ $c->main_currency .' - '. $c->foreign_currency .' Rp.'. number_format($c->value) }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('currency_id'))
                        <span class="text-danger">{{ $errors->first('currency_id') }}</span>
                    @endif
                </div> --}}
                <div class="form-group">
                    <label for="photo">{{ trans('cruds.productBrand.fields.photo') }}</label>
                    <div class="needsclick dropzone {{ $errors->has('photo') ? 'is-invalid' : '' }}"
                         id="photo-dropzone">
                    </div>
                    @if($errors->has('photo'))
                        <span class="text-danger">{{ $errors->first('photo') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.productBrand.fields.photo_helper') }}</span>
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
        var uploadedPhotoMap = {}
        Dropzone.options.photoDropzone = {
            url: '{{ route('admin.product-brands.storeMedia') }}',
            maxFilesize: 2, // MB
            acceptedFiles: '.jpeg,.jpg,.png,.gif',
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
                @if(isset($productBrand) && $productBrand->photo)
                var files =
                {!! json_encode($productBrand->photo) !!}
                for (var i in files) {
                    var file = files[i]
                    console.log(file);
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
