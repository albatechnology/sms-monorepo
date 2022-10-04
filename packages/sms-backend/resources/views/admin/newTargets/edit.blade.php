@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{ trans('cruds.target.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.new-targets.update", [$new_target->id]) }}"
                  enctype="multipart/form-data">
                @method('PUT')
                @csrf

                <x-input key='type' :model='$new_target' required="0" disabled="1"
                         value="{{ $new_target->type->key }}"></x-input>
                <x-input key='target' :model='$new_target'></x-input>

                <div class="form-group">
                    <button class="btn btn-danger" type="submit">
                        {{ trans('global.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>



@endsection
