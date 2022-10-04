@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{ trans('cruds.activity.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.activities.update', [$activity->id]) }}"
                enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="form-group">
                    <label class="required" for="user_id">{{ trans('cruds.activity.fields.user') }}</label>
                    <select class="form-control select2 {{ $errors->has('user') ? 'is-invalid' : '' }}" name="user_id"
                        id="user_id" required>
                        @foreach ($users as $id => $user)
                            <option value="{{ $id }}"
                                {{ (old('user_id') ? old('user_id') : $activity->user->id ?? '') == $id ? 'selected' : '' }}>
                                {{ $user }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('user'))
                        <span class="text-danger">{{ $errors->first('user') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.activity.fields.user_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="lead_id">{{ trans('cruds.activity.fields.lead') }}</label>
                    <select class="form-control select2 {{ $errors->has('lead') ? 'is-invalid' : '' }}" name="lead_id"
                        id="lead_id" required>
                        @foreach ($leads as $id => $lead)
                            <option value="{{ $id }}"
                                {{ (old('lead_id') ? old('lead_id') : $activity->lead->id ?? '') == $id ? 'selected' : '' }}>
                                {{ $lead }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('lead'))
                        <span class="text-danger">{{ $errors->first('lead') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.activity.fields.lead_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="customer_id">{{ trans('cruds.activity.fields.customer') }}</label>
                    <select class="form-control select2 {{ $errors->has('customer') ? 'is-invalid' : '' }}"
                        name="customer_id" id="customer_id" required>
                        @foreach ($customers as $id => $customer)
                            <option value="{{ $id }}"
                                {{ (old('customer_id') ? old('customer_id') : $activity->customer->id ?? '') == $id ? 'selected' : '' }}>
                                {{ $customer }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('lead'))
                        <span class="text-danger">{{ $errors->first('lead') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.activity.fields.customer_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required"
                        for="follow_up_datetime">{{ trans('cruds.activity.fields.follow_up_datetime') }}</label>
                    <input class="form-control datetime {{ $errors->has('follow_up_datetime') ? 'is-invalid' : '' }}"
                        type="text" name="follow_up_datetime" id="follow_up_datetime"
                        value="{{ old('follow_up_datetime', $activity->follow_up_datetime) }}" required>
                    @if ($errors->has('follow_up_datetime'))
                        <span class="text-danger">{{ $errors->first('follow_up_datetime') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.activity.fields.follow_up_datetime_helper') }}</span>
                </div>
                <div class="form-group">
                    <label class="required" for="created_at">Created At</label>
                    <input class="form-control datetime {{ $errors->has('created_at') ? 'is-invalid' : '' }}" type="text"
                        name="created_at" id="created_at" value="{{ old('created_at', $activity->created_at) }}"
                        required>
                    @if ($errors->has('created_at'))
                        <span class="text-danger">{{ $errors->first('created_at') }}</span>
                    @endif
                    <span class="help-block">{{ trans('cruds.activity.fields.created_at_helper') }}</span>
                </div>
                <x-enum key='follow_up_method' :model='$activity'></x-enum>

                <x-input key='feedback' :model='$activity' required="0"></x-input>

                <x-enum key='status' :model='$activity'></x-enum>

                <div class="form-group">
                    <button class="btn btn-danger" type="submit">
                        {{ trans('global.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>



@endsection
