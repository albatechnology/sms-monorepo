<?php

namespace App\OpenApi\Responses\Custom;

use App\OpenApi\Customs\Attributes\Response as ResponseAttribute;
use App\OpenApi\Schemas\Metadata\LinksSchema;
use App\OpenApi\Schemas\Metadata\MetaSchema;
use App\OpenApi\Schemas\Metadata\QuerySchema;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class SuccessResourceResponse extends ResponseFactory
{
    public ?Response $response = null;

    public function build(): Response
    {
        return $this->response;
    }

    public function customBuild(ResponseAttribute $attribute): Response
    {
        $resource = $attribute->resource;

        $property = Schema::object('data')
                ->properties(...$resource::getSchemas())
                ->required(...$resource::getSchemas(true));


        // for index, map into array of data and append doc for pagination and queries
        if($attribute->isCollection){
            $properties = [
                Schema::array('data')->items($property),
                self::paginationLinksSchema(),
                self::paginationMetaSchema(),
                self::metadataQuerySchema(),
            ];

            $response = Response::create('ResourceCollectionRequest')
                ->description($description ?? 'Successful response')
                ->content(
                    MediaType::json()->schema(
                        Schema::object()->properties(...$properties)
                    )
                )->statusCode($attribute->statusCode);
        }else{
            $response = Response::create('ResourceRequest')
                ->description('Successful response')
                ->content(
                    MediaType::json('data')->schema(
                        Schema::object('data')
                            ->properties($property)
                            ->required($property)
                    )
                )->statusCode($attribute->statusCode);
        }

        $this->response = $response;

        return $response;
    }

    public static function paginationLinksSchema()
    {
        return Schema::ref(LinksSchema::ref()->ref, 'links');
    }

    public static function paginationMetaSchema()
    {
        return Schema::ref(MetaSchema::ref()->ref, 'meta');
    }

    public static function metadataQuerySchema()
    {
        return Schema::ref(QuerySchema::ref()->ref, 'query');
    }
}
