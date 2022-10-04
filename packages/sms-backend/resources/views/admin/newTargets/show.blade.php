@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.target.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.targets.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                <x-show-row :model="$target" key="id"></x-show-row>
                <x-show-row :model="$target" key="type"></x-show-row>
                <x-show-row :model="$target" key="target" value="{{ $target->targetFormatted }}"></x-show-row>
                <x-show-row :model="$target" key="value" value="{{ $target->valueFormatted }}"></x-show-row>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.targets.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection