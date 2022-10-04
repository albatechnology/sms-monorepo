@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{ trans('cruds.importLine.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST"
                  action="{{ route("admin.import-batches.import-lines.update", [$batch_id, $import_line->id]) }}"
                  enctype="multipart/form-data">
                @method('PUT')
                @csrf

                @foreach($import_line->data as $key => $value)
                    <div class="form-group">
                        <label for="{{ $key }}">{{ $key }}</label>
                        <input class="form-control {{ $errors->has($key) ? 'is-invalid' : '' }}" type="text"
                               name="{{ $key }}" id="{{ $key }}" value="{{ old($key, $value) }}">
                        @if($errors->has($key))
                            <span class="text-danger">{{ $errors->first($key) }}</span>
                        @endif
                    </div>
                @endforeach

                <div class="form-group">
                    <button class="btn btn-danger" type="submit">
                        {{ trans('global.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>



@endsection