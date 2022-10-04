@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            Delete customer by phone number
        </div>
        <div class="card-body">
            <form class="form-horizontal" method="POST" action="" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="form-group{{ $errors->has('csv_file') ? ' has-error' : '' }}">
                    <label for="csv_file" class="col-md-4 control-label">@lang('global.app_csv_file_to_import')</label>
                    <div class="col-md-6">
                        <input id="csv_file" type="file" class="form-control-file" name="csv_file" required>

                        @if ($errors->has('csv_file'))
                            <span class="help-block">
                                <strong>{{ $errors->first('csv_file') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
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
@endsection
