<?php

namespace App\Http\Controllers\API\V1;

use App\Classes\CustomQueryBuilder;
use App\Classes\DocGenerator\Enums\Tags;
use App\Http\Resources\V1\Activity\ActivityResource;
use App\Http\Resources\V1\InteriorDesign\DetailInteriorDesignResource;
use App\Http\Resources\V1\InteriorDesign\InteriorDesignResource;
use App\Http\Resources\V1\Lead\LeadResource;
use App\Models\Activity;
use App\Models\InteriorDesign;
use App\Models\Lead;
use App\OpenApi\Customs\Attributes as CustomOpenApi;
use App\OpenApi\Parameters\DefaultHeaderParameters;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class InteriorDesignController extends BaseApiController
{

    /**
     * Get InteriorDesign
     *
     * Returns InteriorDesign by id
     *
     * @param InteriorDesign $interiorDesign
     * @return  DetailInteriorDesignResource
     */
    #[CustomOpenApi\Operation(id: 'InteriorDesignShow', tags: [Tags::InteriorDesign, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\Response(resource: DetailInteriorDesignResource::class, statusCode: 200)]
    public function show(InteriorDesign $interiorDesign): DetailInteriorDesignResource
    {
        return new DetailInteriorDesignResource($interiorDesign->loadMissing(['religion']));
    }

    /**
     * Show all InteriorDesigns.
     *
     * Show all InteriorDesigns available for this user
     */
    #[CustomOpenApi\Operation(id: 'InteriorDesignIndex', tags: [Tags::InteriorDesign, Tags::V1])]
    #[CustomOpenApi\Parameters(model: InteriorDesign::class)]
    #[CustomOpenApi\Response(resource: InteriorDesignResource::class, isCollection: true)]
    public function index()
    {
        return CustomQueryBuilder::buildResource(InteriorDesign::class, InteriorDesignResource::class, fn ($query) => $query->customTenanted());
    }

    /**
     * Show all interior design leads.
     *
     */
    #[CustomOpenApi\Operation(id: 'reportLeads', tags: [Tags::InteriorDesign, Tags::V1])]
    #[CustomOpenApi\Parameters(model: Lead::class)]
    #[CustomOpenApi\Response(resource: LeadResource::class, isCollection: true)]
    public function reportLeads(int $interior_design_id)
    {
        $interiorDesign = InteriorDesign::find($interior_design_id);
        if (!$interiorDesign) return response()->json(['message' => 'Not Found!'], 404);
        $query = fn ($q) => $q->whereHas('leadActivities', fn ($q) => $q->where('interior_design_id', $interiorDesign->id))->with(['customer', 'user', 'channel', 'leadCategory', 'subLeadCategory']);
        return CustomQueryBuilder::buildResource(Lead::class, LeadResource::class, $query);
    }

    /**
     * Show all activity posted by user where have interior design
     *
     */
    #[CustomOpenApi\Operation(id: 'activityIndex', tags: [Tags::Activity, Tags::V1])]
    #[CustomOpenApi\Parameters(model: Activity::class)]
    #[CustomOpenApi\Response(resource: ActivityResource::class, isCollection: true)]
    public function reportLeadActivities(int $interior_design_id, int $lead_id)
    {
        $interiorDesign = InteriorDesign::find($interior_design_id);
        if (!$interiorDesign) return response()->json(['message' => 'Interior design not Found!'], 404);

        $lead = Lead::where('id', $lead_id)->whereHas('leadActivities', fn ($q) => $q->where('interior_design_id', $interior_design_id))->first();
        if (!$lead) return response()->json(['message' => 'Lead not Found!'], 404);

        $query = fn ($q) => $q->whereHas('lead', fn ($q) => $q->where('id', $lead->id)->where('interior_design_id', $interiorDesign->id))->with(['lead', 'user', 'customer', 'latestComment.user', 'order', 'brands', 'activity_brand_values', 'interiorDesign']);

        return CustomQueryBuilder::buildResource(Activity::class, ActivityResource::class, $query);
    }
}
