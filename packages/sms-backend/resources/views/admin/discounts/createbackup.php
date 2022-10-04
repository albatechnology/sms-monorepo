@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.discount.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.discounts.store") }}" enctype="multipart/form-data">
            @csrf
            @livewire('discount-page')

            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
@push('js')
<script type="text/javascript">
    $(function() {
        $('#selectProduct').select2({
            placeholder: 'Select an products',
            minimumInputLength: 4,
            ajax: {
                url: '{{ route("admin.orders.getproduct") }}',
                dataType: 'json',
                delay: 250,
                processResults: function (data) {
                    return {
                        results:  $.map(data, function (item) {
                            return {
                                text: item.name,
                                id: item.id
                            }
                        })
                    };
                },
                cache: true
            }
        })
    });
</script>
@endpush