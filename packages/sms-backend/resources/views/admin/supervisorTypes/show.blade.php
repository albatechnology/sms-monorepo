@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.supervisorType.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.supervisor-types.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.supervisorType.fields.id') }}
                        </th>
                        <td>
                            {{ $supervisorType->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.supervisorType.fields.name') }}
                        </th>
                        <td>
                            {{ $supervisorType->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.supervisorType.fields.level') }}
                        </th>
                        <td>
                            {{ $supervisorType->level }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.supervisorType.fields.can_assign_lead') }}
                        </th>
                        <td>
                            {{ $supervisorType->can_assign_lead ? 'TRUE' : 'FALSE' }}
                        </td>
                    </tr>
                    <x-show-row :model="$supervisorType" key="discount_approval_limit_percentage"></x-show-row>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.supervisor-types.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        {{ trans('global.relatedData') }}
    </div>
    <ul class="nav nav-tabs" role="tablist" id="relationship-tabs">
        <li class="nav-item">
            <a class="nav-link" href="#supervisor_type_users" role="tab" data-toggle="tab">
                {{ trans('cruds.user.title') }}
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane" role="tabpanel" id="supervisor_type_users">
            @includeIf('admin.supervisorTypes.relationships.supervisorTypeUsers', ['users' => $supervisorType->supervisorTypeUsers])
        </div>
    </div>
</div>

@endsection