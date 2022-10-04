<?php

namespace App\Imports;

use App\Enums\PersonTitle;
use App\Models\ImportLine;
use App\Models\Lead;
use BenSampo\Enum\Rules\EnumKey;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class LeadImport extends BaseImport implements ToModel, WithHeadingRow
{
    /**
     * May provide export sample data
     * @return Collection
     */
    public static function getExportCollection(): Collection
    {
        return Collect([
            self::getHeader(),
            [
                '',
                '',
                '',
                'required',
                '',
                'required if existing customer',

                '1:Mr, 2:Ms, 3:Mrs',
                'required if new customer',
                '',
                '',
                '',
                '',
                '',
                '1:ADDRESS, 2:DELIVERY, 3:BILLING. By default 1',
                'required if new customer',
                '',
                '',

                '',
                '',
                '',
                '',
                '',
            ]
        ]);
    }

    public static function getHeader(): array
    {
        return [
            'assign_to_id',
            'label',
            'interest',
            'lead_category_id',
            'sub_lead_category_id',
            'customer_id',

            'name_title_id',
            'first_name',
            'last_name',
            'date_of_birth',
            'email',
            'phone',
            'description',
            'address_type',
            'address_line_1',
            'address_line_2',
            'address_line_3',

            'country',
            'province',
            'city',
            'postcode',
            'address_phone',
        ];
    }

    public static function getModelName(): string
    {
        return Lead::class;
    }

    protected function updateArray(ImportLine $line): array
    {
        return [
            'assign_to_id' => $line->data['assign_to_id'],
            'label' => $line->data['label'],
            'interest' => $line->data['interest'],
            'lead_category_id' => $line->data['lead_category_id'] ?? null,
            'sub_lead_category_id' => $line->data['sub_lead_category_id'] ?? null,
            'customer_id' => $line->data['customer_id'] ?? null,

            'title' => $line->data['name_title_id'] ? PersonTitle::fromKey($line->data['name_title_id']) : null,
            'first_name' => $line->data['first_name'],
            'last_name' => $line->data['last_name'] ?? null,
            'date_of_birth' => $line->data['date_of_birth'] ?? null,
            'phone' => $line->data['phone'] ?? null,
            'email' => $line->data['email'] ?? null,
            'description' => $line->data['description'] ?? null,
            'address_line_1' => $line->data['address_line_1'],
            'address_line_2' => $line->data['address_line_2'] ?? null,
            'address_line_3' => $line->data['address_line_3'] ?? null,

            'country' => $line->data['country'] ?? null,
            'province' => $line->data['province'] ?? null,
            'city' => $line->data['city'] ?? null,
            'postcode' => $line->data['postcode'] ?? null,
            'address_type' => $line->data['address_type'] ?? null,
            'address_phone' => $line->data['address_phone'] ?? null,
        ];
    }

    protected function createArray(ImportLine $line): array
    {
        return [
            'assign_to_id' => $line->data['assign_to_id'],
            'label' => $line->data['label'],
            'interest' => $line->data['interest'],
            'lead_category_id' => $line->data['lead_category_id'] ?? null,
            'sub_lead_category_id' => $line->data['sub_lead_category_id'] ?? null,
            'customer_id' => $line->data['customer_id'] ?? null,

            'title' => $line->data['name_title_id'] ? PersonTitle::fromKey($line->data['name_title_id']) : null,
            'first_name' => $line->data['first_name'],
            'last_name' => $line->data['last_name'] ?? null,
            'date_of_birth' => $line->data['date_of_birth'] ?? null,
            'phone' => $line->data['phone'] ?? null,
            'email' => $line->data['email'] ?? null,
            'description' => $line->data['description'] ?? null,
            'address_line_1' => $line->data['address_line_1'],
            'address_line_2' => $line->data['address_line_2'] ?? null,
            'address_line_3' => $line->data['address_line_3'] ?? null,

            'country' => $line->data['country'] ?? null,
            'province' => $line->data['province'] ?? null,
            'city' => $line->data['city'] ?? null,
            'postcode' => $line->data['postcode'] ?? null,
            'address_type' => $line->data['address_type'] ?? null,
            'address_phone' => $line->data['address_phone'] ?? null,
        ];
    }

    protected function getValidationRule(array $data): array
    {
        return [
            'assign_to_id' => 'nullable',
            'label' => 'nullable',
            'interest' => 'nullable',
            'lead_category_id'                => [
                'required',
                'integer',
                Rule::exists('lead_categories', 'id')
            ],
            'sub_lead_category_id'              => [
                'nullable',
                'integer',
                Rule::exists('sub_lead_categories', 'id')
            ],
            'customer_id'           => [
                'nullable',
                'integer',
                Rule::exists('customers', 'id'),
            ],
            'name_title_id' => ['nullable', new EnumKey(PersonTitle::class)],
            'first_name'   => 'required_without:customer_id',
            'last_name'    => 'nullable',
            'date_of_birth' => 'nullable',
            'phone'   => 'nullable',
            'email'   => 'nullable',
            'description'     => 'nullable',
            'address_line_1'   => 'required_with:first_name',
            'address_line_2'   => 'nullable',
            'address_line_3'   => 'nullable',

            'country'   => 'nullable',
            'province'   => 'nullable',
            'city'   => 'nullable',
            'postcode'   => 'nullable',
            'address_type'   => 'required_with:first_name',
            'address_phone'   => 'nullable',
        ];
    }
}
