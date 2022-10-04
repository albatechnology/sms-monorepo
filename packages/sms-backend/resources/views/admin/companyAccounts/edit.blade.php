@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{ trans('cruds.companyAccount.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route("admin.company-accounts.update", [$companyAccount->id]) }}"
                  enctype="multipart/form-data">
                @method('PUT')
                @csrf

                <x-input key='name' :model='$companyAccount' required=0></x-input>
                <x-input key='bank_name' :model='$companyAccount' required=0></x-input>
                <x-input key='account_name' :model='$companyAccount' required=0></x-input>
                <x-input key='account_number' :model='$companyAccount' required=0></x-input>
                <div class="form-group">
                    <button class="btn btn-danger" type="submit">
                        {{ trans('global.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>



@endsection