<?php

namespace App\OpenApi\RequestBodies\Custom;

use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Factories\RequestBodyFactory;

class ImageRequestBody extends RequestBodyFactory
{
    public function build(): RequestBody
    {

        $data = Schema::object('image')
            ->title('ImageRequestBody')
            ->properties(
                Schema::string('image')->format(Schema::FORMAT_BINARY),
            );

        return RequestBody::create('UploadImage')
            ->description('Upload Image')
            ->content(
                MediaType::json()->schema($data)
            );
    }
}
