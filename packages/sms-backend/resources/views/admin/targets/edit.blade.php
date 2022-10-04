@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{ trans('cruds.target.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.targets.update", [$target->id]) }}"
                  enctype="multipart/form-data">
                @method('PUT')
                @csrf

                <x-input key='type' :model='$target' required="0" disabled="1"
                         value="{{ $target->type->key }}"></x-input>
                <x-input key='target' :model='$target'></x-input>

                <div class="form-group">
                    <button class="btn btn-danger" type="submit">
                        {{ trans('global.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>



@endsection