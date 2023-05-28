<div class="modal fade" id="csvImportModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">@lang('global.app_csvImport')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class='col-md-12'>
                        <div class="col-md-6">
                            <a href="{{ route('admin.exports.sample', $type) }}">Download Sample CSV</a>
                        </div>

                        <br>

                        <form class="form-horizontal" method="POST" action="{{ route($route, ['model' => $model]) }}"
                              enctype="multipart/form-data">
                            {{ csrf_field() }}

                            <input type="hidden" name="type" value="{{$type}}">
                            <div class="form-group{{ $errors->has('csv_file') ? ' has-error' : '' }}">
                                <label for="csv_file"
                                       class="col-md-4 control-label">@lang('global.app_csv_file_to_import')</label>
                                <div class="col-md-6">
                                    <input id="csv_file" type="file" class="form-control-file" name="csv_file" required>

                                    @if($errors->has('csv_file'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('csv_file') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <br>
                                {{-- <div class="col-md-6 {{ $type == \App\Enums\Import\ImportBatchType::INTERIOR_DESIGN ? 'd-none' : null  }}">
                                    <label class="required"
                                           for="companies">{{ trans('cruds.user.fields.company') }}</label> <br>
                                    <select class="form-control select2 {{ $errors->has('company') ? 'is-invalid' : '' }}"
                                            name="company_id" id="company">
                                        @foreach(tenancy()->getCompanies()->pluck('name', 'id') as $id => $name)
                                            <option value="{{ $id }}" {{ in_array($id, old('company', [])) ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @if($errors->has('company'))
                                        <span class="text-danger">{{ $errors->first('company') }}</span>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.user.fields.company_helper') }}</span>
                                </div> --}}
                            </div>

                            {{--                            <div class="form-group">--}}
                            {{--                                <div class="col-md-6 col-md-offset-4">--}}
                            {{--                                    <div class="checkbox">--}}
                            {{--                                        <label>--}}
                            {{--                                            <input type="checkbox" name="header" checked> @lang('global.app_file_contains_header_row')--}}
                            {{--                                        </label>--}}
                            {{--                                    </div>--}}
                            {{--                                </div>--}}
                            {{--                            </div>--}}

                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        @lang('global.app_parse_csv')
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
