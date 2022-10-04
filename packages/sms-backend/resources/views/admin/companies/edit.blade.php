@extends('layouts.admin')
@section('content')

    <form method="POST" action="{{ route("admin.companies.update", [$company->id]) }}" enctype="multipart/form-data">
        @method('PUT')
        @csrf
        <div class="card">
            <div class="card-header">
                {{ trans('global.edit') }} {{ trans('cruds.company.title_singular') }}
            </div>

            <div class="card-body">
                <x-input key='name' :model='$company'></x-input>

                <div class="form-group">
                    <label for="logo">{{ trans('cruds.company.fields.logo') }}</label>
                    <div class="needsclick dropzone {{ $errors->has('logo') ? 'is-invalid' : '' }}"
                         id="logo-dropzone">
                    </div>
                    @if($errors->has('logo'))
                        <span class="text-danger">{{ $errors->first('logo') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.company.fields.logo_helper') }}</span>
                </div>

                <div class="form-group">
                    <label for="company_account_id">{{ trans('cruds.company.fields.company_account_id') }}</label>
                    <select class="form-control select2 {{ $errors->has('company_account_id') ? 'is-invalid' : '' }}"
                            name="company_account_id">
                        @foreach(\App\Models\CompanyAccount::where('company_id', $company->id)->get() as $option)
                            <option value="{{ $option->id }}" {{ $option->id == old('company_account_id', $company->company_account_id) ? 'selected' : '' }}>{{ $option->name }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('company_account_id'))
                        <span class="text-danger">{{ $errors->first('company_account_id') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.company.fields.company_account_id_helper') }}</span>
                </div>

                <div class="form-group">
                    <button class="btn btn-danger" type="submit">
                        {{ trans('global.save') }}
                    </button>
                </div>
            </div>
        </div>


        <div class="card">
            <div class="card-header">
                Lead status duration (days)
            </div>


            <div class="card-body">

                @foreach(\App\Enums\LeadStatus::getInstances() as $enum)

                    @if($enum->nextStatus() !== null)
                        <div class="form-group">
                            <label class="required"
                                   for="options_lead_status_duration_days_{{$enum->value}}">{{$enum->description}}</label>
                            <input class="form-control {{ $errors->has("options_lead_status_duration_days_".$enum->value) ? 'is-invalid' : '' }}"
                                   type="text" name="options_lead_status_duration_days_{{$enum->value}}"
                                   id="options_lead_status_duration_days_{{$enum->value}}"
                                   value="{{ old("options_lead_status_duration_days_".$enum->value, $company->getLeadStatusDuration(\App\Enums\LeadStatus::fromValue($enum->value))) }}"
                                   required>
                            @if($errors->has("options_lead_status_duration_days_".$enum->value))
                                <span class="text-danger">{{ $errors->first("options_lead_status_duration_days_".$enum->value) }}</span>
                            @endif
                        </div>
                    @endif
                @endforeach

                <div class="form-group">
                    <button class="btn btn-danger" type="submit">
                        {{ trans('global.save') }}
                    </button>
                </div>
            </div>
        </div>
    </form>



@endsection

@section('scripts')
    <script>
        var uploadedLogoMap = {}
        Dropzone.options.logoDropzone = {
            url: '{{ route('admin.companies.storeMedia') }}',
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
                $('form').append('<input type="hidden" name="logo" value="' + response.name + '">')
                uploadedLogoMap[file.name] = response.name
            },
            removedfile: function (file) {
                console.log(file)
                file.previewElement.remove()
                var name = ''
                if (typeof file.file_name !== 'undefined') {
                    name = file.file_name
                } else {
                    name = uploadedLogoMap[file.name]
                }
                $('form').find('input[name="logo"][value="' + name + '"]').remove()
            },
            init: function () {
                @if(isset($company) && $company->logo)
                var file = {!! json_encode($company->logo) !!}
                    file.original_url = file.url
                file.extension = 'jpg'
                console.log(file);
                this.options.addedfile.call(this, file)
                this.options.thumbnail.call(this, file, file.preview)
                console.log(file.preview);
                file.previewElement.classList.add('dz-complete')
                $('form').append('<input type="hidden" name="logo" value="' + file.file_name + '">')

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