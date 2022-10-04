<?php

namespace App\OpenApi\SecuritySchemes;

use GoldSpecDigital\ObjectOrientedOAS\Objects\SecurityScheme;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\SecuritySchemeFactory;

class BearerScheme extends SecuritySchemeFactory implements Reusable
{
    /**
     * @return SecurityScheme
     */
    public function build(): SecurityScheme
    {
        return SecurityScheme::create('bearerAuth')
            ->type(SecurityScheme::TYPE_HTTP)
            ->scheme('bearer')
            ->bearerFormat('JWT');
    }
}
