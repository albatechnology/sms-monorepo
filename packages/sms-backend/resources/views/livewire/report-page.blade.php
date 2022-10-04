<div>

    <div class="form-group">
        <label class="required" for="reportable_type">{{ trans('cruds.report.fields.type') }}</label>
        <select wire:model="reportable_type"
                class="form-control {{ $errors->has('reportable_type') ? 'is-invalid' : '' }}"
                name="reportable_type" id="reportable_type" required>

            <option value="">-- choose type --</option>
            {{--            @foreach ($reportable_types as $key)--}}
            {{--                <option value="{{ $key }}" {{ old('reportable_type') == $key ? 'selected' : '' }}>{{ $key }}</option>--}}
            {{--            @endforeach--}}
            @foreach (App\Enums\ReportableType::getInstances() as $enum)
                <option value="{{ $enum->value }}" {{ old('reportable_type') == $enum->value ? 'selected' : '' }}>{{ $enum->key }}</option>
            @endforeach
        </select>
        @if($errors->has('reportable_type'))
            <span class="text-danger">{{ $errors->first('reportable_type') }}</span>
        @endif
        <span class="help-block">{{ trans('cruds.report.fields.type_helper') }}</span>
    </div>

    <div class="form-group">
        <label class="required" for="reportable_id">{{ trans('cruds.report.fields.reportable_label') }}</label>
        <select class="form-control select2 {{ $errors->has('payment_type') ? 'is-invalid' : '' }}"
                name="reportable_id" id="reportable_id" required>

            @if ($reportable_models->count() == 0)
                <option value="">-- choose type first --</option>
            @endif
            @foreach($reportable_models as $model)
                <option value="{{ $model->id }}" {{ ($reportable_id ?? old('reportable_id')) == $model->id ? 'selected' : '' }} >{{ $model->getReportLabel() }}</option>
            @endforeach
        </select>
        @if($errors->has('reportable_id'))
            <span class="text-danger">{{ $errors->first('reportable_id') }}</span>
        @endif
        <span class="help-block">{{ trans('cruds.report.fields.reportable_label_helper') }}</span>
    </div>
</div>
