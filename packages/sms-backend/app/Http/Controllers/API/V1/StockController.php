<?php

namespace App\Http\Controllers\API\V1;

use App\Classes\CustomQueryBuilder;
use App\Classes\DocGenerator\Enums\Tags;
use App\Exceptions\UnauthorisedTenantAccessException;
use App\Http\Resources\V1\Stock\StockExtendedResource;
use App\Http\Resources\V1\Stock\StockResource;
use App\Models\Channel;
use App\Models\Stock;
use App\OpenApi\Customs\Attributes as CustomOpenApi;
use App\OpenApi\Parameters\DefaultHeaderParameters;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class StockController extends BaseApiController
{
    const load_relation = [];
    private $perPage = 15;

    /**
     * Get stock
     *
     * Returns stock by id
     *
     * @param Stock $stock
     * @return  StockResource
     */
    #[CustomOpenApi\Operation(id: 'StockShow', tags: [Tags::Stock, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\Response(resource: StockResource::class, statusCode: 200)]
    #[CustomOpenApi\ErrorResponse(exception: UnauthorisedTenantAccessException::class)]
    public function show(Stock $stock)
    {
        return new StockResource(
            $stock->loadMissing(self::load_relation)
                ->checkTenantAccess()
        );
    }

    /**
     * Show all stock.
     *
     * Show all stock
     *
     */
    #[CustomOpenApi\Operation(id: 'StockIndex', tags: [Tags::Stock, Tags::V1])]
    #[CustomOpenApi\Parameters(model: Stock::class)]
    #[CustomOpenApi\Response(resource: StockResource::class, isCollection: true)]
    public function index()
    {
        return CustomQueryBuilder::buildResource(
            Stock::class,
            StockResource::class,
            fn ($query) => $query->with(self::load_relation)->tenanted()
        );
    }

    /**
     * Show all stock (with extended data).
     *
     * Show all stock with additional loaded properties.
     *
     */
    #[CustomOpenApi\Operation(id: 'StockIndexExtended', tags: [Tags::Stock, Tags::V1])]
    #[CustomOpenApi\Parameters(model: Stock::class)]
    #[CustomOpenApi\Response(resource: StockExtendedResource::class, isCollection: true)]
    public function indexExtended()
    {
        $loadRelation = ['product.product.brand', 'product.colour', 'product.covering'];

        return CustomQueryBuilder::buildResource(
            Stock::class,
            StockExtendedResource::class,
            fn ($query) => $query->with($loadRelation)->tenanted()
        );
    }

    public function indexNew(Request $request)
    {
        $name = $request->has('name') ? $request->name : '';
        $channels = Channel::withSum('channelStocks', 'stock')->where('name', 'like', '%' . $name . '%')->customTenanted()->simplePaginate($this->perPage);

        return response()->json($channels);
    }

    public function extendedNew(Request $request, $channelId)
    {
        $name = $request->has('name') ? $request->name : '';
        $stocks = Stock::query()
            ->with(['channel' => function ($query) {
                return $query->select(['id', 'name']);
            }, 'product' => function ($query) {
                return $query->select(['id', 'name', 'product_id']);
            }, 'product.product' => function ($query) {
                return $query->select(['id', 'name', 'product_brand_id']);
            }, 'product.product.brand' => function ($query) {
                return $query->select(['id', 'name']);
            }])->where('channel_id', $channelId)
            ->whereHas('product', function ($query) use ($name) {
                $query->where('name', 'like', '%' . $name . '%');
            })->orWhereHas('product.product.brand', function ($query) use ($name) {
                $query->where('name', 'like', '%' . $name . '%');
            })->select(sprintf('%s.*', (new Stock)->table))->simplePaginate($this->perPage);

        $data = [];
        $data['current_page'] = $stocks->currentPage();
        $data['first_page_url'] = $stocks->url(1);
        $data['from'] = $stocks->firstItem();
        $data['next_page_url'] = $stocks->nextPageUrl();
        $data['path'] = $stocks->path();
        $data['per_page'] = $stocks->perPage();
        $data['prev_page_url'] = $stocks->previousPageUrl();
        $data['to'] = $stocks->lastItem();
        $data['data'] = [];

        foreach ($stocks as $stock) {
            $stock['outstanding_order'] = (int) \App\Services\StockService::outstandingOrder($stock->company_id, $stock->channel_id, $stock->product_id);;
            $stock['outstanding_shipment'] = (int) \App\Services\StockService::outstandingShipment($stock->company_id, $stock->channel_id, $stock->product_id);
            $stock['real_stock'] = ($stock->stock + $stock['outstanding_shipment']) - $stock->indent;
            $data['data'][] = $stock;
        }

        return response()->json($data);
    }

    public function productChannel(Request $request, $productId)
    {
        $data = Stock::with(['channel' => function ($query) {
            $query->select('id', 'name');
        }, 'product' => function ($query) {
            $query->select('id', 'name');
        }])->where('product_id', $productId)->select('id', 'stock', 'channel_id', 'product_id')->get();

        if ($request->has('channel_id')) $data = $data->where('channel_id', $request->channel_id);

        return response()->json($data);
    }

    public function extendedDetail($companyId, $channelId, $productId)
    {
        $details = Stock::outstandingShipmentDetail($companyId, $channelId, $productId);
        $data = [];
        foreach ($details as $d) {
            array_push($data, [
                'created_at' => date('d-m-Y', strtotime($d->created_at)),
                'invoice_number' => $d->invoice_number,
                'sales' => $d->user->name,
                'deal_at' => $d->deal_at ? date('d-m-Y', strtotime($d->deal_at)) : '-',
                'expected_shipping_datetime' => $d->expected_shipping_datetime ? date('d-m-Y', strtotime($d->expected_shipping_datetime)) : '-',
            ]);
        }
        return response()->json($data);
    }
}
