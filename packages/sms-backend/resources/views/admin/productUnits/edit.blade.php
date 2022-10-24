@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.productUnit.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.product-units.update", [$productUnit->id]) }}"
              enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="company_id">{{ trans('cruds.productUnit.fields.company') }}</label>
                <select class="form-control select2 {{ $errors->has('company_id') ? 'is-invalid' : '' }}" name="company_id" id="company_id" required>
                  @foreach($companies as $id => $company)
                      <option value="{{ $id }}" {{ (old('company_id') ? old('company_id') : $productUnit->company_id ?? '') == $id ? 'selected' : '' }}>{{ $company }}</option>
                  @endforeach
                </select>
                @if($errors->has('company_id'))
                    <span class="text-danger">{{ $errors->first('company_id') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.productUnit.fields.company_helper') }}</span>
            </div>

            <div class="form-group">
                <label class="required" for="product_id">{{ trans('cruds.productUnit.fields.product') }}</label>
                <select class="form-control select2" id="product_id" disabled>
                    <option selected>{{ $productUnit->product->name }}</option>
                </select>
                <span class="help-block">{{ trans('cruds.productUnit.fields.product_helper') }}</span>
            </div>

            <div class="form-group">
                <label class="required" for="colour_id">{{ trans('cruds.productUnit.fields.colour') }}</label>
                <select class="form-control select2 {{ $errors->has('colour_id') ? 'is-invalid' : '' }}"
                        name="colour_id" id="colour_id" required>
                    @foreach($colours as $id => $colour)
                        <option value="{{ $id }}" {{ (old('colour_id') ? old('colour_id') : $productUnit->colour->id ?? '') == $id ? 'selected' : '' }}>{{ $colour }}</option>
                    @endforeach
                </select>
                @if($errors->has('colour_id'))
                    <span class="text-danger">{{ $errors->first('colour_id') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.productUnit.fields.colour_helper') }}</span>
            </div>

            <div class="form-group">
                <label class="required" for="covering_id">{{ trans('cruds.productUnit.fields.covering') }}</label>
                <select class="form-control select2 {{ $errors->has('covering_id') ? 'is-invalid' : '' }}"
                        name="covering_id" id="covering_id" required>
                    @foreach($coverings as $id => $covering)
                        <option value="{{ $id }}" {{ (old('covering_id') ? old('covering_id') : $productUnit->covering->id ?? '') == $id ? 'selected' : '' }}>{{ $covering }}</option>
                    @endforeach
                </select>
                @if($errors->has('covering_id'))
                    <span class="text-danger">{{ $errors->first('covering_id') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.productUnit.fields.covering_helper') }}</span>
            </div>

            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.productUnit.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name"
                       id="name" value="{{ old('name', $productUnit->name) }}" required>
                @if($errors->has('name'))
                    <span class="text-danger">{{ $errors->first('name') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.productUnit.fields.name_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="description">{{ trans('cruds.productUnit.fields.description') }}</label>
                <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description" id="description">{{ old('description', $productUnit->description) }}</textarea>
                @if($errors->has('description'))
                    <span class="text-danger">{{ $errors->first('description') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.productUnit.fields.description_helper') }}</span>
            </div>

            <x-input key='sku' :model='$productUnit'></x-input>
            <div class="form-group">
                <label class="required" for="volume">Volume (&#13221;)</label>
                <input name="volume" class="form-control" type="number" min="0" step="any" value="{{ $productUnit->volume }}">
                @if ($errors->has('volume'))
                    <span class="text-danger">{{ $errors->first('volume') }}</span>
                @endif
            </div>
            <x-input key='price' :model='$productUnit' type="number"></x-input>
            <x-input key='production_cost' :model='$productUnit' required=1 type="number"></x-input>
            <x-input key='purchase_price' :model='$productUnit' required=1 type="number"></x-input>
            <div class="form-group">
                <label class="" for="product_unit_category">{{ trans('cruds.discount.fields.product_unit_category') }}</label>
                <select class="form-control {{ $errors->has('product_unit_category') ? 'is-invalid' : '' }}" name="product_unit_category" id="product_unit_category" style="width: 100%">
                    <option selected value="">-- Select Product Unit Category --</option>
                    @foreach(App\Enums\ProductCategory::getInstances() as $productCategory)
                        <option value="{{ $productCategory->value }}" {{ $productCategory == old('product_unit_category', $productUnit->product_unit_category) ? 'selected' : '' }}>{{ $productCategory->description }}</option>
                    @endforeach
                </select>

                @if($errors->has('product_unit_category'))
                    <span class="text-danger">{{ $errors->first('product_unit_category') }}</span>
                @endif

                <span class="help-block">{{ trans('cruds.discount.fields.product_unit_category_helper') }}</span>
            </div>
            <div class="form-group">
                <div class="form-check {{ $errors->has('is_active') ? 'is-invalid' : '' }}">
                    <input type="hidden" name="is_active" value="0">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ $productUnit->is_active || old('is_active', 0) === 1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">{{ trans('cruds.productUnit.fields.is_active') }}</label>
                </div>
                @if($errors->has('is_active'))
                    <span class="text-danger">{{ $errors->first('is_active') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.productUnit.fields.is_active_helper') }}</span>
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
                xhr.open('POST', '/admin/product-units/ckmedia', true);
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
                data.append('crud_id', '{{ $productUnit->id ?? 0 }}');
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
