<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PDF</title>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&display=swap");

        @media print {
            footer {
                break-before: page;
            }

            tr {
                page-break-inside: avoid !important;
                -webkit-column-break-inside: avoid;
                break-inside: avoid;
                -webkit-region-break-inside: avoid;
            }
        }

        tr {
            page-break-inside: avoid !important;
            page-break-after: auto !important;
        }

        html {
            background-color: white;
        }

        body {
            font-size: 12px;
            min-height: 100%;
            font-family: Open Sans, Arial, Helvetica, sans-serif;
            margin: 2, 5%;
        }

        footer {
            font-size: 12px;
            font-family: Open Sans, Arial, Helvetica, sans-serif;
        }

        h1 {
            text-align: center;
        }

        hr {
            height: 2px;
            border-width: 0;
            color: #313132;
            background-color: #313132;
            margin-top: 24px;
        }

        .italic {
            font-style: italic;
        }

        .topContainer {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            margin-top: 12px;
            margin-bottom: 24px;
        }

        .logo {
            width: 20%;
            height: auto
        }

        .quotationTitle {
            font-size: 26px;
            font-weight: bold;
            text-align: center;
        }

        .metadata {
            display: flex;
            flex-direction: row;
            margin-top: 4px;
        }

        .metadata-title {
            width: 120px;
            font-weight: bold;
            flex-shrink: 0;
        }

        .table {
            width: 100%;
            margin-top: 32px;
            border-spacing: 0;
        }

        th {
            padding: 8px;
            border-top: 1px solid #313132;
            border-bottom: 1px solid #313132;
        }

        td {
            padding: 8px;
            text-align: center;
            vertical-align: middle;
        }

        table tr:first-child th:first-child {
            border-left: 1px solid #313132;
        }

        table tr:first-child th:last-child {
            border-right: 1px solid #313132;
        }

        .info-row-left {
            border-left: 1px solid #313132;
            text-align: right;
        }

        .info-row-right {
            border-right: 1px solid #313132;
            text-align: right;
        }

        .items-table tr td:first-child {
            border-left: 1px solid #313132;
        }

        .items-table tr td:last-child {
            border-right: 1px solid #313132;
        }

        .items-table tr td:nth-child(n + 4) {
            text-align: right;
        }

        .items-table tr:last-child td {
            border-bottom: 1px solid #313132;
        }

        .alt-table {
            width: 100%;
            margin-top: 32px;
            border-spacing: 0;
        }

        .alt-table td {
            background-color: #ef633f;
            color: white;
            padding: 8px;
            border-top: 1px solid #313132;
            border-bottom: 1px solid #313132;
        }

        .alt-table tr:first-child td:first-child {
            border-left: 1px solid #313132;
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
        }

        .alt-table tr:first-child td:last-child {
            border-right: 1px solid #313132;
            border-top-right-radius: 8px;
            border-bottom-right-radius: 8px;
        }

        .footer-section {
            display: flex;
            flex: 1;
            flex-direction: row;
            justify-content: space-between;
            margin-top: 50px;
        }
    </style>
</head>

<body>
    <table style="width: 100%; border: 0; margin-top: 12px; margin-bottom: 24px;">
        <tr>
            <td style="text-align: left; padding: 0;">
                <img src="{{ public_path('images/logo/logo-trek.webp') }}" class="logo" alt="Logo" />
            </td>
            <td style="text-align: right; padding: 0; width: 30%; vertical-align: top;">
                <table style="width: 100%; border: 0;">
                    <tr>
                        <td style="text-align: center; border: 0; padding: 0;">
                            <div class="" style="font-weight: bold; font-size: 26px;">
                                @if ($params['type'] == 'quotation')
                                    {{ ucwords('Quotation') }}
                                @elseif($params['type'] == 'invoice')
                                    {{ ucwords('Invoice') }}
                                @else
                                    {{ ucwords('Sales Confirmation') }}
                                @endif
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: top; border: 0; padding: 0;">
                            <table>
                                <tr>
                                    <td style="text-align: left; border: 0; padding: 0; font-weight: bold;"
                                        width="80">
                                        Date</td>
                                    <td style="text-align: left; border: 0; padding: 0;">:
                                        {{ \Carbon\Carbon::parse($params['order']->created_at)->toDayDateTimeString() }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: left; border: 0; padding: 0; font-weight: bold;"
                                        width="80">
                                        No.</td>
                                    <td style="text-align: left; border: 0; padding: 0;">:
                                        {{ $params['order']->invoice_number }}</td>
                                </tr>
                                <tr>
                                    <td style="text-align: left; border: 0; padding: 0; font-weight: bold;"
                                        width="80">
                                        Associate</td>
                                    <td style="text-align: left; border: 0; padding: 0;">: {{ $params['user']->name }}
                                    </td>
                                </tr>
                                @if ($params['type'] == 'quotation')
                                    <tr>
                                        <td style="text-align: left; border: 0; padding: 0; font-weight: bold;"
                                            width="80">Valid Until</td>
                                        <td style="text-align: left; border: 0; padding: 0;">:
                                            {{ \Carbon\Carbon::parse($params['order']->quotation_valid_until_datetime)->format('D, M j, Y') }}
                                        </td>
                                    </tr>
                                @endif
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div>
        <div class="metadata-title">Shipping To:</div>
        <div>
            {{ \App\Enums\PersonTitle::fromValue($params['order']->customer->title)->description . ' ' . $params['order']->customer->getFullNameAttribute() }}
        </div>
        @if ($params['order']->shipping_address?->address_line_1)
            <div>{{ $params['order']->shipping_address->address_line_1 }}</div>
        @endif
        @if ($params['order']->shipping_address?->address_line_2)
            <div>{{ $params['order']->shipping_address->address_line_2 }}</div>
        @endif
        @if ($params['order']->shipping_address?->address_line_3)
            <div>{{ $params['order']->shipping_address->address_line_3 }}</div>
        @endif

        <div>{{ $params['order']->shipping_address?->city }}</div>
        <div>{{ $params['order']->shipping_address?->province }}</div>
        <div>{{ $params['order']->shipping_address?->country }}</div>
        <div>{{ $params['order']->shipping_address?->postcode }}</div>
        <div>{{ $params['order']->shipping_address?->phone }}</div>
    </div>

    <table class="table items-table">
        <tr>
            <th>IMAGE</th>
            <th>DESCRIPTION</th>
            <th>STATUS</th>
            <th>QTY</th>
            <th>UNIT PRICE</th>
            <th>DISCOUNT</th>
            <th>AMOUNT(IDR)</th>
        </tr>
        @foreach ($params['order']->order_details as $item)
            <tr>
                <td>
                    @php
                        if (isset($item->photo) && count($item->photo) > 0) {
                            $photo = $item->photo[0]['url'];
                        } elseif (isset($item->records['images']) && count($item->records['images']) > 0) {
                            $photo = $item->records['images'][0]['url'];
                        } else {
                            $photo = public_path('images/no-image.jpg');
                        }
                    @endphp
                    <img src="{{ $photo }}" alt="productImage" style="width: 126px; height: auto; display: block; margin-left: auto; margin-right: auto;" />
                </td>
                <td style="text-align: left;">{{ $item->product?->name }}</td>
                <td>{{ $item->is_ready == 1 ? 'ready' : 'indent' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ rupiah($item->unit_price) }}</td>
                <td>
                    @php
                        $discountPercentage = ($item->total_discount / ($item->unit_price * $item->quantity)) * 100;
                        echo $discountPercentage > 100 ? '100%' : $discountPercentage . '%';
                    @endphp
                </td>
                <td>{{ rupiah($item->total_price) }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="6" class="info-row-left" style="border-top: 1px solid #313132;">Packing Fee</td>
            <td class="info-row-right" style="border-top: 1px solid #313132;">
                {{ rupiah($params['order']->packing_fee) }}
            </td>
        </tr>
        <tr>
            <td colspan="6" class="info-row-left">Shipping Fee</td>
            <td class="info-row-right">
                {{ rupiah($params['order']->shipping_fee) }}
            </td>
        </tr>
        <tr>
            <td colspan="6" class="info-row-left">Sub Total</td>
            <td class="info-row-right">
                {{ rupiah($params['order']->original_price + $params['order']->packing_fee + $params['order']->shipping_fee) }}
            </td>
        </tr>
        <tr>
            <td colspan="6" class="info-row-left" style="border-top: 1px solid #313132;">Additional Discount</td>
            <td class="info-row-right" style="border-top: 1px solid #313132;">
                {{ rupiah($params['order']->additional_discount) }}
            </td>
        </tr>
        <tr>
            <td colspan="6" class="info-row-left">Discount</td>
            <td class="info-row-right">
                {{ rupiah($params['order']->total_discount) }}
            </td>
        </tr>
        <tr style="margin-bottom: 10px;">
            <td colspan="6" class="info-row-left" style="border-bottom: 1px solid #313132;">Total</td>
            <td class="info-row-right" style="border-bottom: 1px solid #313132;">
                {{ rupiah($params['order']->total_price) }}
            </td>
        </tr>
        @foreach ($params['order']->orderPayments()->whereNot('status', \App\Enums\PaymentStatus::REJECTED()) as $orderPayment)
            <tr style="margin-bottom: 10px;">
                <td colspan="6" class="info-row-left" style="margin-top: 15px">
                    {{ ucwords($orderPayment->payment_type->name) }}
                </td>
                <td class="info-row-right">
                    {{ rupiah($orderPayment->amount) }}
                </td>
            </tr>
        @endforeach
    </table>
    @if (!is_null($params['order']->note) && $params['order']->note != '')
        <table style="width: 100%; border: 0; margin-top: 12px; margin-bottom: 24px;">
            <tr>
                <td style="text-align: left; padding: 0; width: 50%; vertical-align: top;">
                    <div style="font-weight: bold">Note:</div>
                    <p>{{ $params['order']->note }}</p>
                </td>
            </tr>
        </table>
    @endif
    @if ($params['type'] == 'quotation')
        <table class="table items-table">
            <tr>
                <th>Jumlah DP 50%</th>
                <th>{{ rupiah($params['order']->total_price / 2) }}</th>
            </tr>
        </table>
    @endif
    @if ($params['order']->orderPayments && count($params['order']->orderPayments) > 0)
        <table class="table items-table">
            <tr>
                <th>PAYMENT</th>
                <th>REFERENCE</th>
                <th>AMOUNT(IDR)</th>
            </tr>
            @foreach ($params['order']->orderPayments as $payment)
                <tr>
                    <td>
                        {{ $payment->payment_type->payment_category->name . ' - ' . $payment->payment_type->name }}
                    </td>
                    <td>{{ $payment->reference ?? '-' }}</td>
                    <td>{{ rupiah($payment->amount) }}</td>
                </tr>
            @endforeach
            @if (
                $params['order']->payment_status->in([
                    \App\Enums\OrderPaymentStatus::NONE,
                    \App\Enums\OrderPaymentStatus::PARTIAL,
                    \App\Enums\OrderPaymentStatus::DOWN_PAYMENT,
                ]))
                <tr>
                    <td>Balance Due</td>
                    <td></td>
                    <td>{{ rupiah($params['order']->total_price - $params['order']->amount_paid) }}</td>
                </tr>
            @endif
        </table>
    @endif
    <footer>
        <div class="footer-section">
            <table style="width: 100%; border: 0; margin-top: 12px; margin-bottom: 24px;">
                <tr>
                    @if ($params['type'] == 'quotation')
                        <td style="text-align: left; padding: 0; width: 50%; vertical-align: top;">
                            <div style="font-weight: bold">Terms & Conditions:</div>
                            <ul>
                                <li>
                                    All prices stated in this quotation are including VAT 11% and
                                    other applicable taxes
                                </li>
                                <li>
                                    Prices stated in this quotation are excluding the Delivering
                                    Charges
                                </li>
                            </ul>
                        </td>
                    @endif
                    <td style="text-align: left; padding: 0; width: 50%; vertical-align: top;">
                        <div style="font-weight: bold">Terms of Payment:</div>
                        <ul>
                            <li>Ready Stock Items: Full payment before delivery</li>
                            <li>
                                Indent Items: 50% Down Payment is required upon confirmation and
                                the remaining payment will be required prior to delivery
                            </li>
                            <li>
                                No order cancellation after the quotation is confirmed and payment
                                is received
                            </li>
                            {{-- <li>
                                Please make your payment only to the following account:
                                <ul>
                                    <li>Bank: {{ $params['order']->company->companyAccount->bank_name ?? '-' }}</li>
                                    <li>Account No:
                                        {{ $params['order']->company->companyAccount->account_number ?? '-' }}</li>
                                    <li>Account Name:
                                        {{ $params['order']->company->companyAccount->account_name ?? '-' }}</li>
                                </ul>
                            </li> --}}
                            <li>
                                If you need further assistance, please do not hesitate to contact
                                your sales representative
                            </li>
                        </ul>
                    </td>
                </tr>
            </table>
        </div>

        <div
            style="
              color: white;
              background-color: #1d4076;
              font-size: 18px;
              font-weight: bold;
              text-align: center;
              padding-top: 12px;
              padding-bottom: 12px;
              margin-top: 12px;
            ">
            THANK YOU FOR YOUR ORDER AT <br /> PT. Alba Digital Technology
            {{-- {{ strtoupper($params['order']->company->companyAccount->name ?? 'our company') }} --}}
        </div>
    </footer>
</body>

</html>
