<table>
    <thead>
        <tr>
            <th rowspan="2" width="20" style="border: 1px solid black; background-color: #B3C6E7">NAME</th>
            <th rowspan="2" width="20" style="border: 1px solid black; background-color: #B3C6E7">LEADS</th>
            <th colspan="3" width="20" style="border: 1px solid black; background-color: #B3C6E7">STATUS</th>
            <th colspan="2" width="20" style="border: 1px solid black; background-color: #f00000">HOT</th>
            <th colspan="3" width="20" style="border: 1px solid black; background-color: #A9D08D">CLOSING DEALS
            </th>
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
        @foreach ($datas['original']['data'] as $data)
            <tr>
                <td width="20" style="border: 1px solid black; background-color: #BCD8F0">{{ $data['name'] }} (BUM)
                </td>
                <td width="20" style="border: 1px solid black; background-color: #BCD8F0">
                    {{ isset($data['total_leads']) && $data['total_leads'] != '' && $data['total_leads'] != null ? number_format($data['total_leads'], 0) : number_format(0, 2, '.', '') }}
                </td>
                <td width="20" style="border: 1px solid black; background-color: #BCD8F0">
                    {{ isset($data['cold_activity']) && $data['cold_activity'] != '' && $data['cold_activity'] != null ? number_format($data['cold_activity'], 0) : number_format(0, 2, '.', '') }}
                </td>
                <td width="20" style="border: 1px solid black; background-color: #BCD8F0">
                    {{ isset($data['warm_activity']) && $data['warm_activity'] != '' && $data['warm_activity'] != null ? number_format($data['warm_activity'], 0) : number_format(0, 2, '.', '') }}
                </td>
                <td width="20" style="border: 1px solid black; background-color: #BCD8F0">
                    {{ isset($data['estimated_value']) && $data['estimated_value'] != '' && $data['estimated_value'] != null ? number_format($data['estimated_value'], 0) : number_format(0, 2, '.', '') }}
                </td>
                <td width="20" style="border: 1px solid black; background-color: #BCD8F0">
                    {{ isset($data['hot_activity']) && $data['hot_activity'] != '' && $data['hot_activity'] != null ? number_format($data['hot_activity'], 0) : number_format(0, 2, '.', '') }}
                </td>
                <td width="20" style="border: 1px solid black; background-color: #BCD8F0">
                    {{ isset($data['quotation']) && $data['quotation'] != '' && $data['quotation'] != null ? number_format($data['quotation'], 0) : number_format(0, 2, '.', '') }}
                </td>
                <td width="20" style="border: 1px solid black; background-color: #BCD8F0">
                    {{ isset($data['deal_leads']) && $data['deal_leads'] != '' && $data['deal_leads'] != null ? number_format($data['deal_leads'], 0) : number_format(0, 2, '.', '') }}
                </td>
                <td width="20" style="border: 1px solid black; background-color: #BCD8F0">
                    {{ isset($data['invoice_price']) && $data['invoice_price'] != '' && $data['invoice_price'] != null ? number_format($data['invoice_price'], 0) : number_format(0, 2, '.', '') }}
                </td>
                <td width="20" style="border: 1px solid black; background-color: #BCD8F0">
                    {{ isset($data['amount_paid']) && $data['amount_paid'] != '' && $data['amount_paid'] != null ? number_format($data['amount_paid'], 0) : number_format(0, 2, '.', '') }}
                </td>
            </tr>
            @if (isset($data['channels']))
                @foreach ($data['channels'] as $channel)
                    <tr>
                        <td width="20" style="border: 1px solid black; background-color: #DDEBF6">
                            {{ $channel['name'] }} (Channel)</td>
                        <td width="20" style="border: 1px solid black; background-color: #DDEBF6">
                            {{ isset($channel['total_leads']) && $channel['total_leads'] != '' && $channel['total_leads'] != null ? number_format($channel['total_leads'], 0) : number_format(0, 2, '.', '') }}
                        </td>
                        <td width="20" style="border: 1px solid black; background-color: #DDEBF6">
                            {{ isset($channel['cold_activity']) && $channel['cold_activity'] != '' && $channel['cold_activity'] != null ? number_format($channel['cold_activity'], 0) : number_format(0, 2, '.', '') }}
                        </td>
                        <td width="20" style="border: 1px solid black; background-color: #DDEBF6">
                            {{ isset($channel['warm_activity']) && $channel['warm_activity'] != '' && $channel['warm_activity'] != null ? number_format($channel['warm_activity'], 0) : number_format(0, 2, '.', '') }}
                        </td>
                        <td width="20" style="border: 1px solid black; background-color: #DDEBF6">
                            {{ isset($channel['estimated_value']) && $channel['estimated_value'] != '' && $channel['estimated_value'] != null ? number_format($channel['estimated_value'], 0) : number_format(0, 2, '.', '') }}
                        </td>
                        <td width="20" style="border: 1px solid black; background-color: #DDEBF6">
                            {{ isset($channel['hot_activity']) && $channel['hot_activity'] != '' && $channel['hot_activity'] != null ? number_format($channel['hot_activity'], 0) : number_format(0, 2, '.', '') }}
                        </td>
                        <td width="20" style="border: 1px solid black; background-color: #DDEBF6">
                            {{ isset($channel['quotation']) && $channel['quotation'] != '' && $channel['quotation'] != null ? number_format($channel['quotation'], 0) : number_format(0, 2, '.', '') }}
                        </td>
                        <td width="20" style="border: 1px solid black; background-color: #DDEBF6">
                            {{ isset($channel['deal_leads']) && $channel['deal_leads'] != '' && $channel['deal_leads'] != null ? number_format($channel['deal_leads'], 0) : number_format(0, 2, '.', '') }}
                        </td>
                        <td width="20" style="border: 1px solid black; background-color: #DDEBF6">
                            {{ isset($channel['invoice_price']) && $channel['invoice_price'] != '' && $channel['invoice_price'] != null ? number_format($channel['invoice_price'], 0) : number_format(0, 2, '.', '') }}
                        </td>
                        <td width="20" style="border: 1px solid black; background-color: #DDEBF6">
                            {{ isset($channel['amount_paid']) && $channel['amount_paid'] != '' && $channel['amount_paid'] != null ? number_format($channel['amount_paid'], 0) : number_format(0, 2, '.', '') }}
                        </td>
                    </tr>
                    @if (isset($channel['sales']))
                        @foreach ($channel['sales'] as $sales)
                            <tr>
                                <td width="20" style="border: 1px solid black;">{{ $sales['name'] }} (Sales)</td>
                                <td width="20" style="border: 1px solid black;">
                                    {{ isset($sales['total_leads']) && $sales['total_leads'] != '' && $sales['total_leads'] != null ? number_format($sales['total_leads'], 0) : number_format(0, 2, '.', '') }}
                                </td>
                                <td width="20" style="border: 1px solid black;">
                                    {{ isset($sales['cold']) && $sales['cold'] != '' && $sales['cold'] != null ? number_format($sales['cold'], 0) : number_format(0, 2, '.', '') }}
                                </td>
                                <td width="20" style="border: 1px solid black;">
                                    {{ isset($sales['warm']) && $sales['warm'] != '' && $sales['warm'] != null ? number_format($sales['warm'], 0) : number_format(0, 2, '.', '') }}
                                </td>
                                <td width="20" style="border: 1px solid black;">
                                    {{ isset($sales['estimated_value']) && $sales['estimated_value'] != '' && $sales['estimated_value'] != null ? number_format($sales['estimated_value'], 0) : number_format(0, 2, '.', '') }}
                                </td>
                                <td width="20" style="border: 1px solid black;">
                                    {{ isset($sales['hot']) && $sales['hot'] != '' && $sales['hot'] != null ? number_format($sales['hot'], 0) : number_format(0, 2, '.', '') }}
                                </td>
                                <td width="20" style="border: 1px solid black;">
                                    {{ isset($sales['quotation']) && $sales['quotation'] != '' && $sales['quotation'] != null ? number_format($sales['quotation'], 0) : number_format(0, 2, '.', '') }}
                                </td>
                                <td width="20" style="border: 1px solid black;">
                                    {{ isset($sales['deal_leads']) && $sales['deal_leads'] != '' && $sales['deal_leads'] != null ? number_format($sales['deal_leads'], 0) : number_format(0, 2, '.', '') }}
                                </td>
                                <td width="20" style="border: 1px solid black;">
                                    {{ isset($sales['invoice_price']) && $sales['invoice_price'] != '' && $sales['invoice_price'] != null ? number_format($sales['invoice_price'], 0) : number_format(0, 2, '.', '') }}
                                </td>
                                <td width="20" style="border: 1px solid black;">
                                    {{ isset($sales['amount_paid']) && $sales['amount_paid'] != '' && $sales['amount_paid'] != null ? number_format($sales['amount_paid'], 0) : number_format(0, 2, '.', '') }}
                                </td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
            @endif
        @endforeach
        @isset($datas['original']['total'])
            <tr>
                <td width="20" style="border: 1px solid black; background-color: #17949D; font-weight: bold;"></td>
                <td width="20" style="border: 1px solid black; background-color: #17949D; font-weight: bold;">
                    {{ isset($datas['original']['total']['total_leads']) && $datas['original']['total']['total_leads'] != '' && $datas['original']['total']['total_leads'] != null ? number_format($datas['original']['total']['total_leads'], 0) : number_format(0, 2, '.', '') }}
                </td>
                <td width="20" style="border: 1px solid black; background-color: #17949D; font-weight: bold;">
                    {{ isset($datas['original']['total']['cold_activity']) && $datas['original']['total']['cold_activity'] != '' && $datas['original']['total']['cold_activity'] != null ? number_format($datas['original']['total']['cold_activity'], 0) : number_format(0, 2, '.', '') }}
                </td>
                <td width="20" style="border: 1px solid black; background-color: #17949D; font-weight: bold;">
                    {{ isset($datas['original']['total']['warm_activity']) && $datas['original']['total']['warm_activity'] != '' && $datas['original']['total']['warm_activity'] != null ? number_format($datas['original']['total']['warm_activity'], 0) : number_format(0, 2, '.', '') }}
                </td>
                <td width="20" style="border: 1px solid black; background-color: #17949D; font-weight: bold;">
                    {{ isset($datas['original']['total']['estimated_value']) && $datas['original']['total']['estimated_value'] != '' && $datas['original']['total']['estimated_value'] != null ? number_format($datas['original']['total']['estimated_value'], 0) : number_format(0, 2, '.', '') }}
                </td>
                <td width="20" style="border: 1px solid black; background-color: #17949D; font-weight: bold;">
                    {{ isset($datas['original']['total']['hot_activity']) && $datas['original']['total']['hot_activity'] != '' && $datas['original']['total']['hot_activity'] != null ? number_format($datas['original']['total']['hot_activity'], 0) : number_format(0, 2, '.', '') }}
                </td>
                <td width="20" style="border: 1px solid black; background-color: #17949D; font-weight: bold;">
                    {{ isset($datas['original']['total']['quotation']) && $datas['original']['total']['quotation'] != '' && $datas['original']['total']['quotation'] != null ? number_format($datas['original']['total']['quotation'], 0) : number_format(0, 2, '.', '') }}
                </td>
                <td width="20" style="border: 1px solid black; background-color: #17949D; font-weight: bold;">
                    {{ isset($datas['original']['total']['deal_leads']) && $datas['original']['total']['deal_leads'] != '' && $datas['original']['total']['deal_leads'] != null ? number_format($datas['original']['total']['deal_leads'], 0) : number_format(0, 2, '.', '') }}
                </td>
                <td width="20" style="border: 1px solid black; background-color: #17949D; font-weight: bold;">
                    {{ isset($datas['original']['total']['invoice_price']) && $datas['original']['total']['invoice_price'] != '' && $datas['original']['total']['invoice_price'] != null ? number_format($datas['original']['total']['invoice_price'], 0) : number_format(0, 2, '.', '') }}
                </td>
                <td width="20" style="border: 1px solid black; background-color: #17949D; font-weight: bold;">
                    {{ isset($datas['original']['total']['amount_paid']) && $datas['original']['total']['amount_paid'] != '' && $datas['original']['total']['amount_paid'] != null ? number_format($datas['original']['total']['amount_paid'], 0) : number_format(0, 2, '.', '') }}
                </td>
            </tr>
        @endisset
    </tbody>
</table>
