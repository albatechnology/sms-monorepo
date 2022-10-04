<?php

namespace App\Classes\DocGenerator;

use App\Classes\DocGenerator\Enums\DataFormat;
use App\Classes\DocGenerator\Enums\FormType;
use BenSampo\Enum\Enum;
use Illuminate\Contracts\Support\Arrayable;
use JetBrains\PhpStorm\ArrayShape;
use JsonSerializable;

class FrontEndForm  implements Arrayable, JsonSerializable
{
    protected FormType $form_type;
    protected DataFormat $data_format;

    public function __construct(
        protected string $key,
        protected string $label,
        FormType $form_type = null,
        DataFormat $data_format = null,
        protected mixed $rule = '',
        protected array $options = []
    )
    {
        $this->form_type = $form_type ?? FormType::TEXT();
        $this->data_format = $data_format ?? DataFormat::DEFAULT();
    }

    public static function make(string $key, string $label = null): self
    {
        return new self($key, $label ?? $key);
    }

    public function rule(mixed $rule):self
    {
        $this->rule = $rule;
        return $this;
    }

    public function formType(FormType $formType):self
    {
        $this->form_type = $formType;
        return $this;
    }

    public function dataFormat(DataFormat $dataFormat):self
    {
        $this->data_format = $dataFormat;
        return $this;
    }

    public function isRequired(): bool
    {
        return in_array("required", $this->getRule());
    }

    public function getRule(): ?array
    {
        return $this->rule;
    }

    public function options(Enum ...$enums): self
    {
        $options = collect($enums)->map(fn(Enum $enum) => [
            "value" => $enum->key,
            "label" => $enum->label
        ]);
        $this->options = array_merge($this->getOptions(), $options->values()->all());
        return $this;
    }

    #[ArrayShape([
        "key" => "string", "label" => "string", "formType" => "mixed", "dataFormat" => "mixed", "options" => "array",
        "rule" => "null|string"
    ])]
    public function toArray()
    {
        return [
            "key" => $this->getKey(),
            "label" => $this->getLabel(),
            "formType" => $this->getFormType()->value,
            "dataFormat" => $this->getDataFormat()->value,
            "options" => $this->getOptions(),
            "rule" => $this->getRuleString(),
        ];
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getFormType(): FormType
    {
        return $this->form_type;
    }

    public function getDataFormat(): DataFormat
    {
        return $this->data_format;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    public function getRuleString(): ?string
    {
        if(is_array($this->rule)) return implode('|', $this->rule);
        return $this->rule;
    }

    #[ArrayShape([
        "key" => "string", "label" => "string", "formType" => "mixed", "dataFormat" => "mixed", "options" => "array",
        "rule" => "\null|string"
    ])]
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
