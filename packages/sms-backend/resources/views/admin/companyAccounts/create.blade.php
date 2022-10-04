@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.companyAccount.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.company-accounts.store") }}" enctype="multipart/form-data">
                @csrf
                <x-input key='name' :model='app(\App\Models\CompanyAccount::class)'></x-input>
                <x-input key='bank_name' :model='app(\App\Models\CompanyAccount::class)' required=0></x-input>
                <x-input key='account_name' :model='app(\App\Models\CompanyAccount::class)' required=0></x-input>
                <x-input key='account_number' :model='app(\App\Models\CompanyAccount::class)' required=0></x-input>
                <x-select-company></x-select-company>
                <div class="form-group">
                    <button class="btn btn-danger" type="submit">
                        {{ trans('global.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>



@endsection