<?php

namespace App\Classes\DocGenerator;

use App\Interfaces\FrontEndRule;
use App\Interfaces\OpenApiExport;
use Exception;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;

/**
 * Class CustomApiRequest
 * @package Consilience\PrintTrail\Core\Api\Http\Requests
 */
abstract class BaseApiRequest extends FormRequest implements OpenApiExport, FrontEndRule
{
    protected ?string $model = null;

    /**
     * Prepare the data for validation.
     * Here we casts enum keys to enum values.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $data = collect($this->getEnumCasts())
            // Only attempt to cast enum what is provided is the request
            ->filter(function ($val, $key) {
                return isset($this->$key);
            })

            // Try to cast from enum key to value.
            // If key is invalid, continue using the key, it will be catch on the validation
            ->map(function ($val, $key) {
                try {
                    return $val::fromKey($this->$key)->value;
                } catch (Exception) {
                    return $this->$key;
                }
            });

        $this->merge($data->all());

    }

    public function getEnumCasts()
    {
        return !is_null($this->model) ? $this->model::getEnumCasts() : [];
    }

    public static function frontEndRuleResponse(): JsonResponse
    {
        $rule = collect(static::data())
            ->filter(fn(RequestData $data) => !$data->getIgnoreFrontEnd())
            ->keyBy(fn(RequestData $data) => $data->getKey())
            ->map(fn(RequestData $data) => $data->getFrontEndForm())
            ->toArray();

        $rules = [
            "data" => collect($rule)
                ->values()
                ->toArray()
        ];

        return response()->json($rules);

    }

    /**
     * Build the schema for the Open API spec
     * @return array
     */
    public static function getSchemas(): array
    {
        $all_data = collect(static::data())
            // filter ignore fields
            ->filter(fn(RequestData $data) => !$data->getIgnoreSchema());

        return $all_data

            //filter out non top level schema (e.g., array field)
            ->filter(fn(RequestData $data) => empty($data->getParent()))
            ->map(function (RequestData $data) use ($all_data) {

                // Schema may already be explicitly provided
                if (!empty($data->getSchema())) return $data->getSchema();

                $type = $data->getType();

                if ($type === Schema::TYPE_OBJECT || $type === Schema::TYPE_ARRAY) {

                    // get the children schemas of this nested element
                    $child = $all_data->filter(fn(RequestData $childData
                    ) => $childData->getParent() === $data->getKey());

                    if ($child->isEmpty()) {
                        throw new Exception("There is no children data defined for the following object/array: ".json_encode($data));
                    }

                    // directly map children as object properties
                    if ($type === Schema::TYPE_OBJECT) {
                        $schema = Schema::$type($data->getKey())
                            ->properties(
                                ...$child->map(fn(RequestData $d) => $d->toSchema())->toArray()
                            );
                    }

                    // Currently only support array of primitive and array of object (non nested)
                    if ($type === Schema::TYPE_ARRAY) {

                        $isAllArrayOfObject = $child->every(fn(RequestData $d) => $d->isArrayOfObjects());
                        $isAllArrayOfPrimitive = $child->every(fn(RequestData $d) => $d->isArrayOfPrimitives());

                        if (!$isAllArrayOfObject && !$isAllArrayOfPrimitive) {
                            throw new Exception("Could not compile open api schema from: ".json_encode($data));
                        }

                        if ($isAllArrayOfPrimitive) {
                            $schema = Schema::$type($data->getKey())
                                ->items(
                                    ...$child->map(fn(RequestData $d) => $d->toSchema())->toArray()
                                );
                        } else {
                            $schema = Schema::$type($data->getKey())->items(
                                Schema::object()->properties(
                                    ...$child->map(fn(RequestData $d) => $d->toSchema())->toArray()
                                )
                            );
                        }

                    }

                } else {
                    // handling non nested field (e.g., non object or array)
                    $schema = $data->toSchema();
                }

                return $schema;
            })->toArray();
    }

    /**
     * All API call except login and register must be authenticated.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth('sanctum')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return collect(static::data())
            ->filter(fn(RequestData $data) => !$data->getIgnoreRule())
            ->keyBy(fn(RequestData $data) => $data->getKey())
            ->map(function (RequestData $data) {
                return is_callable($data->getRule()) ? $data->getRule()($this) : $data->getRule();
            })
            ->toArray();
    }

    abstract protected static function data();
}
