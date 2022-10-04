@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.orderDetail.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.order-details.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="product_unit_id">{{ trans('cruds.orderDetail.fields.product_unit') }}</label>
                <select class="form-control select2 {{ $errors->has('product_unit') ? 'is-invalid' : '' }}" name="product_unit_id" id="product_unit_id">
                    @foreach($product_units as $id => $product_unit)
                        <option value="{{ $id }}" {{ old('product_unit_id') == $id ? 'selected' : '' }}>{{ $product_unit }}</option>
                    @endforeach
                </select>
                @if($errors->has('product_unit'))
                    <span class="text-danger">{{ $errors->first('product_unit') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.orderDetail.fields.product_unit_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="order_id">{{ trans('cruds.orderDetail.fields.order') }}</label>
                <select class="form-control select2 {{ $errors->has('order') ? 'is-invalid' : '' }}" name="order_id" id="order_id" required>
                    @foreach($orders as $id => $order)
                        <option value="{{ $id }}" {{ old('order_id') == $id ? 'selected' : '' }}>{{ $order }}</option>
                    @endforeach
                </select>
                @if($errors->has('order'))
                    <span class="text-danger">{{ $errors->first('order') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.orderDetail.fields.order_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="product_detail">{{ trans('cruds.orderDetail.fields.product_detail') }}</label>
                <textarea class="form-control {{ $errors->has('product_detail') ? 'is-invalid' : '' }}" name="product_detail" id="product_detail">{{ old('product_detail') }}</textarea>
                @if($errors->has('product_detail'))
                    <span class="text-danger">{{ $errors->first('product_detail') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.orderDetail.fields.product_detail_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="note">{{ trans('cruds.orderDetail.fields.note') }}</label>
                <textarea class="form-control ckeditor {{ $errors->has('note') ? 'is-invalid' : '' }}" name="note" id="note">{!! old('note') !!}</textarea>
                @if($errors->has('note'))
                    <span class="text-danger">{{ $errors->first('note') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.orderDetail.fields.note_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="quantity">{{ trans('cruds.orderDetail.fields.quantity') }}</label>
                <input class="form-control {{ $errors->has('quantity') ? 'is-invalid' : '' }}" type="number" name="quantity" id="quantity" value="{{ old('quantity', '') }}" step="1" required>
                @if($errors->has('quantity'))
                    <span class="text-danger">{{ $errors->first('quantity') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.orderDetail.fields.quantity_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="mutations">{{ trans('cruds.orderDetail.fields.mutations') }}</label>
                <textarea class="form-control {{ $errors->has('mutations') ? 'is-invalid' : '' }}" name="mutations" id="mutations">{{ old('mutations') }}</textarea>
                @if($errors->has('mutations'))
                    <span class="text-danger">{{ $errors->first('mutations') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.orderDetail.fields.mutations_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="unit_price">{{ trans('cruds.orderDetail.fields.unit_price') }}</label>
                <input class="form-control {{ $errors->has('unit_price') ? 'is-invalid' : '' }}" type="number" name="unit_price" id="unit_price" value="{{ old('unit_price', '') }}" step="0.01" required>
                @if($errors->has('unit_price'))
                    <span class="text-danger">{{ $errors->first('unit_price') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.orderDetail.fields.unit_price_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="price">{{ trans('cruds.orderDetail.fields.price') }}</label>
                <input class="form-control {{ $errors->has('price') ? 'is-invalid' : '' }}" type="number" name="price" id="price" value="{{ old('price', '') }}" step="0.01" required>
                @if($errors->has('price'))
                    <span class="text-danger">{{ $errors->first('price') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.orderDetail.fields.price_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="notes">{{ trans('cruds.orderDetail.fields.notes') }}</label>
                <textarea class="form-control ckeditor {{ $errors->has('notes') ? 'is-invalid' : '' }}" name="notes" id="notes">{!! old('notes') !!}</textarea>
                @if($errors->has('notes'))
                    <span class="text-danger">{{ $errors->first('notes') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.orderDetail.fields.notes_helper') }}</span>
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