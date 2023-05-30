<?php

namespace App\Http\Controllers\API\V1;

use App\Classes\CustomQueryBuilder;
use App\Classes\DocGenerator\Enums\Tags;
use App\Exceptions\UnauthorisedTenantAccessException;
use App\Http\Resources\V1\Channel\ChannelResource;
use App\Models\Channel;
use App\OpenApi\Customs\Attributes as CustomOpenApi;
use App\OpenApi\Parameters\DefaultHeaderParameters;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class ChannelController extends BaseApiController
{

    /**
     * Get channel
     *
     * Returns channel by id
     *
     * @param Channel $channel
     * @return  ChannelResource
     */
    #[CustomOpenApi\Operation(id: 'channelShow', tags: [Tags::Channel, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\Response(resource: ChannelResource::class, statusCode: 200)]
    public function show(Channel $channel): ChannelResource
    {
        return new ChannelResource($channel);
    }

    /**
     * Get default channel
     *
     * Returns the default channel of authenticated user
     *
     * @return ChannelResource
     * @throws UnauthorisedTenantAccessException
     */
    #[CustomOpenApi\Operation(id: 'channelDefault', tags: [Tags::Channel, Tags::V1])]
    #[OpenApi\Parameters(factory: DefaultHeaderParameters::class)]
    #[CustomOpenApi\Response(resource: ChannelResource::class, statusCode: 200)]
    #[CustomOpenApi\ErrorResponse(exception: UnauthorisedTenantAccessException::class)]
    public function default(): ChannelResource
    {
        if (!$channel = auth()->user()->channel) throw new UnauthorisedTenantAccessException();
        return new ChannelResource($channel);
    }

    /**
     * Show all channels.
     *
     * Show all channels available for this user
     */
    #[CustomOpenApi\Operation(id: 'channelIndex', tags: [Tags::Channel, Tags::V1])]
    #[CustomOpenApi\Parameters(model: Channel::class)]
    #[CustomOpenApi\Response(resource: ChannelResource::class, isCollection: true)]
    public function index()
    {
        return CustomQueryBuilder::buildResource(Channel::class, ChannelResource::class);
    }
}
