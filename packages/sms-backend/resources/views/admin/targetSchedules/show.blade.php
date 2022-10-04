@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.targetSchedule.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.target-schedules.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.targetSchedule.fields.id') }}
                        </th>
                        <td>
                            {{ $targetSchedule->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.targetSchedule.fields.duration_type') }}
                        </th>
                        <td>
                            {{ App\Models\TargetSchedule::DURATION_TYPE_SELECT[$targetSchedule->duration_type] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.targetSchedule.fields.start_date') }}
                        </th>
                        <td>
                            {{ $targetSchedule->start_date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.targetSchedule.fields.target_name') }}
                        </th>
                        <td>
                            {{ $targetSchedule->target_name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.targetSchedule.fields.value') }}
                        </th>
                        <td>
                            {{ $targetSchedule->value }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.targetSchedule.fields.target_value_type') }}
                        </th>
                        <td>
                            {{ App\Models\TargetSchedule::TARGET_VALUE_TYPE_SELECT[$targetSchedule->target_value_type] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.targetSchedule.fields.target_type') }}
                        </th>
                        <td>
                            {{ App\Models\TargetSchedule::TARGET_TYPE_SELECT[$targetSchedule->target_type] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.targetSchedule.fields.target_subject') }}
                        </th>
                        <td>
                            {{ App\Models\TargetSchedule::TARGET_SUBJECT_SELECT[$targetSchedule->target_subject] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.targetSchedule.fields.target_subject_type') }}
                        </th>
                        <td>
                            {{ App\Models\TargetSchedule::TARGET_SUBJECT_TYPE_SELECT[$targetSchedule->target_subject_type] ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.target-schedules.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection