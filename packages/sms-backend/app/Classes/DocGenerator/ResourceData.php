<?php


namespace App\Classes\DocGenerator;

use App\Classes\DocGenerator\Interfaces\ApiDataExample;
use App\Enums\BaseEnum;
use App\Http\Resources\V1\Channel\ChannelResource;
use App\Http\Resources\V1\Generic\MediaResource;
use Exception;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Illuminate\Http\Resources\MissingValue;
use InvalidArgumentException;

class ResourceData
{
    protected ?array $enum = null;
    protected $value = null;
    protected ?Schema $schema = null;
    protected ?string $parent = null;
    protected bool $nullable = false;
    protected bool $optional = false;
    protected bool $sortable = false;
    protected bool $on_nested = true;
    protected string $description = "";
    protected ?string $cast = null;

    // region core service

    public function __construct(protected string $key, protected string $type, protected $example = null)
    {
    }

    public static function makeEnum(string $key, string $enum, bool $nullable = false): self
    {
        if (!is_a($enum, BaseEnum::class, true)) {
            throw new InvalidArgumentException("$enum must be an Enum class.");
        }

        $query = function ($q) use ($key, $enum) {
            $value = $q->$key ?? $q[$key];

            if ($value instanceof BaseEnum) return $value->key;
            if (is_int($value) || is_numeric($value)) return $enum::fromValue((int)$value)->key;

            return $value;
        };

        return self::make($key, Schema::TYPE_STRING, $enum::getDefaultInstance()->key, $enum::getKeys(), $query)->nullable($nullable);
    }

    public function nullable(bool $nullable = true): self
    {
        $this->nullable = $nullable;
        return $this;
    }

    /**
     * @param string $key name of the field
     * @param string $type [string, integer, object, array, binary]
     * @param null $example example value. used for open api
     * @param array|null $enum array of accepted values
     * @param callable|null $value When the resource value is not the same as the key, a callback function can be passed to locate the value on the resource class
     * @param Schema|null $schema explicitly define the open api schema for this field
     * @param string|null $parent the parent key of this field. Parent should be an array or object
     * @param bool $nullable whether this field is nullable
     * @param string|null $cast
     * @return ResourceData
     * @throws Exception
     */
    public static function make(
        string $key,
        string $type,
        $example = null,
        ?array $enum = null,
        callable $value = null,
        Schema $schema = null,
        ?string $parent = null,
        bool $nullable = false,
        ?string $cast = null
    ): self
    {

        $allowed_types = collect(["string", "integer", "object", "array", "binary", "boolean"]);
        if (!$allowed_types->contains($type)) {
            throw new Exception(sprintf("Unaccepted resource field type %s. Allowed types: %s", $type,
                $allowed_types->implode(', ')));
        }

        $object = new self($key, $type, $example);
        if ($enum) $object = $object->enum($enum);
        if ($value) $object = $object->value($value);
        if ($schema) $object = $object->schema($schema);
        if ($parent) $object = $object->parent($parent);
        if ($nullable) $object = $object->nullable($nullable);
        if ($cast) $object = $object->cast($cast);

        return $object;
    }

    public function enum(array $enum): self
    {
        $this->enum = $enum;
        return $this;
    }

    /**
     * @param mixed $value
     */
    public function value($value): self
    {
        $this->value = $value;
        return $this;
    }

    public function schema(Schema $schema): self
    {
        $this->schema = $schema;
        return $this;
    }

    public function parent(?string $parent): self
    {
        $this->parent = $parent;
        return $this;
    }

    public function cast(string $cast = 'string'): self
    {
        $this->cast = $cast;
        return $this;
    }

    public static function timestamps(): array
    {
        return [
            self::make('updated_at', Schema::TYPE_STRING, ApiDataExample::TIMESTAMP, nullable: true),
            self::make('created_at', Schema::TYPE_STRING, ApiDataExample::TIMESTAMP, nullable: true)
        ];
    }

    /**
     * Return an array of property instead of model. For example, instead of an
     * array of tags model, we just want to return an array of tag names.
     *
     * @param string $key
     * @param string $pluck_key the property of the model to be plucked
     * @param Schema $schema
     * @param string|null $relationship_key
     * @return ResourceData
     * @throws Exception
     */
    public static function makeRelationshipPluck(string $key, string $pluck_key, Schema $schema, ?string $relationship_key = null): ResourceData
    {
        $relationship_key = $relationship_key ?? $key;

        return self::make($key, Schema::TYPE_ARRAY)
            ->value(function (self $d) use ($relationship_key, $pluck_key) {
                $resource = $d->whenLoaded($relationship_key);

                // optional relational may not be eager loaded. We want to make sure
                // that MissingValue is returned so that the key are removed from API response.
                if ($resource instanceof MissingValue) {
                    return $resource;
                }

                return collect($resource)->pluck($pluck_key);
            })
            ->schema($schema);
    }

    /**
     * specific function for melandas images resource data
     * @param string $key
     * @return ResourceData
     * @throws Exception
     */
    public static function images(string $key = 'images')
    {
        return self::makeRelationshipCollection(
            $key,
            MediaResource::class,
            'apiImages',
            fn($q) => $q->apiImages ?? []
        )->notNested();
    }

    public function notNested(): self
    {
        $this->on_nested = false;
        return $this;
    }

    /**
     * @param string $key
     * @param string $resource_class
     * @param string|null $relationship_key
     * @param callable|null $data
     * @param SchemaConfig|null $config
     * @return ResourceData
     * @throws Exception
     */
    public static function makeRelationshipCollection(string $key, string $resource_class, ?string $relationship_key = null, ?callable $data = null, SchemaConfig $config = null): ResourceData
    {
        if (!is_a($resource_class, BaseResource::class, true)) {
            throw new Exception("$resource_class parameter must be type BaseResource class");
        }

        $relationship_key = $relationship_key ?? $key;

        $value = function ($d) use ($resource_class, $relationship_key, $data) {
            if ($data) {
                $data = $data($d);
            } else {
                $data = $d->resource->relationLoaded($relationship_key) ? ($d->$relationship_key ?? $d[$relationship_key]) : new MissingValue();
            }
            // TODO: we need to prevent nested property from showing
            //return $resource_class::collection($data)->setNested();
            return $resource_class::collection($data);
        };

        return self::make($key, Schema::TYPE_STRING)
            ->value($value)
            ->schema(Schema::array($key)->items($resource_class::getAsObjectSchema($key, $config)))
            ->optional();
    }

    public function optional(bool $optional = true): self
    {
        $this->optional = $optional;
        return $this;
    }

    public static function image($key)
    {
        return self::makeRelationship(
            $key,
            MediaResource::class,
            $key,
            fn($q) => $q->$key ?? null
        )->notNested();
    }

    /**
     * @param string $key
     * @param string $resource_class
     * @param string|null $relationship_key
     * @param callable|null $data
     * @param SchemaConfig|null $config
     * @param bool $on_nested
     * @param callable|null $value
     * @return ResourceData
     * @throws Exception
     */
    public static function makeRelationship(
        string $key,
        string $resource_class,
        ?string $relationship_key = null,
        ?callable $data = null,
        SchemaConfig $config = null,
        bool $on_nested = false,
        callable $value = null
    ): ResourceData
    {
        if (!is_a($resource_class, BaseResource::class, true)) {
            throw new Exception("$resource_class parameter must be type BaseResource class");
        }

        $relationship_key = $relationship_key ?? $key;

        $value = $value ?? function ($d) use ($resource_class, $relationship_key, $data) {
            if ($data) {
                $data = $data($d);
            } else {
                $data = $d->resource->relationLoaded($relationship_key) ? ($d->$relationship_key ?? $d[$relationship_key]) : new MissingValue();
            }

            if (is_null($data)) $data = new MissingValue();
            return (new $resource_class($data))->setNested();
        };

        return self::make($key, Schema::TYPE_STRING)
            ->value($value)
            ->schema($resource_class::getAsObjectSchema($key, $config))
            ->onNested($on_nested)
            ->optional();
    }

    public function onNested(bool $on_nested): self
    {
        $this->on_nested = $on_nested;
        return $this;
    }

    public static function channel(): ResourceData
    {
        return ResourceData::makeRelationship(
            'channel',
            ChannelResource::class,
            null,
            fn($r) => cache_service()->channels($r->channel_id)
        );
    }

    public function sortable(bool $sortable = true): self
    {
        $this->sortable = $sortable;
        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getOnNested(): bool
    {
        return $this->on_nested;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getSortable(): bool
    {
        return $this->sortable;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getEnum(): ?array
    {
        return $this->enum;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return null|string
     */
    public function getCast(): ?string
    {
        return $this->cast;
    }

    /**
     * @return null
     */
    public function getExample()
    {
        return $this->example;
    }

    public function getParent(): ?string
    {
        return $this->parent;
    }

    /**
     * @return Schema
     */
    public function getSchema(): ?Schema
    {
        return $this->schema;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    public function getNullable(): bool
    {
        return $this->nullable;
    }

    public function getOptional(): bool
    {
        return $this->optional;
    }

    // endregion

    public function toArray(): array
    {
        return [
            "key"      => $this->key,
            "type"     => $this->type,
            "example"  => $this->example,
            "enum"     => $this->enum ?? null,
            "value"    => $this->value ?? null,
            "schema"   => $this->schema ?? null,
            "parent"   => $this->parent ?? null,
            "nullable" => $this->nullable ?? false,
            "cast"     => $this->cast ?? null,
        ];
    }
}
