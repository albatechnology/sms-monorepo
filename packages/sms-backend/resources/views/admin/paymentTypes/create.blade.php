@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.paymentType.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.payment-types.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="payment_category_id">Promo</label>
                <select class="form-control select2 {{ $errors->has('payment_category_id') ? 'is-invalid' : '' }}"
                    name="payment_category_id" id="payment_category_id" required>
                    @foreach ($paymentCategories as $id => $name)
                        <option value="{{ $id }}" {{ old('payment_category_id', null) == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                @if ($errors->has('payment_category_id'))
                    <span class="text-danger">{{ $errors->first('payment_category_id') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.product.fields.brand_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.paymentType.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name"
                       id="name" value="{{ old('name', '') }}" required>
                @if($errors->has('name'))
                    <span class="text-danger">{{ $errors->first('name') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.paymentType.fields.name_helper') }}</span>
            </div>

            {{-- @livewire('payment-type-page') --}}
            <div class="form-group">
                <label for="photo">{{ trans('cruds.productModel.fields.photo') }}</label>
                <div class="needsclick dropzone {{ $errors->has('photo') ? 'is-invalid' : '' }}"
                     id="photo-dropzone">
                </div>
                @if($errors->has('photo'))
                    <span class="text-danger">{{ $errors->first('photo') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.productModel.fields.photo_helper') }}</span>
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
            url: '{{ route('admin.product-models.storeMedia') }}',
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
                @if(isset($productModel) && $productModel->photo)
                var files =
                {!! json_encode($productModel->photo) !!}
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
