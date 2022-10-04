<?php

namespace App\OpenApi\Customs\Attributes;


use Attribute;
use Vyuldashev\LaravelOpenApi\Attributes\Operation as BaseOperation;

#[Attribute(Attribute::TARGET_METHOD)]
class Operation extends BaseOperation
{
    public function __construct(string $id = null, array $tags = [], string $method = null)
    {
        $hasDummyTag = in_array("dummy", $tags);

//        $tags = collect($tags)
//            ->filter(function (string $tag) {
//                // for now, lets just keep version & "dummy" tag
//                return in_array($tag, ["V1", "dummy"]);
//            })
//            ->filter(function (string $tag) use ($hasDummyTag) {
//                // if it is tagged as dummy, remove the version tag
//                if (!$hasDummyTag) return true;
//                return $tag != "V1";
//            })->values()->toArray();

        parent::__construct($id, $tags, $method);
    }
}
