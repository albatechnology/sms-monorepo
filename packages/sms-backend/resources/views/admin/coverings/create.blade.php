@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.covering.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.coverings.store") }}" enctype="multipart/form-data">
                @csrf

                <x-input key='name' :model='app(\App\Models\Covering::class)'></x-input>
                <x-input key='type' :model='app(\App\Models\Covering::class)' required=0></x-input>
                
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
                    <label class="required" for="product_id">{{ trans('cruds.productUnit.fields.product') }}</label>
                    <select class="form-control select2 product_id {{ $errors->has('product') ? 'is-invalid' : '' }}" name="product_id" id="product_id" required>
                        <option value="">Please select</option>
                    </select>
                    @if($errors->has('product'))
                        <span class="text-danger">{{ $errors->first('product') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.productUnit.fields.product_helper') }}</span>
                </div>


                <div class="form-group">
                    <label for="photo">{{ trans('cruds.covering.fields.photo') }}</label>
                    <div class="needsclick dropzone {{ $errors->has('photo') ? 'is-invalid' : '' }}"
                         id="photo-dropzone">
                    </div>
                    @if($errors->has('photo'))
                        <span class="text-danger">{{ $errors->first('photo') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.covering.fields.photo_helper') }}</span>
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

            if ($('.select2-search__field').val().length >= 3){
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

        var uploadedPhotoMap = {}
        Dropzone.options.photoDropzone = {
            url: '{{ route('admin.coverings.storeMedia') }}',
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
                @if(isset($covering) && $covering->photo)
                var files =
                {!! json_encode($covering->photo) !!}
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