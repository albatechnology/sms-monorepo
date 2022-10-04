@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.catalogue.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.catalogues.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="catalogue">{{ trans('cruds.catalogue.fields.catalogue') }}</label>
                <div class="needsclick dropzone {{ $errors->has('catalogue') ? 'is-invalid' : '' }}" id="catalogue-dropzone">
                </div>
                @if($errors->has('catalogue'))
                    <span class="text-danger">{{ $errors->first('catalogue') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.catalogue.fields.catalogue_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="channel_id">{{ trans('cruds.catalogue.fields.channel') }}</label>
                <select class="form-control select2 {{ $errors->has('channel') ? 'is-invalid' : '' }}" name="channel_id" id="channel_id">
                    @foreach($channels as $id => $channel)
                        <option value="{{ $id }}" {{ old('channel_id') == $id ? 'selected' : '' }}>{{ $channel }}</option>
                    @endforeach
                </select>
                @if($errors->has('channel'))
                    <span class="text-danger">{{ $errors->first('channel') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.catalogue.fields.channel_helper') }}</span>
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
    var uploadedCatalogueMap = {}
Dropzone.options.catalogueDropzone = {
    url: '{{ route('admin.catalogues.storeMedia') }}',
    maxFilesize: 3, // MB
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 3
    },
    success: function (file, response) {
      $('form').append('<input type="hidden" name="catalogue[]" value="' + response.name + '">')
      uploadedCatalogueMap[file.name] = response.name
    },
    removedfile: function (file) {
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedCatalogueMap[file.name]
      }
      $('form').find('input[name="catalogue[]"][value="' + name + '"]').remove()
    },
    init: function () {
@if(isset($catalogue) && $catalogue->catalogue)
          var files =
            {!! json_encode($catalogue->catalogue) !!}
              for (var i in files) {
              var file = files[i]
              this.options.addedfile.call(this, file)
              file.previewElement.classList.add('dz-complete')
              $('form').append('<input type="hidden" name="catalogue[]" value="' + file.file_name + '">')
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