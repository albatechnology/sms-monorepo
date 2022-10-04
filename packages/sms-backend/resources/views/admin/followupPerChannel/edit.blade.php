@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{ trans('cruds.report.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.reports.update", [$report->id]) }}"
                  enctype="multipart/form-data">
                @method('PUT')
                @csrf

                <x-input key='name' :model='$report'></x-input>
                <x-input key='start_date' :model='$report' type="datetime"></x-input>
                <x-input key='end_date' :model='$report' type="datetime"></x-input>

                @livewire('report-page', ['reportable_type' => $report->reportable_type->value])

                <div class="form-group">
                    <button class="btn btn-danger" type="submit">
                        {{ trans('global.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>



@endsection
