<?php

namespace App\Classes\DocGenerator;

use App\Interfaces\OpenApiExport;
use App\Services\HelperService;
use Exception;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Collection;

abstract class BaseResource extends JsonResource implements OpenApiExport
{
    public bool $is_nested = false;

    /**
     * @return array
     * @throws Exception
     */
    public static function getSortableFields(): array
    {
        // default to return all properties as sortable
        return collect(static::data())
            ->filter(function (ResourceData $data) {
                // make id sortable by default
                if($data->getKey() == "id") return true;

                return $data->getSortable();
            })
            ->map(fn(ResourceData $data) => $data->getKey())
            ->diff([])
            ->values()
            ->all();
    }

    public static function getAsObjectSchema(string $id, SchemaConfig $config = null): Schema
    {
        $schema = Schema::object($id)
                        ->properties(...self::getSchemas(false, true, true))
                        ->required(...self::getSchemas(true, false, true));

        return $config ? $config->applyToSchema($schema) : $schema;
    }

    /**
     * Turn resource data into schemas.
     *
     * @param bool $required_only set true to get only required properties
     * @param bool $base_property_only
     * @return array
     */
    public static function getSchemas(bool $required_only = false, bool $base_property_only = false, bool $nested_only = false): array
    {
        $all_data = collect(static::data());

        $schemas = $all_data->filter(fn(ResourceData $data) => empty($data->getParent()));

        if ($nested_only) $schemas = $schemas->filter(fn(ResourceData $data) => $data->getOnNested());
        if ($base_property_only) $schemas = $schemas->filter(fn(ResourceData $data) => $data->getType() !== Schema::TYPE_OBJECT);
        if ($required_only) $schemas = $schemas->filter(fn(ResourceData $data) => !$data->getOptional());

        $schemas = $schemas->map(function (ResourceData $data) use ($all_data) {
            return self::toSchema($data, $all_data);
        });

        return $schemas->toArray();
    }

    protected static function toSchema(ResourceData $data, Collection $all_data): Schema
    {
        if (!empty($data->getSchema())) {
            return $data->getSchema();
        }

        $type = $data->getType();

        if ($type === "object" || $type === "array") {

            // currently we only support direct array/object data.
            // this will not work for nested object/array

            if ($type == "object") {
                $mapKey = "properties";
            }
            if ($type == "array") {
                $mapKey = "items";
            }

            // get the children schemas of this nested element
            $child = $all_data
                ->filter(fn(ResourceData $data) => !empty($data->getParent()))
                ->map(fn(ResourceData $data) => self::primitiveTypeToSchema($data));

            $schema = Schema::$type($data->getKey())->$mapKey(
                ...$child->toArray()
            );

            if (!empty($data->getDescription())) {
                $schema = $schema->description($data->getDescription());
            }

        } else {
            // handling non nested field (e.g., non object or array)
            $schema = self::primitiveTypeToSchema($data);
        }

        return $schema;
    }

    protected static function primitiveTypeToSchema(ResourceData $data): Schema
    {
        if (!empty($data->getSchema())) {
            return $data->getSchema();
        }

        $type = $data->getType();

        $schema = Schema::$type($data->getKey());

        if (!empty($data->getExample())) {
            $schema = $schema->example($data->getExample());
        }

        if (!empty($data->getEnum())) {
            $schema = $schema->enum(...$data->getEnum());
        }

        if (!empty($data->getDescription())) {
            $schema = $schema->description($data->getDescription());
        }

        if (!empty($data->getNullable())) {
            $schema = $schema->nullable($data->getNullable());
        } else {
            $schema = $schema->required();
        }

        return $schema;
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return (collect(static::data())
            ->filter(function (ResourceData $data) {
                if ($this->is_nested && !$data->getOnNested()) return false;

                return empty($data->getParent());
            })
            ->keyBy(fn(ResourceData $data) => $data->getKey())
            ->map(function (ResourceData $data) {

                // value should be a callback
                if (!empty($data->getValue())) {
                    return $data->getValue()($this);
                }

                return HelperService::getProperty($this->resource, $data->getKey(), $data->getCast());
            })->toArray());
    }

    protected static abstract function data(): array;

    public function setNested(): self
    {
        $this->is_nested = true;
        return $this;
    }

    /**
     * Retrieve a relationship if it has been loaded.
     *
     * @param string $relationship
     * @param mixed $value
     * @param mixed $default
     * @return MissingValue|mixed
     */
    public function customWhenLoaded($relationship, $value = null, $default = null)
    {
        return $this->whenLoaded($relationship, $value, $default);
    }
}
