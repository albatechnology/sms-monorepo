<table>
    <thead>
        <tr>
            <th width="20" rowspan="2">Sales</th>
            <th width="20" rowspan="2">Channel</th>
            <th width="20" rowspan="2">BUM</th>
            <th width="20" rowspan="2">Total Leads</th>
            <th width="20" colspan="{{ $total_colspan + 1 }}">Estimated</th>
            <th width="20" colspan="{{ $total_colspan + 1 }}">Quotation</th>
        </tr>
        <tr>
            @if ($datas)
                @foreach ($datas[0]['product_brands'] as $brand)
                    <th width="20" style="background-color: #f00000">
                        {{ gettype($brand) == 'array' ? $brand['product_brand'] : $brand->product_brand }}</th>
                @endforeach
            @else
                <th width="20" style="background-color: #f00000"></th>
            @endif

            @if ($datas)
                <th width="20" style="background-color: #f00000">Total Estimated</th>
                @foreach ($datas[0]['product_brands'] as $brand)
                    <th width="20" style="background-color: #A9D08D">
                        {{ gettype($brand) == 'array' ? $brand['product_brand'] : $brand->product_brand }}</th>
                @endforeach
            @else
                <th width="20" style="background-color: #A9D08D"></th>
            @endif
            <th width="20" style="background-color: #A9D08D">Total Quotation</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($datas as $data)
            <tr>
                <td>{{ $data['sales'] }}</td>
                <td>{{ $data['channel'] }}</td>
                <td>{{ $data['bum'] }}</td>
                <td>{{ $data['total_leads'] ?? 0 }}</td>
                @if (isset($data['product_brands']))
                    @foreach ($data['product_brands'] as $brand)
                        <td>{{ gettype($brand) == 'array' ? $brand['product_brand'] : $brand->product_brand }}</td>
                        <td>{{ gettype($brand) == 'array' ? $brand['estimated_value'] ?? 0 : $brand->estimated_value ?? 0 }}
                        </td>
                    @endforeach
                    <td>{{ $data['total_estimated'] ?? 0 }}</td>
                    @foreach ($data['product_brands'] as $brand)
                        <td>{{ gettype($brand) == 'array' ? $brand['product_brand'] : $brand->product_brand }}</td>
                        <td>{{ gettype($brand) == 'array' ? $brand['order_value'] ?? 0 : $brand->order_value ?? 0 }}
                        </td>
                    @endforeach
                    <td>{{ $data['total_quotation'] ?? 0 }}</td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>
