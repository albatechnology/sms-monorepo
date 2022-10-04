<?php

namespace App\Imports;

use App\Enums\PersonTitle;
use App\Models\Customer;
use App\Models\ImportLine;
use BenSampo\Enum\Rules\EnumKey;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomerImport extends BaseImport implements ToModel, WithHeadingRow
{
    public static function getModelName(): string
    {
        return Customer::class;
    }

    /**
     * May provide export sample data
     * @return Collection
     */
    public static function getExportCollection(): Collection
    {
        return Collect([
            self::getHeader(),
            [
                'john', 'doe', 'test@test.com', '08736236132',
                '31-01-1990', 'test customer import', 'MR'
            ]
        ]);
    }

    public static function getHeader(): array
    {
        return [
            'first_name', 'last_name', 'email', 'phone',
            'date_of_birth', 'description', 'title'
        ];
    }

    /**
     * Override to remove company id line
     * @param ImportLine $line
     * @return mixed
     */
    protected function updateModel(ImportLine $line): void
    {
        (static::getModelName())::query()
            ->where(static::getUniqueKey(), $line->data[static::getUniqueKey()])
            ->firstOrFail()
            ->update(static::updateArray($line));
    }

    protected function updateArray(ImportLine $line): array
    {
        return [
            'first_name'    => $line->data['first_name'],
            'last_name'     => $line->data['last_name'] ?? null,
            'email'         => $line->data['email'] ?? null,
            'phone'         => $line->data['phone'] ?? null,
            'date_of_birth' => $line->data['date_of_birth'] ?? null,
            'description'   => $line->data['description'] ?? null,
            'title'         => $line->data['title'] ? PersonTitle::fromKey($line->data['title']) : null,
        ];
    }

    protected function createArray(ImportLine $line): array
    {
        return [
            'first_name'    => $line->data['first_name'],
            'last_name'     => $line->data['last_name'] ?? null,
            'email'         => $line->data['email'] ?? null,
            'phone'         => $line->data['phone'] ?? null,
            'date_of_birth' => $line->data['date_of_birth'] ?? null,
            'description'   => $line->data['description'] ?? null,
            'title'         => $line->data['title'] ? PersonTitle::fromKey($line->data['title']) : null,
        ];
    }

    protected function getValidationRule(array $data): array
    {
        return [
            'first_name'    => 'required|string|min:2|max:100',
            'last_name'     => 'nullable|string|min:2|max:100',
            'email'         => 'required|string|email|unique:customers',
            'phone'         => 'required|numeric|unique:customers',
            'date_of_birth' => 'nullable|date|before:now',
            'description'   => 'nullable|string|max:225',
            'title'         => ['nullable', new EnumKey(PersonTitle::class)],
        ];
    }
}
