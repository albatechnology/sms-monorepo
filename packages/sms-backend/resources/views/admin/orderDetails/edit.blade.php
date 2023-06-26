@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.orderDetail.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.order-details.update", [$orderDetail->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="status">{{ trans('cruds.orderDetail.fields.status') }}</label>
                <select class="form-control select2 {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status" id="status" required>
                    <option value="">Please select</option>
                    @foreach($status as $enum)
                        <option value="{{ $enum->value }}">{{ $enum->key }}</option>
                    @endforeach
                </select>
                @if($errors->has('status'))
                    <span class="text-danger">{{ $errors->first('status') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.orderDetail.fields.status_helper') }}</span>
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
      $('#status').val('{{ $orderDetail->status }}').trigger('change');

      function SimpleUploadAdapter(editor) {
        editor.plugins.get('FileRepository').createUploadAdapter = function(loader) {
          return {
            upload: function() {
              return loader.file
                .then(function (file) {
                  return new Promise(function(resolve, reject) {
                    // Init request
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', '/admin/order-details/ckmedia', true);
                    xhr.setRequestHeader('x-csrf-token', window._token);
                    xhr.setRequestHeader('Accept', 'application/json');
                    xhr.responseType = 'json';

                    // Init listeners
                    var genericErrorText = `Couldn't upload file: ${ file.name }.`;
                    xhr.addEventListener('error', function() { reject(genericErrorText) });
                    xhr.addEventListener('abort', function() { reject() });
                    xhr.addEventListener('load', function() {
                      var response = xhr.response;

                      if (!response || xhr.status !== 201) {
                        return reject(response && response.message ? `${genericErrorText}\n${xhr.status} ${response.message}` : `${genericErrorText}\n ${xhr.status} ${xhr.statusText}`);
                      }

                      $('form').append('<input type="hidden" name="ck-media[]" value="' + response.id + '">');

                      resolve({ default: response.url });
                    });

                    if (xhr.upload) {
                      xhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                          loader.uploadTotal = e.total;
                          loader.uploaded = e.loaded;
                        }
                      });
                    }

                    // Send request
                    var data = new FormData();
                    data.append('upload', file);
                    data.append('crud_id', '{{ $orderDetail->id ?? 0 }}');
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
