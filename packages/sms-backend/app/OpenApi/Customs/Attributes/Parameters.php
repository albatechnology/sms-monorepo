<?php

namespace App\OpenApi\Customs\Attributes;

use App\OpenApi\Parameters\Custom\CustomParameter;
use Attribute;
use Illuminate\Database\Eloquent\Model;
use Vyuldashev\LaravelOpenApi\Attributes\Parameters as ParametersAttribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Parameters extends ParametersAttribute
{
    public string $model;

    public function __construct(string $model)
    {
//        if (!is_a($model, Model::class, true)) {
//            throw new \Exception("CustomParameters attribute must be eloquent model class");
//        }

        $this->model = $model;
        parent::__construct(CustomParameter::class);
    }
}
