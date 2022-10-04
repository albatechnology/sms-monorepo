<?php


namespace App\Classes\DocGenerator;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class SchemaConfig
{
    public function __construct(protected bool $nullable = false, protected string $description = "")
    {
    }

    public static function nullable()
    {
        return new self(true);
    }

    public function getNullable(): bool
    {
        return $this->nullable;
    }

    public function optional(bool $optional = true): self
    {
        $this->optional = $optional;
        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    public function applyToSchema(Schema $schema): Schema
    {
        return $schema
            ->nullable($this->nullable)
            ->description($this->description);
    }
}
