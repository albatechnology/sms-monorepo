<?php

namespace App\Http\Controllers\API\V1;

use App\Classes\CustomQueryBuilder;
use App\Classes\DocGenerator\Enums\Tags;
use App\Http\Resources\V1\Target\TargetResource;
use App\Models\Target;
use App\OpenApi\Customs\Attributes as CustomOpenApi;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class TargetController extends BaseApiController
{
    const load_relation = ['report', 'target_lines', 'model'];

    /**
     * Show all target.
     *
     * Show all target
     *
     */
    #[CustomOpenApi\Operation(id: 'TargetIndex', tags: [Tags::Target, Tags::V1])]
    #[CustomOpenApi\Parameters(model: Target::class)]
    #[CustomOpenApi\Response(resource: TargetResource::class, isCollection: true)]
    public function index()
    {

        return CustomQueryBuilder::buildResourceCustom(
            Target::class,
            TargetResource::class,
            function ($query) {

                $query = $query->with(self::load_relation)
                    ->tenanted()->whereNotIn('type', ['2']);

                $filter = request()->get('filter');

                // if no time filter set, show all target that includes now
                if (!isset($filter['start_after']) && !isset($filter['end_before'])) {
                    $query = $query->targetDatetime(now());
                }

                return $query->get()
                    ->sortBy(function (Target $target) {
                        return [$target->report->time_diff, $target->type->getPriority()];
                    });
            },
        );
    }
}
