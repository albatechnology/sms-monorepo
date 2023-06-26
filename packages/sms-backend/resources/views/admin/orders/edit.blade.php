@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{ trans('cruds.order.title_singular') }} - {{ $order->invoice_number }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.orders.update', [$order->id]) }}">
                @method('PUT')
                @csrf
                <div class="form-group">
                    <label for="user_id">{{ trans('cruds.order.fields.user') }}</label>
                    <input type="text" class="form-control {{ $errors->has('user_id') ? 'is-invalid' : '' }}"
                        name="user_id" id="user_id" value="{{ old('user_id', $order->user->name) }}" readonly />
                    @if ($errors->has('user_id'))
                        <span class="text-danger">{{ $errors->first('user_id') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.order.fields.user_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="customer_id">{{ trans('cruds.order.fields.customer') }}</label>
                    <input type="text" class="form-control {{ $errors->has('customer_id') ? 'is-invalid' : '' }}"
                        name="customer_id" id="customer_id"
                        value="{{ old('customer_id', $order->customer->first_name . ' ' . $order->customer->last_name) }}"
                        readonly />
                    @if ($errors->has('customer_id'))
                        <span class="text-danger">{{ $errors->first('customer_id') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.order.fields.customer_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="note">{{ trans('cruds.order.fields.note') }}</label>
                    <textarea class="form-control ckeditor {{ $errors->has('note') ? 'is-invalid' : '' }}" name="note"
                        id="note">{!! old('note', $order->note) !!}</textarea>
                    @if ($errors->has('note'))
                        <span class="text-danger">{{ $errors->first('note') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.order.fields.note_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="channel_id">{{ trans('cruds.order.fields.channel') }}</label>
                    <select class="form-control select2 {{ $errors->has('channel') ? 'is-invalid' : '' }}"
                        name="channel_id" id="channel_id" required>
                        @foreach ($channels as $id => $channel)
                            <option value="{{ $id }}"
                                {{ (old('channel_id') ? old('channel_id') : $order->channel->id ?? '') == $id ? 'selected' : '' }}>
                                {{ $channel }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('channel'))
                        <span class="text-danger">{{ $errors->first('channel') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.order.fields.channel_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="status">{{ trans('cruds.order.fields.status') }}</label>
                    <select class="form-control select2 {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status" id="status" required>
                        <option value="">Please select</option>
                        @foreach ($status as $enum)
                            <option value="{{ $enum->value }}" {{ $order->status->is($enum) ? 'selected' : '' }}>{{ $enum->key }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('status'))
                        <span class="text-danger">{{ $errors->first('status') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.order.fields.status_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="expected_shipping_datetime">Expected Delivery Date</label>
                    <input type="text" name="expected_shipping_datetime" value="{{ $order->expected_shipping_datetime }}" class="form-control datetime" required>
                    @if ($errors->has('expected_shipping_datetime'))
                        <span class="text-danger">{{ $errors->first('expected_shipping_datetime') }}</span>
                    @endif
                </div>
                <div class="form-group">
                    <label class="required" for="created_at">Order Date</label>
                    <input type="text" name="created_at" value="{{ $order->created_at }}" class="form-control datetime" required>
                    @if ($errors->has('created_at'))
                        <span class="text-danger">{{ $errors->first('created_at') }}</span>
                    @endif
                </div>
                @if ($order->deal_at != null && $order->deal_at != '')
                    <div class="form-group">
                        <label class="required" for="deal_at">Deal At</label>
                        <input type="text" name="deal_at" value="{{ $order->deal_at }}" class="form-control datetime"
                            required>
                        @if ($errors->has('deal_at'))
                            <span class="text-danger">{{ $errors->first('deal_at') }}</span>
                        @endif
                    </div>
                @endif
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
    @parent
    <script>
        $(function() {
                $('#status').val('{{ $order->status }}').trigger('change');

                function SimpleUploadAdapter(editor) {
                    editor.plugins.get('FileRepository').createUploadAdapter = function(loader) {
                        return {
                            upload: function() {
                                return loader.file
                                    .then(function(file) {
                                        return new Promise(function(resolve, reject) {
                                                // Init request
                                                var xhr = new XMLHttpRequest();
                                                xhr.open('POST', '/admin/order-details/ckmedia', true);
                                                xhr.setRequestHeader('x-csrf-token', window._token);
                                                xhr.setRequestHeader('Accept', 'application/json');
                                                xhr.responseType = 'json';

                                                // Init listeners
                                                var genericErrorText =
                                                    `Couldn't upload file: ${ file.name }.`;
                                                xhr.addEventListener('error', function() {
                                                    reject(genericErrorText)
                                                });
                                                xhr.addEventListener('abort', function() {
                                                    reject()
                                                });
                                                xhr.addEventListener('load', function() {
                                                        var response = xhr.response;

                                                        if (!response || xhr.status !== 201) {
                                                            return reject(response && response
                                                                .message ?
                                                                `${genericErrorText}\n${xhr.status} ${response.message}` :
                                                                `${genericErrorText}\n ${xhr.status} ${xhr.statusText}` <<
                                                                <<
                                                                << < HEAD
                                                            ); ===
                                                            ===
                                                            =
                                                        ); >>>
                                                        >>>
                                                        > develop
                                                    }

                                                    $('form').append(
                                                        '<input type="hidden" name="ck-media[]" value="' +
                                                        response.id + '">');

                                                    resolve({
                                                        default: response.url
                                                    });
                                                });

                                            if (xhr.upload) {
                                                xhr.upload.addEventListener('progress', function( <<
                                                    <<
                                                    << < HEAD e) {
                                                    ===
                                                    ===
                                                    =
                                                    e) {
                                                    >>>
                                                    >>>
                                                    > develop
                                                    if (e.lengthComputable) {
                                                        loader.uploadTotal = e.total;
                                                        loader.uploaded = e.loaded;
                                                    }
                                                });
                                            }

                                            // Send request
                                            var data = new FormData(); data.append('upload', file); data
                                            .append('crud_id', <<
                                                <<
                                                << < HEAD '{{ $orderDetail->id ?? 0 }}'); ===
                                            ===
                                            =
                                            '{{ $orderDetail->id ?? 0 }}'); >>>
                                        >>>
                                        > develop
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
