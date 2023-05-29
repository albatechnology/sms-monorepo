@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.supervisorType.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.supervisor-types.update", [$supervisorType->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.supervisorType.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', $supervisorType->name) }}" required>
                @if($errors->has('name'))
                    <span class="text-danger">{{ $errors->first('name') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.supervisorType.fields.name_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="level">{{ trans('cruds.supervisorType.fields.level') }}</label>
                <input class="form-control {{ $errors->has('level') ? 'is-invalid' : '' }}" type="number" name="level" id="level" value="{{ old('level', $supervisorType->level) }}" step="1">
                @if($errors->has('level'))
                    <span class="text-danger">{{ $errors->first('level') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.supervisorType.fields.level_helper') }}</span>
            </div>
            {{-- <x-input key='discount_approval_limit_percentage' :model='$supervisorType' type="number" required="0"></x-input> --}}
            <div class="form-group">
                <label>{{ trans('cruds.supervisorType.fields.discount_approval_limit_percentage') }}</label>
                <span class="help-block">(percentage)</span>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        @foreach ($productBrands as $id => $name)
                        <tr>
                            <td>{{$name}}</td>
                            <td><input type="number" name="discount_approval_limit_percentage[{{$id}}]" class="form-control" required min="0" max="100" value="{{ $supervisorTypeLimits[$id] ?? 0 }}"></td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
            <div class="form-group">
                <div class="form-check {{ $errors->has('can_assign_lead') ? 'is-invalid' : '' }}">
                    <input type="hidden" name="can_assign_lead" value="0">
                    <input class="form-check-input" type="checkbox" name="can_assign_lead" id="can_assign_lead" value="1" {{ $supervisorType->can_assign_lead || old('can_assign_lead', 0) === 1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="can_assign_lead">{{ trans('cruds.supervisorType.fields.can_assign_lead') }}</label>
                </div>
                @if($errors->has('can_assign_lead'))
                    <span class="text-danger">{{ $errors->first('can_assign_lead') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.supervisorType.fields.can_assign_lead_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection
