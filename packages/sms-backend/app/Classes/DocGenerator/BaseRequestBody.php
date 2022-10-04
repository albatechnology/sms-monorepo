<?php

namespace App\Classes\DocGenerator;

use App\Interfaces\OpenApiExport;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Factories\RequestBodyFactory;

class BaseRequestBody extends RequestBodyFactory
{
    protected ?OpenApiExport $requestClass = null;
    protected ?string $description = null;

    public function build(): RequestBody
    {
        $data = Schema::object('data');

        if ($this->requestClass) {
            $data = $data->properties(
                ...$this->requestClass::getSchemas()
            );
        }

        $body = RequestBody::create(get_called_class())
            ->content(
                MediaType::json()->schema($data)
            );

        if ($this->description) {
            $body = $body->description($this->description);
        }

        return $body;
    }
}
