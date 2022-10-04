<?php

namespace App\Http\Controllers\API\V1;

use App\Classes\CustomQueryBuilder;
use App\Classes\DocGenerator\Enums\Tags;
use App\Http\Resources\V1\SmsChannel\SmsChannelResource;
use App\Models\SmsChannel;
use App\OpenApi\Customs\Attributes as CustomOpenApi;
use App\OpenApi\Parameters\DefaultHeaderParameters;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class SmsChannelController extends BaseApiController
{

    /**
     * Get SMS channel
     *
     * Returns SMS channel by id
     *
     * @param Channel $smsChannel
     * @return  SmsChannelResource
     */
    #[CustomOpenApi\Operation(id: 'smsChannelShow', tags: [Tags::SmsChannel, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\Response(resource: SmsChannelResource::class, statusCode: 200)]
    public function show(SmsChannel $smsChannel): SmsChannelResource
    {
        return new SmsChannelResource($smsChannel);
    }

    /**
     * Show all SMS channels.
     *
     * Show all SMS channels available for this user
     */
    #[CustomOpenApi\Operation(id: 'smsChannelIndex', tags: [Tags::SmsChannel, Tags::V1])]
    #[CustomOpenApi\Parameters(model: SmsChannel::class)]
    #[CustomOpenApi\Response(resource: SmsChannelResource::class, isCollection: true)]
    public function index()
    {
        return CustomQueryBuilder::buildResource(SmsChannel::class, SmsChannelResource::class, fn ($query) => $query->whereHas('user'));
    }
}
