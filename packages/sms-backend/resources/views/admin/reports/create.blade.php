@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.report.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.reports.store") }}" enctype="multipart/form-data">
                @csrf

                <x-input key='name' :model='app(\App\Models\Report::class)'></x-input>
                <x-input key='start_date' :model='app(\App\Models\Report::class)' type="datetime"></x-input>
                <x-input key='end_date' :model='app(\App\Models\Report::class)' type="datetime"></x-input>

                @livewire('report-page')

                <div class="form-group">
                    <button class="btn btn-danger" type="submit">
                        {{ trans('global.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection