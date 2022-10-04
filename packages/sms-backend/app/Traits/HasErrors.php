<?php


namespace App\Traits;

/**
 * Trait HasErrors
 * @package App\Traits
 */
trait HasErrors
{
    public function addError(string $error_message): static
    {
        $errors = collect($this->errors ?? []);

        $this->errors = $errors->push($error_message)->all();
        return $this;
    }

    public function addErrors(array $errors): static
    {
        foreach ($errors as $error) {
            $this->addError($error);
        }

        return $this;
    }

    public function hasError(): bool
    {
        return !empty($this->errors ?? []);
    }
}
