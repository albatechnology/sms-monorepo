<td>
    <select class='search' strict='true'>
        <option value>{{ trans('global.all') }}</option>
        @foreach($enums as $enum)
            <option value='{{ $enum->value }}'>{{ $enum->description }}</option>
        @endforeach
    </select>
</td>