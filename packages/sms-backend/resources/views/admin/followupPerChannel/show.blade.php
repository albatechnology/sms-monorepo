@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.report.title') }}
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.reports.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                    <x-show-row :model="$report" key="id"></x-show-row>
                    <x-show-row :model="$report" key="name"></x-show-row>
                    <x-show-row :model="$report" key="type"
                                value="{{ $report->reportable_type->key }}"></x-show-row>
                    <x-show-row :model="$report" key="reportable_label"></x-show-row>
                    <x-show-row :model="$report" key="start_date"></x-show-row>
                    <x-show-row :model="$report" key="end_date"></x-show-row>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-default" href="{{ route('admin.reports.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                    <a class="btn btn-warning" href="{{ route('admin.reports.reevaluate', [$report->id]) }}">
                        {{ trans('cruds.report.fields.reevaluate') }}
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
                <a class="nav-link" href="#reports_targets" role="tab" data-toggle="tab">
                    {{ trans('cruds.target.title') }}
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane" role="tabpanel" id="reports_targets">
                @includeIf('admin.reports.relationships.reportsTargets', ['targets' => $report->targets])
            </div>
        </div>
    </div>

@endsection