@if($payment ?? false)
    <a class="btn btn-xs btn-warning" href="{{ $viewRoute ?? route('admin.' . $crudRoutePart . '.payment', $row->id) }}">
        Payment
    </a>
@endif
@if($viewGate ?? false)
    @can($viewGate ?? false)
        <a class="btn btn-xs btn-primary" href="{{ $viewRoute ?? route('admin.' . $crudRoutePart . '.show', $row->id) }}">
            {{ trans('global.view') }}
        </a>
    @endcan
@endif
@can($editGate ?? false)
    <a class="btn btn-xs btn-info" href="{{ $editRoute ?? route('admin.' . $crudRoutePart . '.edit', $row->id) }}">
        {{ trans('global.edit') }}
    </a>
@endcan
@if($deleteGate ?? false)
@can($deleteGate ?? false)
    <form action="{{ $deleteRoute ?? route('admin.' . $crudRoutePart . '.destroy', $row->id) }}" method="POST"
          onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
        <input type="hidden" name="_method" value="DELETE">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
    </form>
@endcan
@endif
