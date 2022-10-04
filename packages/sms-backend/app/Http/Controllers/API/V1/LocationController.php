<?php

namespace App\Http\Controllers\API\V1;

use App\Classes\DocGenerator\Enums\Tags;
use App\Http\Resources\V1\Location\LocationResource;
use App\Models\Location;
use App\OpenApi\Customs\Attributes as CustomOpenApi;
use App\OpenApi\Parameters\DefaultHeaderParameters;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class LocationController extends BaseApiController
{
    public function index(Request $request)
    {
        $company_id = $request->company_id ?? 1;
        $itemId = $request->sku;

        try {
            $locations = Http::get(env('ORLANSOFT_API_URL') . 'locations?sku=' . $itemId . '&company_id=' . $company_id);
            return $locations->json();
            // $locationsResult = $locations?->json();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Get location
     *
     * Returns location by id
     *
     * @param Channel $location
     * @return  LocationResource
     */
    #[CustomOpenApi\Operation(id: 'locationShow', tags: [Tags::Location, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\Response(resource: LocationResource::class, statusCode: 200)]
    public function show(Location $location): LocationResource
    {
        return new LocationResource($location);
    }

    /**
     * Show all locations.
     *
     * Show all locations available for this user
     */
    // #[CustomOpenApi\Operation(id: 'locationIndex', tags: [Tags::Location, Tags::V1])]
    // #[CustomOpenApi\Parameters(model: Location::class)]
    // #[CustomOpenApi\Response(resource: LocationResource::class, isCollection: true)]
    // public function index(Request $request)
    // {
    //     $company_id = $request->company_id ?? 1;
    //     $itemId = $request->sku;

    //     $locations = DB::table('locations')->where('company_id', $company_id)->whereNull('deleted_at')->pluck('name', 'orlan_id');

    //     $query = "select ItemID,Description";
    //     foreach ($locations as $orlan_id => $name) {
    //         $query .= ", isnull((select sum(BeginQty+InQty-OutQty) as endQty from INItemBalance where ItemID = a.ItemID and LocationID = '" . $orlan_id . "' and PeriodID = GetPeriod(today())),0) as '" . $orlan_id . "'";
    //     }
    //     $query .= " from INITem as a where itemID='" . $itemId . "' group by ItemID, Description, ItemBrandID order by Description ASC";

    //     $data = DB::connection('orlansoft')->select($query);
    //     $result = [
    //         'ItemID' => $data[0]->ItemID ?? '',
    //         'Description' => $data[0]->Description ?? '',
    //     ];

    //     foreach ($locations as $orlan_id => $name) {
    //         $result['stocks'][] = [
    //             'orlan_id' => $orlan_id,
    //             'name' => $name,
    //             'stock' => (int)$data[0]->{$orlan_id} ?? 0
    //         ];
    //     }
    //     return response()->json($result);
    //     // return CustomQueryBuilder::buildResource(Location::class, LocationResource::class, fn($q) => $q->tenanted());
    // }
}
