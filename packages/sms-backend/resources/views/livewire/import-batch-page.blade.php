<div @if($shouldPoll) wire:poll.2000ms="poll" @endif>
    <div class="form-group">
        <table class="table table-bordered table-striped">
            <tbody>
            <tr>
                <th>
                    {{ trans('cruds.importBatch.fields.id') }}
                </th>
                <td>
                    {{ $batch->id }}
                </td>
            </tr>
            <tr>
                <th>
                    {{ trans('cruds.importBatch.fields.filename') }}
                </th>
                <td>
                    {{ $batch->filename }}
                </td>
            </tr>
            <tr>
                <th>
                    {{ trans('cruds.importBatch.fields.status') }}
                </th>
                <td>
                    <span class="external-event bg-{{ $batch->status->getColourSchema() }}">
                        @if($batch->status->isLoading())
                            <i class="fa fa-spin fa-sync-alt"></i>
                        @endif
                        {{ $batch->status->description ?? '' }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>
                    {{ trans('cruds.importBatch.fields.type') }}
                </th>
                <td>
                    {{ $batch->type?->description ?? '' }}
                </td>
            </tr>
            <tr>
                <th>
                    {{ trans('cruds.importBatch.fields.summary') }}
                </th>
                <td>
                    {{ $batch->summary ?? '' }}
                </td>
            </tr>
            <tr>
                <th>
                    {{ trans('cruds.importBatch.fields.preview_summary') }}
                </th>
                <td>
                    {{ $batch->preview_summary ?? '' }}
                </td>
            </tr>
            <tr>
                <th>
                    {{ trans('cruds.importBatch.fields.errors') }}
                </th>
                <td>
                    @if(!empty($batch->errors ?? []))
                        <ul>
                            @foreach($batch->errors as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </td>
            </tr>
            <tr>
                <th>
                    {{ trans('cruds.importBatch.fields.updated_at') }}
                </th>
                <td>
                    {{ $batch->updated_at }}
                </td>
            </tr>
            <tr>
                <th>
                    {{ trans('cruds.importBatch.fields.created_at') }}
                </th>
                <td>
                    {{ $batch->created_at }}
                </td>
            </tr>
            </tbody>
        </table>

        @if($batch->status->isImporting())
            <div class="progress">
                <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated"
                     role="progressbar"
                     aria-valuenow="{{ $progress }}%" aria-valuemin="0" aria-valuemax="100"
                     style="width: {{ $progress }}%">
                    {{ $progress }}% Complete
                </div>
            </div>
            <br>
        @endif

        <div class="form-group">
            @if($batch->processable())
                <button class="btn btn-primary" wire:click="processUpdate">Process (Update duplicates)</button>
                <button class="btn btn-secondary" wire:click="processSkip">Process (Skip duplicates)</button>
                <button class="btn btn-secondary" wire:click="processBulkInsert">Process (Bulk Insert)</button>
            @endif
            @if($batch->status->cancellable())
                <button class="btn btn-danger" wire:click="cancelImport">{{ trans('global.cancel') }}</button>
            @endif
        </div>
    </div>
</div>
