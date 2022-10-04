<?php


namespace App\Classes\DocGenerator;

use App\Classes\DocGenerator\Enums\DataFormat;
use App\Classes\DocGenerator\Enums\FormType;
use App\Enums\BaseEnum;
use BenSampo\Enum\Enum;
use Exception;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use InvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;
use JsonSerializable;

class RequestData implements JsonSerializable, Arrayable
{
    protected ?string $rule_string = null;
    // array of accepted values (enum class)
    protected ?array $enum = null;
    //the parent key of this field. Parent should be an array or object
    protected ?string $parent = null;

    protected ?string $description = null;
    protected ?bool $ignore_rule = false;
    protected ?bool $ignore_schema = false;
    protected ?bool $ignore_front_end = false;
    protected ?Schema $schema = null;
    protected ?FrontEndForm $front_end_form = null;

    public function __construct(
        protected string $key,
        protected ?string $type = null,
        protected mixed $example = null,
        protected mixed $rule = ''
    )
    {
    }

    /**
     * @param  string  $key  name of the field
     * @param  string|null  $type  [string, integer, object, array, binary]
     * @param  mixed  $example  example value. used for open api
     * @param  mixed  $rule  Laravel FormRequest validation rule. It accept string, array, and callable.
     *    when callable is passed in as the rule, the first parameter would be the request class itself.
     * @param  string|null  $front_end_rule  validation rule as a string used for front end use
     * @return self
     * @throws Exception
     */
    public static function make(
        string $key,
        ?string $type = null,
        mixed $example = null,
        mixed $rule = null,
        ?string $front_end_rule = null,
    ): self
    {
        if(!is_null($type)){
            $allowed_types = collect([
                Schema::TYPE_STRING, Schema::TYPE_INTEGER,
                Schema::TYPE_OBJECT, Schema::TYPE_ARRAY, Schema::TYPE_BOOLEAN
            ]);

            if (!$allowed_types->contains($type)) {
                throw new Exception(sprintf("Unaccepted request field type %s. Allowed types: %s", $type,
                    $allowed_types->implode(', ')));
            }
        }

        $object = new self($key, $type, $example, $rule);
        if ($front_end_rule) $object = $object->frontEndRule($front_end_rule);

        return $object;
    }

    public static function makeEnum(string $key, string $enum, bool $required = false, array $enum_instances = null): self
    {
        if (!is_a($enum, BaseEnum::class, true)) {
            throw new InvalidArgumentException("$enum must be an Enum class.");
        }

        $example = $enum::getDefaultInstance()->key;

        $instances = $enum_instances ?? $enum::getInstances();

        $enum_keys   = collect($instances)->pluck('key')->all();
        $enum_values = collect($instances)->pluck('value')->all();

        $rule = implode('|', [
            $required ? "required" : "nullable",
            sprintf("in:%s", implode(',', $enum_keys))
        ]);

        $backend_rule = [
            $required ? "required" : "nullable",
            Rule::in($enum_values),
        ];

        return self::make($key, Schema::TYPE_STRING, $example, $backend_rule, $rule)->enum(...$enum::getInstances());
    }

    /**
     * Create requestData for validation rule only. No schema needed.
     * @param  string  $key
     * @param  mixed|null  $rule
     * @param  string|null  $front_end_rule
     * @return RequestData
     * @throws Exception
     */
    public static function makeRule(string $key, mixed $rule  = null, ?string $front_end_rule = null): self
    {
        return self::make($key, null, null, $rule, $front_end_rule)->ignoreSchema()->ignoreFrontEnd();
    }

    /**
     * Create requestData for validation rule only. No schema needed.
     * @param  string  $key
     * @param  string  $front_end_rule
     * @param  Enum  ...$enums
     * @return RequestData
     * @throws Exception
     */
    public static function makeFrontEndRule(string $key, string $front_end_rule, Enum ...$enums): self
    {
        if(!empty($enums)){
            $front_end_rule = empty($front_end_rule) ? '' : $front_end_rule . '|';
            $front_end_rule = $front_end_rule . 'in:' . implode(',', collect($enums)->map(fn(Enum $enum) => $enum->key)->values()->all());
        }

        return self::make($key, null, null, null)
            ->frontEndRule($front_end_rule)
            ->enum(...$enums)
            ->ignoreSchema()->ignoreRule();
    }

    /**
     * @param  string  $key
     * @param  Schema  ...$schemas  schemas that goes into the array
     * @return RequestData
     */
    public static function makeArrayType(string $key, Schema ...$schemas): self
    {
        $data = new self($key, Schema::TYPE_ARRAY);
        return $data->schema(Schema::array($key)->items(...$schemas));
    }

    public function frontEndRule(string $rule_string): self
    {
        $this->rule_string = $rule_string;
        return $this;
    }

    /**
     * @param string $base_rule
     * @param string $table
     * @param string $column
     * @param string|null $parameter
     * @return RequestData
     * @throws Exception
     */
    public function uniqueRule(string $base_rule, string $table, string $column, string $parameter = null): self
    {
        $rule = sprintf("%s|unique:%s,%s", $base_rule, $table, $column);

        if ($parameter) {
            $param = request()->route($parameter);
            $id    = $param instanceof Model ? $param->id : $param;
            $rule  = $rule . ',' . $id;
        }

        return $this->rule($rule)->frontEndRule($base_rule);
    }

    public function enum(Enum ...$enums): self
    {
        $this->enum = $enums;
        $this->addRule('in:' . implode(',', collect($enums)->map(fn(Enum $enum) => $enum->value)->values()->all()));
        return $this;
    }

    public function rule(string $rule): self
    {
        $this->rule = $rule;
        return $this;
    }

    public function addRule(string $rule): self
    {
        if (empty($this->rule)) {
            $this->rule = $rule;
        } elseif (is_array($this->rule)) {
            array_push($this->rule, $rule);
        } elseif (is_string($this->rule)) {
            $this->rule = $this->rule . '|' . $rule;
        }
        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function parent(string $parent): self
    {
        $this->parent = $parent;
        return $this;
    }

    public function ignoreRule(bool $ignore = true): self
    {
        $this->ignore_rule = $ignore;
        return $this;
    }

    public function ignoreSchema(bool $ignore = true): self
    {
        $this->ignore_schema = $ignore;
        return $this;
    }

    public function ignoreFrontEnd(bool $ignore = true): self
    {
        $this->ignore_front_end = $ignore;
        return $this;
    }

    public function getSchema(): ?Schema
    {
        return $this->schema;
    }

    public function schema(Schema $schema): self
    {
        $this->schema = $schema;
        return $this;
    }

    public function getDescription(): ?bool
    {
        return $this->description;
    }

    public function getIgnoreRule(): ?bool
    {
        return $this->ignore_rule;
    }

    public function getIgnoreSchema(): ?bool
    {
        return $this->ignore_schema;
    }

    public function getIgnoreFrontEnd(): ?bool
    {
        return $this->ignore_front_end;
    }

    #[ArrayShape([
        "key" => "string", "type" => "string", "example" => "null|string", "rule" => "mixed|null|string",
        "rule_string" => "null|string", "enum" => "array|null", "parent" => "null|string", "ignore" => "bool"
    ])]
    public function toArray(): array
    {
        return [
            "key" => $this->key,
            "type" => $this->type,
            "example" => $this->example,
            "rule" => $this->rule ?? null,
            "rule_string" => $this->rule_string ?? null,
            "enum" => $this->enum ?? null,
            "parent" => $this->parent ?? null,
        ];
    }

    public function isArrayOfObjects(): bool
    {
        if (!$this->getType() === Schema::TYPE_ARRAY) return false;
        return $this->getKey() === $this->getParent().".*.".Str::afterLast($this->getKey(), '.');
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getParent(): ?string
    {
        return $this->parent;
    }

    public function isArrayOfPrimitives(): bool
    {
        if (!$this->getType() === Schema::TYPE_ARRAY) return false;
        return $this->getKey() === $this->getParent().".*";
    }

    public function toSchema(): Schema
    {
        $type = $this->getType();
        $key = Str::afterLast($this->getKey(), '.');

        $schema = Schema::$type($key);

        if (!empty($this->getExample())) {
            $schema = $schema->example($this->getExample());
        }

        if (!empty($this->getDescription())) {
            $schema = $schema->description($this->getDescription());
        }

        if (!empty($this->getEnum())) {
            $schema = $schema->enum(...collect($this->getEnum())->map(fn(Enum $enum) => $enum->key)->values()->all());
        }
        return $schema;
    }

    public function getExample(): mixed
    {
        return $this->example;
    }

    public function frontEndForm(FormType $form_type = null, DataFormat $data_format = null): self
    {
        $form = $this->makeFrontEndForm();
        if($form_type) $form = $form->formType($form_type);
        if($data_format) $form = $form->dataFormat($data_format);
        $this->front_end_form = $form;
        return $this;
    }

    public function getFrontEndForm(): FrontEndForm
    {
        return empty($this->front_end_form) ? $this->makeFrontEndForm() : $this->front_end_form;
    }

    protected function makeFrontEndForm(): FrontEndForm
    {
        $form = FrontEndForm::make($this->key)->rule($this->rule_string ?? $this->getRule());
        if(!empty($this->getEnum())) $form = $form->options(...$this->getEnum());
        return $form;
    }

    public function getRule(): mixed
    {
        return $this->rule;
    }

    public function getEnum(): ?array
    {
        return $this->enum;
    }

    #[ArrayShape([
        "key" => "string", "type" => "string", "example" => "\null|string", "rule" => "\mixed|null|string",
        "rule_string" => "\null|string", "enum" => "\array|null", "parent" => "\null|string", "ignore" => "bool"
    ])] public function jsonSerialize()
    {
        return $this->toArray();
    }
}
