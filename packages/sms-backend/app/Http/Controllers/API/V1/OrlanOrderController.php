<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Order;
use App\Models\Payment;
use App\Services\OrlansoftApiService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Throwable;

class OrlanOrderController extends BaseApiController
{
    /**
     * Create sales order to orlansoft
     *
     * 1. cek customer yang ada di moves, apakah sudah ada di db orlan atau belum. jika blm ada, buat customernya dulu
     *
     */
    public function store($order_id)
    {
        $order = Order::find($order_id);
        if ($order->customer->orlan_customer_id == null || $order->customer->orlan_customer_id == '') {
            $createCustomer = app(OrlansoftApiService::class)->createNewCustomer($order->customer);
            if (!$createCustomer) return response()->json(['success' => false, 'message' => 'Failed to create data customer in Orlan!']);
        }

        // prepare data for order detail
        $totalDiscount = 0;
        $orderDetails = [];
        foreach ($order->order_details as $detail) {
            $dataOrderDetaiil = [];
            $grossAmt = $detail->unit_price * $detail->quantity;
            $discAmt = $detail->total_discount;
            $netAmt = $grossAmt - $discAmt;
            $taxable = $netAmt / 1.11;
            $taxAmt = $netAmt - $taxable;

            $dataOrderDetaiil = [
                "itemId" => $detail->product_unit->sku,
                "qt" => $detail->quantity,
                "unitId" => "PCS",
                "description" => $detail->product_unit->name,
                "unitPrice" => $detail->unit_price,
                "grossAmt" => $grossAmt,
                "discAmt" => $discAmt,
                "taxable" => $taxable,
                "taxAmt" => $taxAmt,
                "netAmt" => $netAmt,
                "string2" => $detail->is_ready ? 'RDY' : 'IND',
            ];

            if ($detail->location_id != null && $detail->location_id != '') $dataOrderDetaiil['locationId'] = $detail->location_id;
            array_push($orderDetails, $dataOrderDetaiil);

            $totalDiscount += $discAmt;
        }
        // prepare data for order detail

        /**
         * set trType $trType
         * set projectId $projectId
         */
        $trType = '101SS';
        if ($order->channel->orlan_id != '' && $order->channel->orlan_tr_type != '') {
            $projectId = $order->channel->orlan_id;

            if ($order->is_direct_purchase) {
                $trType = $order->channel?->orlan_tr_type_as ?? '101AS';
            } else {
                $trType = $order->channel?->orlan_tr_type ?? '101SS';
            }
        }

        // set string1 $customerType
        $customerType = 'WalkIn02';
        if ($leadCategory = $order->lead?->leadCategory) {
            $customerType = $leadCategory->orlan_id;
        }

        /**
         * check if order has interior design. set $trType to SOIDN and custBillTo to orlan_id interior design
         * set trType $trType
         * set custBillTo
         * set headerNote
         */
        if ($order->interior_design_id != null || $order->interior_design_id != '') {
            $trType = 'SOIDN';
            $orderData['trType'] = $trType;
            $orderData['custBillTo'] = $order->interiorDesign->orlan_id;

            $customer = $order->customer;
            $headerNote = $customer->first_name . $customer->last_name;
            $headerNote .= $customer->phone != null && $customer->phone != '' ? "\r\n" . $customer->phone : '';
            $headerNote .= $customer->email != null && $customer->email != '' ? "\r\n" . $customer->email : '';

            $address = $customer->defaultCustomerAddress ? $customer->defaultCustomerAddress : $customer->customerAddresses()->first();
            $headerNote .= "\r\n" . ($address?->address_line_1 ?? "don't have address in moves");

            $orderData['headerNote'] = $headerNote;
        } else {
            $orderData['custBillTo'] = $order->customer->id;
        }

        /**
         * if order use discount, set discPct to 1 and string2(header) set with orlan_id discount
         */
        if ($totalDiscount > 0) {
            $orderData['discPct'] = 1;
            if ($order->discount_id != null && $order->discount_id != '' && $order->discount->orlan_id != null && $order->discount->orlan_id != '') {
                $orderData['string2'] = $order->discount->orlan_id;
            } else {
                $orderDiscount = $order->order_discounts()->first();
                $orderData['string2'] = $orderDiscount->discount->orlan_id;
            }
        }

        // check if order has additional discount, set discAmt with order additional_discount
        if ($order->additional_discount > 0) {
            $orderData['discPct'] = 0;
            $orderData['discAmt'] = $order->additional_discount;
        }

        $trNo = $trType . 'MV' . date('ym') . substr($order->id, -4);

        $orderData['trNo'] = $trNo;
        $orderData['trDate'] = date('Y-m-d');
        $orderData['trTime'] = date('Y-m-d');
        $orderData['trType'] = $trType;
        $orderData['projectId'] = $projectId ?? '101';
        $orderData['expectedDlv'] = date('Y-m-d', strtotime($order->expected_shipping_datetime));
        $orderData['expectedArv'] = date('Y-m-d', strtotime($order->expected_shipping_datetime));
        $orderData['addedBy'] = $order->user->orlan_user_id;
        $orderData['salesRepId'] = $order->user->orlan_user_id;
        $orderData['siteId'] = 100;
        $orderData['entityId'] = $order->company_id == 1 ? 100 : 302; // 100 melandas, 302 dioliving
        $orderData['string1'] = $customerType;
        $orderData['trManualRef'] = 'inv ' . $order->invoice_number;

        $dataPost = [
            "SALES ORDER" => [
                $orderData,
                $orderDetails
            ]
        ];
        // return response()->json($dataPost);
        $encryptData = base64_encode(gzencode(json_encode($dataPost)));
        $hashData = sha1($encryptData);
        $apiUrl = env('ORLANSOFT_API_URL') . '/orlansoft-api/data-access/salesorder/createSalesOrder?hashCode=' . $hashData;
        $response = Http::withBasicAuth(env('ORLANSOFT_API_USERNAME'), env('ORLANSOFT_API_PASSWORD'))->withBody($encryptData, 'text/plain')->put($apiUrl);
        // return $response;
        if ($response->body() == 'Success') {
            $order->orlan_tr_no = $trNo;
            $order->is_created_orlan = true;
            $order->save();
            return response()->json(['success' => true, 'message' => 'Sales Order ' . $trNo . ' created successfully in Orlansoft']);
        }
        return response()->json(['success' => false, 'message' => $response->body()]);
    }

    public function getSalesInvoice($trNo = null)
    {
        if ($trNo) {
            $lastRPTransHdr = DB::connection('orlansoft')->table('RPTransHdr')->selectRaw('TOP 1 *')->where('trNo', $trNo)->get();
        } else {
            $lastRPTransHdr = DB::connection('orlansoft')->table('RPTransHdr')->selectRaw('TOP 50 *')->orderByDesc('Sys')->get();
        }
        // dd($lastRPTransHdr);
        return response()->json($lastRPTransHdr);
    }

    public function getSalesInvoiceDetail($OrderNo = null)
    {
        if ($OrderNo) {
            $lastRPTransdtl = DB::connection('orlansoft')->table('RPTransDtl')->where('OrderNo', $OrderNo)->get();
        } else {
            $lastRPTransdtl = DB::connection('orlansoft')->table('RPTransDtl')->selectRaw('TOP 50 *')->orderByDesc('Sys')->get();
        }
        // dd($lastRPTransdtl);
        return response()->json($lastRPTransdtl);
    }

    public function storeSalesInvoice($trNo, $payment_id, $total_payment = 0)
    {
        $totalPayment = $total_payment;

        $apiUrl = env('ORLANSOFT_API_URL') . '/orlansoft-api/data-access/salesorder/getSalesOrder?trNo=' . $trNo;
        $response = Http::withBasicAuth(env('ORLANSOFT_API_USERNAME'), env('ORLANSOFT_API_PASSWORD'))->get($apiUrl);

        if ($response?->json()) {
            if ($data = $response->json()['SALES ORDER']) {

                $payment = Payment::find($payment_id);
                $order = $payment->order;

                $salesOrder = $data[0];
                $projectId = $salesOrder['projectId'];

                // set trType $trType
                $trType = '101AD';
                if ($order->channel->orlan_tr_type_sa != null && $order->channel->orlan_tr_type_sa != '') {
                    $trType = $order->channel?->orlan_tr_type_sa ?? $trType;
                }
                // set trType $trType

                $trNo = $trType . 'MV' . date('ym') . substr($payment->id, -4);
                // $trNo = $trType . 'MV' . date('ym') . $order->id;

                // check last RPTransHdr Sys id
                $lastRPTransHdr = DB::connection('orlansoft')->table('RPTransHdr')->selectRaw('TOP 1 Sys')->orderByDesc('Sys')->get();
                $sys = (int)$lastRPTransHdr[0]->Sys + 1;

                // prepare payment value
                $grossAmt = $totalPayment;
                $discAmt = 0;
                $netAmt = $grossAmt - $discAmt;
                $taxable = $netAmt / 1.11;
                $taxAmt = $netAmt - $taxable;

                // insert into table RPTransHdr
                $message = '';
                $dataHeader = [
                    'SiteID' => 100,
                    'Sys' => $sys,
                    'PeriodID' => $salesOrder['periodId'] ?? null,
                    'EntityID' => $salesOrder['entityId'] ?? null,
                    'XRPeriod' => $salesOrder['xrperiod'] ?? null,
                    'TrNo' => $trNo,
                    'TrManualRef' => 'order_id ' . $order->id . '. payment_id ' . $payment->id,
                    'TrManualRef2' => 'inv ' . $order->invoice_number,
                    'TrDate' => date('Y-m-d', strtotime($payment->created_at)),
                    'TrType' => $trType,
                    'TransCode' => 3,
                    'CustID' => $salesOrder['custBillTo'] ?? null,
                    'TOP_days' => $salesOrder['topDays'] ?? null,
                    'TaxCalc' => $salesOrder['taxCalc'] ?? null,
                    'TaxInvoiceDate' => date('Y-m-d', strtotime($payment->created_at)),
                    'TaxSubmitDate' => date('Y-m-d', strtotime($payment->created_at)),
                    'SalesRepID' => $salesOrder['salesRepId'] ?? null,
                    'CurrencyID' => $salesOrder['currencyId'] ?? null,
                    'Taxable' => $taxable ?? 0,
                    // 'Tax' => $salesOrder['tax'] ?? null,
                    'NetAmt' => $grossAmt ?? 0,
                    'Added_by' => $salesOrder['addedBy'] ?? null,
                    'Changed_by' => $salesOrder['changedBy'] ?? null,
                    'Posted' => 0,
                    'XRPeriodTax' => $salesOrder['xrperiod'] ?? null,
                    'Approved' => 0,
                    'IC_' => 0,
                    'TaxPmt' => 0,
                    'COAID' => 0,
                    'CustTaxTo' => $salesOrder['custTaxTo'] ?? null,
                    'TOP_ID' => $salesOrder['topId'] ?? null,
                    'DiscPct_' => 0,
                    'DiscPct' => 0,
                    'DiscPct2' => 0,
                    'DiscPct3' => 0,
                    'DiscPct4' => 0,
                    'DiscPct5' => 0,
                    'DiscAmt' => 0,
                    'CustSellTo' => $salesOrder['custSellTo'] ?? null,
                    'AutoInvoice' => 0,
                    'Printed' => $salesOrder['printed'] ?? 0,
                    'void_' => $salesOrder['void_'] ?? 0,
                    'ProjectID' => $projectId ?? '101',
                    'InvDiscChanged' => 0,
                    'TaxNoChanged_' => 0,
                    'TaxAdjCreated' => 0,
                    'CollectionDate' => date('Y-m-d', strtotime($payment->created_at)),
                    'PaymentCloseFlag' => 0,
                    'RoundTaxMethod' => 'N',
                    'RejectionStatus' => 0,
                    'TaxLineNo' => 0,
                    'TaxLineNo2' => 0,
                    'TX' => 0,
                ];
                // dump($dataHeader);
                try {
                    $RPTransHdr = DB::connection('orlansoft')->table('RPTransHdr')->insert($dataHeader);
                    if ($RPTransHdr == 1) {
                        $message .= 'Sales Invoice Header #' . $trNo . ' created successfully. ';

                        // insert into table RPTransdtl
                        $dataDetail = [
                            'SiteID' => 100,
                            'Sys' => $sys,
                            'LineNo' => $salesOrder['lineNo'] ?? 1,
                            'TransCode' => 3,
                            'ItemID' => '9990000990078',
                            'PeriodID' => $salesOrder['periodId'] ?? null,
                            'LocationID' => $salesOrder['locationId'] ?? '100WH',
                            'ProjectID' => $projectId ?? '101',
                            'QT' => 1,
                            'UnitID' => 'PCS',
                            'Qty' => 1,
                            'Qty2' => 0,
                            'Description' => 'Sales Advance',
                            'Length_' => 0,
                            'Width_' => 0,
                            'Height' => 0,
                            'Diameter' => 0,
                            'UnitPrice' => $grossAmt ?? 0, // invoice price
                            'DiscPct' => 0,
                            'DiscAmt' => $discAmt ?? 0,
                            'GrossAmt' => $grossAmt ?? 0,
                            'Taxable' => $taxable ?? 0,
                            'TaxAmt' => $taxAmt ?? 0,
                            // 'Rounding' => 0,
                            'NetAmt' => $netAmt ?? 0,
                            'OrderNo' => $salesOrder['trNo'],
                            'InvoiceNo' => 'IDR',
                            'DiscPct2' => 0,
                            'DiscPct3' => 0,
                            'DiscPct4' => 0,
                            'DiscPct5' => 0,
                            'DiscPct_' => 0,
                            'InvDiscAmt' => 0,
                            'LineType' => 'T',
                            'String1' => $payment->payment_type->orlan_id ?? 'CA',
                            'String2' => $payment->payment_type->orlan_id ?? 'CA',
                            'Numeric1' => 0,
                            // 'Bonus_' => 0,
                            'Added_by' => $salesOrder['addedBy'] ?? null,
                            'Changed_by' => $salesOrder['changedBy'] ?? null,
                        ];
                        // dd($dataDetail);
                        try {
                            $RPTransdtl = DB::connection('orlansoft')->table('RPTransDtl')->insert($dataDetail);
                            if ($RPTransdtl == 1) {
                                $payment->orlan_tr_no = $trNo;
                                $payment->save();
                                $message .= 'Sales Invoice Detail created successfully. ';

                                $insertRPTRHdrApproval = $this->insertRPTRHdrApproval($sys);
                                $message .= $insertRPTRHdrApproval . '. ';
                            }
                        } catch (Throwable $e) {
                            DB::connection('orlansoft')->table('RPTransHdr')->where('TrNo', $trNo)->update(['Approved' => 0]);
                            DB::connection('orlansoft')->table('RPTransHdr')->where('TrNo', $trNo)->delete();
                            $message .= 'Failed to create Sales Invoice Detail. ';
                        }
                    }
                } catch (Throwable $e) {
                    $message .= 'Failed to create Sales Invoice Header. ';
                }
            }
        } else {
            $message = 'Failed to create Sales Invoice, Sales Order not found. ';
        }

        return response()->json(['success' => true, 'message' => $message]);
    }

    public function deleteSalesInvoice($TrNo)
    {
        $lastRPTransHdr = DB::connection('orlansoft')->table('RPTransHdr')->where('TrNo', $TrNo)->delete();
        return response()->json($lastRPTransHdr);
    }

    public function deleteSalesInvoiceDetail($orderNo)
    {
        // $lastRPTransDtl = DB::connection('orlansoft')->table('RPTransDtl')->where('OrderNo', $orderNo)->update(['LineType' => 'T']);
        $lastRPTransDtl = DB::connection('orlansoft')->table('RPTransDtl')->where('OrderNo', $orderNo)->delete();
        return response()->json($lastRPTransDtl);
    }

    public function unapproveSalesInvoice($TrNo)
    {
        $lastRPTransHdr = DB::connection('orlansoft')->table('RPTransHdr')->where('TrNo', $TrNo)->update(['Approved' => 0]);
        return response()->json($lastRPTransHdr);
    }

    public function insertRPTRHdrApproval($sys): string
    {
        try {
            $data = [
                [
                    "SiteID" => 100,
                    "Sys" => $sys,
                    "ApproverNo" => 1,
                    "ApproverID" => "FIN01",
                    "Approved" => 0,
                    "GroupNo" => 1,
                ],
                [
                    "SiteID" => 100,
                    "Sys" => $sys,
                    "ApproverNo" => 2,
                    "ApproverID" => "SPVFN",
                    "Approved" => 0,
                    "GroupNo" => 1,
                ],
                [
                    "SiteID" => 100,
                    "Sys" => $sys,
                    "ApproverNo" => 3,
                    "ApproverID" => "ADMCMI",
                    "Approved" => 0,
                    "GroupNo" => 0,
                ],
                [
                    "SiteID" => 100,
                    "Sys" => $sys,
                    "ApproverNo" => 4,
                    "ApproverID" => "ITM",
                    "Approved" => 0,
                    "GroupNo" => 0,
                ],
                [
                    "SiteID" => 100,
                    "Sys" => $sys,
                    "ApproverNo" => 6,
                    "ApproverID" => "AR01",
                    "Approved" => 1,
                    "GroupNo" => 1,
                    "Last_Modified" => now(),
                ],
                [
                    "SiteID" => 100,
                    "Sys" => $sys,
                    "ApproverNo" => 7,
                    "ApproverID" => "YANTI",
                    "Approved" => 0,
                    "GroupNo" => 1,
                ],
                [
                    "SiteID" => 100,
                    "Sys" => $sys,
                    "ApproverNo" => 8,
                    "ApproverID" => "AP01",
                    "Approved" => 0,
                    "GroupNo" => 0,
                ],
                [
                    "SiteID" => 100,
                    "Sys" => $sys,
                    "ApproverNo" => 9,
                    "ApproverID" => "GMDIO",
                    "Approved" => 0,
                    "GroupNo" => 0,
                ],
                [
                    "SiteID" => 100,
                    "Sys" => $sys,
                    "ApproverNo" => 10,
                    "ApproverID" => "MIS",
                    "Approved" => 0,
                    "GroupNo" => 1,
                ],
                [
                    "SiteID" => 100,
                    "Sys" => $sys,
                    "ApproverNo" => 12,
                    "ApproverID" => "FINACT",
                    "Approved" => 0,
                    "GroupNo" => 1,
                ],
                [
                    "SiteID" => 100,
                    "Sys" => $sys,
                    "ApproverNo" => 14,
                    "ApproverID" => "DA01",
                    "Approved" => 0,
                    "GroupNo" => 1,
                ],
                [
                    "SiteID" => 100,
                    "Sys" => $sys,
                    "ApproverNo" => 13,
                    "ApproverID" => "AP02",
                    "Approved" => 0,
                    "GroupNo" => 0,
                ]
            ];
            $RPTRHdrApproval = DB::connection('orlansoft')->table('RPTRHdrApproval')->insert($data);
            if ($RPTRHdrApproval == 1) {
                $message = 'RPTRHdrApproval created successfully. ';
            }
        } catch (Throwable $e) {
            $message = 'Failed to create RPTRHdrApproval. ';
        }
        return $message;
    }
}
