@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.payment.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.payments.store") }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="required" for="amount">{{ trans('cruds.payment.fields.amount') }}</label>
                    <input class="form-control {{ $errors->has('amount') ? 'is-invalid' : '' }}" type="number"
                           name="amount" id="amount" value="{{ old('amount', '') }}" step="0.01" required>
                    @if($errors->has('amount'))
                        <span class="text-danger">{{ $errors->first('amount') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.payment.fields.amount_helper') }}</span>
                </div>
                @livewire('payment-page', ['payment_type' => old('payment_type_id')])
                <div class="form-group">
                    <label for="reference">{{ trans('cruds.payment.fields.reference') }}</label>
                    <input class="form-control {{ $errors->has('reference') ? 'is-invalid' : '' }}" type="text"
                           name="reference" id="reference" value="{{ old('reference', '') }}">
                    @if($errors->has('reference'))
                        <span class="text-danger">{{ $errors->first('reference') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.payment.fields.reference_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="proof">{{ trans('cruds.payment.fields.proof') }}</label>
                    <div class="needsclick dropzone {{ $errors->has('proof') ? 'is-invalid' : '' }}"
                         id="proof-dropzone">
                    </div>
                    @if($errors->has('proof'))
                        <span class="text-danger">{{ $errors->first('proof') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.payment.fields.proof_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required">{{ trans('cruds.payment.fields.status') }}</label>
                    <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status"
                            id="status"
                            required>
                        <option value
                                disabled {{ old('status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                        @foreach(\App\Enums\PaymentStatus::getInstances() as $enum)
                            <option value="{{ $enum->value }}" {{ old('status', \App\Enums\PaymentStatus::PENDING) === (string) $enum->value ? 'selected' : '' }}>{{ $enum->description }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('status'))
                        <span class="text-danger">{{ $errors->first('status') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.payment.fields.status_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="reason">{{ trans('cruds.payment.fields.reason') }}</label>
                    <input class="form-control {{ $errors->has('reason') ? 'is-invalid' : '' }}" type="text"
                           name="reason"
                           id="reason" value="{{ old('reason', '') }}">
                    @if($errors->has('reason'))
                        <span class="text-danger">{{ $errors->first('reason') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.payment.fields.reason_helper') }}</span>
                </div>

                <div class="form-group">
                    <label class="required" for="invoice_number">{{ trans('cruds.payment.fields.invoice') }}</label>
                    <input class="form-control {{ $errors->has('invoice_number') ? 'is-invalid' : '' }}" type="text"
                           name="invoice_number"
                           id="invoice_number" value="{{ old('invoice_number', '') }}">
                    @if($errors->has('invoice_number'))
                        <span class="text-danger">{{ $errors->first('invoice_number') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.payment.fields.invoice_helper') }}</span>
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
        var uploadedProofMap = {}
        Dropzone.options.proofDropzone = {
            url: '{{ route('admin.payments.storeMedia') }}',
            maxFilesize: 2, // MB
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            params: {
                size: 2
            },
            success: function (file, response) {
                $('form').append('<input type="hidden" name="proof[]" value="' + response.name + '">')
                uploadedProofMap[file.name] = response.name
            },
            removedfile: function (file) {
                file.previewElement.remove()
                var name = ''
                if (typeof file.file_name !== 'undefined') {
                    name = file.file_name
                } else {
                    name = uploadedProofMap[file.name]
                }
                $('form').find('input[name="proof[]"][value="' + name + '"]').remove()
            },
            init: function () {
                @if(isset($payment) && $payment->proof)
                var files =
                {!! json_encode($payment->proof) !!}
                    for (var i in files) {
                    var file = files[i]
                    this.options.addedfile.call(this, file)
                    file.previewElement.classList.add('dz-complete')
                    $('form').append('<input type="hidden" name="proof[]" value="' + file.file_name + '">')
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
