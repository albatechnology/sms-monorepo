<table>
    <thead>
        <tr>
            <th rowspan="2" width="20" style="border: 1px solid black; background-color: #B3C6E7">NAME</th>
            <th rowspan="2" width="20" style="border: 1px solid black; background-color: #B3C6E7">LEADS</th>
            <th colspan="3" width="20" style="border: 1px solid black; background-color: #B3C6E7">STATUS</th>
            <th colspan="2" width="20" style="border: 1px solid black; background-color: #f00000">HOT</th>
            <th colspan="3" width="20" style="border: 1px solid black; background-color: #A9D08D">CLOSING DEALS</th>
        </tr>
        <tr>
            <th width="20" style="border: 1px solid black; background-color: #3074B5">COLD</th>
            <th width="20" style="border: 1px solid black; background-color: #FDDA64">WARM</th>
            <th width="20" style="border: 1px solid black; background-color: #f00000">ESTIMATED</th>
            <th width="20" style="border: 1px solid black; background-color: #f00000">No of LEADS</th>
            <th width="20" style="border: 1px solid black; background-color: #f00000">QUOTATION</th>
            <th width="20" style="border: 1px solid black; background-color: #A9D08D">No of LEADS</th>
            <th width="20" style="border: 1px solid black; background-color: #A9D08D">INVOICE PRICE</th>
            <th width="20" style="border: 1px solid black; background-color: #A9D08D">AMOUNT PAID</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($datas['data'] as $data)
            <tr>
                <td width="20" style="border: 1px solid black; background-color: #BCD8F0">{{ $data['name'] }} (BUM)</td>
                <td width="20" style="border: 1px solid black; background-color: #BCD8F0">{{ isset($data['total_leads']) && $data['total_leads'] != '' && $data['total_leads'] != null ? number_format($data['total_leads'], 0) : number_format(0, 2, '.', '') }}</td>
                <td width="20" style="border: 1px solid black; background-color: #BCD8F0">{{ isset($data['cold']) && $data['cold'] != '' && $data['cold'] != null ? number_format($data['cold'], 0) : number_format(0, 2, '.', '') }}</td>
                <td width="20" style="border: 1px solid black; background-color: #BCD8F0">{{ isset($data['warm']) && $data['warm'] != '' && $data['warm'] != null ? number_format($data['warm'], 0) : number_format(0, 2, '.', '') }}</td>
                <td width="20" style="border: 1px solid black; background-color: #BCD8F0">{{ isset($data['estimated_value']) && $data['estimated_value'] != '' && $data['estimated_value'] != null ? number_format($data['estimated_value'], 0) : number_format(0, 2, '.', '') }}</td>
                <td width="20" style="border: 1px solid black; background-color: #BCD8F0">{{ isset($data['hot']) && $data['hot'] != '' && $data['hot'] != null ? number_format($data['hot'], 0) : number_format(0, 2, '.', '') }}</td>
                <td width="20" style="border: 1px solid black; background-color: #BCD8F0">{{ isset($data['quotation']) && $data['quotation'] != '' && $data['quotation'] != null ? number_format($data['quotation'], 0) : number_format(0, 2, '.', '') }}</td>
                <td width="20" style="border: 1px solid black; background-color: #BCD8F0">{{ isset($data['total_lead_deals']) && $data['total_lead_deals'] != '' && $data['total_lead_deals'] != null ? number_format($data['total_lead_deals'], 0) : number_format(0, 2, '.', '') }}</td>
                <td width="20" style="border: 1px solid black; background-color: #BCD8F0">{{ isset($data['invoice_price']) && $data['invoice_price'] != '' && $data['invoice_price'] != null ? number_format($data['invoice_price'], 0) : number_format(0, 2, '.', '') }}</td>
                <td width="20" style="border: 1px solid black; background-color: #BCD8F0">{{ isset($data['amount_paid']) && $data['amount_paid'] != '' && $data['amount_paid'] != null ? number_format($data['amount_paid'], 0) : number_format(0, 2, '.', '') }}</td>
            </tr>
            @if (isset($data['channels']))
                @foreach ($data['channels'] as $channel)
                    <tr>
                        <td width="20" style="border: 1px solid black; background-color: #DDEBF6">{{ $channel['name'] }} (Channel)</td>
                        <td width="20" style="border: 1px solid black; background-color: #DDEBF6">{{ isset($channel['total_leads']) && $channel['total_leads'] != '' && $channel['total_leads'] != null ? number_format($channel['total_leads'], 0) : number_format(0, 2, '.', '') }}</td>
                        <td width="20" style="border: 1px solid black; background-color: #DDEBF6">{{ isset($channel['cold']) && $channel['cold'] != '' && $channel['cold'] != null ? number_format($channel['cold'], 0) : number_format(0, 2, '.', '') }}</td>
                        <td width="20" style="border: 1px solid black; background-color: #DDEBF6">{{ isset($channel['warm']) && $channel['warm'] != '' && $channel['warm'] != null ? number_format($channel['warm'], 0) : number_format(0, 2, '.', '') }}</td>
                        <td width="20" style="border: 1px solid black; background-color: #DDEBF6">{{ isset($channel['estimated_value']) && $channel['estimated_value'] != '' && $channel['estimated_value'] != null ? number_format($channel['estimated_value'], 0) : number_format(0, 2, '.', '') }}</td>
                        <td width="20" style="border: 1px solid black; background-color: #DDEBF6">{{ isset($channel['hot']) && $channel['hot'] != '' && $channel['hot'] != null ? number_format($channel['hot'], 0) : number_format(0, 2, '.', '') }}</td>
                        <td width="20" style="border: 1px solid black; background-color: #DDEBF6">{{ isset($channel['quotation']) && $channel['quotation'] != '' && $channel['quotation'] != null ? number_format($channel['quotation'], 0) : number_format(0, 2, '.', '') }}</td>
                        <td width="20" style="border: 1px solid black; background-color: #DDEBF6">{{ isset($channel['total_lead_deals']) && $channel['total_lead_deals'] != '' && $channel['total_lead_deals'] != null ? number_format($channel['total_lead_deals'], 0) : number_format(0, 2, '.', '') }}</td>
                        <td width="20" style="border: 1px solid black; background-color: #DDEBF6">{{ isset($channel['invoice_price']) && $channel['invoice_price'] != '' && $channel['invoice_price'] != null ? number_format($channel['invoice_price'], 0) : number_format(0, 2, '.', '') }}</td>
                        <td width="20" style="border: 1px solid black; background-color: #DDEBF6">{{ isset($channel['amount_paid']) && $channel['amount_paid'] != '' && $channel['amount_paid'] != null ? number_format($channel['amount_paid'], 0) : number_format(0, 2, '.', '') }}</td>
                    </tr>
                    @if (isset($channel['sales']))
                        @foreach ($channel['sales'] as $sales)
                            <tr>
                                <td width="20" style="border: 1px solid black;">{{ $sales->name }} (Sales)</td>
                                <td width="20" style="border: 1px solid black;">{{ isset($sales->total_leads) && $sales->total_leads != '' && $sales->total_leads != null ? number_format($sales->total_leads, 0) : number_format(0, 2, '.', '') }}</td>
                                <td width="20" style="border: 1px solid black;">{{ isset($sales->cold) && $sales->cold != '' && $sales->cold != null ? number_format($sales->cold, 0) : number_format(0, 2, '.', '') }}</td>
                                <td width="20" style="border: 1px solid black;">{{ isset($sales->warm) && $sales->warm != '' && $sales->warm != null ? number_format($sales->warm, 0) : number_format(0, 2, '.', '') }}</td>
                                <td width="20" style="border: 1px solid black;">{{ isset($sales->estimated_value) && $sales->estimated_value != '' && $sales->estimated_value != null ? number_format($sales->estimated_value, 0) : number_format(0, 2, '.', '') }}</td>
                                <td width="20" style="border: 1px solid black;">{{ isset($sales->hot) && $sales->hot != '' && $sales->hot != null ? number_format($sales->hot, 0) : number_format(0, 2, '.', '') }}</td>
                                <td width="20" style="border: 1px solid black;">{{ isset($sales->quotation) && $sales->quotation != '' && $sales->quotation != null ? number_format($sales->quotation, 0) : number_format(0, 2, '.', '') }}</td>
                                <td width="20" style="border: 1px solid black;">{{ isset($sales->total_lead_deals) && $sales->total_lead_deals != '' && $sales->total_lead_deals != null ? number_format($sales->total_lead_deals, 0) : number_format(0, 2, '.', '') }}</td>
                                <td width="20" style="border: 1px solid black;">{{ isset($sales->invoice_price) && $sales->invoice_price != '' && $sales->invoice_price != null ? number_format($sales->invoice_price, 0) : number_format(0, 2, '.', '') }}</td>
                                <td width="20" style="border: 1px solid black;">{{ isset($sales->amount_paid) && $sales->amount_paid != '' && $sales->amount_paid != null ? number_format($sales->amount_paid, 0) : number_format(0, 2, '.', '') }}</td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
            @endif
        @endforeach
        @isset($datas['total'])
        <tr>
            <td width="20" style="border: 1px solid black; background-color: #17949D; font-weight: bold;"></td>
            <td width="20" style="border: 1px solid black; background-color: #17949D; font-weight: bold;">{{ isset($datas['total']['total_leads']) && $datas['total']['total_leads'] != '' && $datas['total']['total_leads'] != null ? number_format($datas['total']['total_leads'], 0) : number_format(0, 2, '.', '') }}</td>
            <td width="20" style="border: 1px solid black; background-color: #17949D; font-weight: bold;">{{ isset($datas['total']['cold']) && $datas['total']['cold'] != '' && $datas['total']['cold'] != null ? number_format($datas['total']['cold'], 0) : number_format(0, 2, '.', '') }}</td>
            <td width="20" style="border: 1px solid black; background-color: #17949D; font-weight: bold;">{{ isset($datas['total']['warm']) && $datas['total']['warm'] != '' && $datas['total']['warm'] != null ? number_format($datas['total']['warm'], 0) : number_format(0, 2, '.', '') }}</td>
            <td width="20" style="border: 1px solid black; background-color: #17949D; font-weight: bold;">{{ isset($datas['total']['estimated_value']) && $datas['total']['estimated_value'] != '' && $datas['total']['estimated_value'] != null ? number_format($datas['total']['estimated_value'], 0) : number_format(0, 2, '.', '') }}</td>
            <td width="20" style="border: 1px solid black; background-color: #17949D; font-weight: bold;">{{ isset($datas['total']['hot']) && $datas['total']['hot'] != '' && $datas['total']['hot'] != null ? number_format($datas['total']['hot'], 0) : number_format(0, 2, '.', '') }}</td>
            <td width="20" style="border: 1px solid black; background-color: #17949D; font-weight: bold;">{{ isset($datas['total']['quotation']) && $datas['total']['quotation'] != '' && $datas['total']['quotation'] != null ? number_format($datas['total']['quotation'], 0) : number_format(0, 2, '.', '') }}</td>
            <td width="20" style="border: 1px solid black; background-color: #17949D; font-weight: bold;">{{ isset($datas['total']['total_lead_deals']) && $datas['total']['total_lead_deals'] != '' && $datas['total']['total_lead_deals'] != null ? number_format($datas['total']['total_lead_deals'], 0) : number_format(0, 2, '.', '') }}</td>
            <td width="20" style="border: 1px solid black; background-color: #17949D; font-weight: bold;">{{ isset($datas['total']['invoice_price']) && $datas['total']['invoice_price'] != '' && $datas['total']['invoice_price'] != null ? number_format($datas['total']['invoice_price'], 0) : number_format(0, 2, '.', '') }}</td>
            <td width="20" style="border: 1px solid black; background-color: #17949D; font-weight: bold;">{{ isset($datas['total']['amount_paid']) && $datas['total']['amount_paid'] != '' && $datas['total']['amount_paid'] != null ? number_format($datas['total']['amount_paid'], 0) : number_format(0, 2, '.', '') }}</td>
        </tr>
        @endisset
    </tbody>
</table>
