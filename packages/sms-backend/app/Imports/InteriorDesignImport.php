<?php

namespace App\Imports;

use App\Models\ImportLine;
use App\Models\InteriorDesign;
use App\Models\SupervisorType;
use App\Enums\UserType;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class InteriorDesignImport extends BaseImport implements ToModel, WithHeadingRow
{
    public static function getHeader(): array
    {
        return [
            'name',
            'bum_id',
            'sales_id',
            'religion_id',
            'company',
            'owner',
            'npwp',
            'address',
            'phone',
            'email',
            'bank_account_name',
            'bank_account_holder',
            'bank_account_number',
        ];
    }

    public static function getModelName(): string
    {
        return InteriorDesign::class;
    }

    protected function updateArray(ImportLine $line): array
    {
        return [
            'name' => $line->data['name'],
            'bum_id' => $line->data['bum_id'] ?? null,
            'sales_id' => $line->data['sales_id'] ?? null,
            'religion_id' => $line->data['religion_id'] ?? null,
            'company' => $line->data['company'] ?? null,
            'owner' => $line->data['owner'] ?? null,
            'npwp' => $line->data['npwp'] ?? null,
            'address' => $line->data['address'] ?? null,
            'phone' => $line->data['phone'] ?? null,
            'email' => $line->data['email'] ?? null,
            'bank_account_name' => $line->data['bank_account_name'] ?? null,
            'bank_account_holder' => $line->data['bank_account_holder'] ?? null,
            'bank_account_number' => $line->data['bank_account_number'] ?? null,
        ];
    }

    protected function createArray(ImportLine $line): array
    {
        return [
            'name' => $line->data['name'],
            'bum_id' => $line->data['bum_id'] ?? null,
            'sales_id' => $line->data['sales_id'] ?? null,
            'religion_id' => $line->data['religion_id'] ?? null,
            'company' => $line->data['company'] ?? null,
            'owner' => $line->data['owner'] ?? null,
            'npwp' => $line->data['npwp'] ?? null,
            'address' => $line->data['address'] ?? null,
            'phone' => $line->data['phone'] ?? null,
            'email' => $line->data['email'] ?? null,
            'bank_account_name' => $line->data['bank_account_name'] ?? null,
            'bank_account_holder' => $line->data['bank_account_holder'] ?? null,
            'bank_account_number' => $line->data['bank_account_number'] ?? null,
        ];
    }

    protected function getValidationRule(array $data): array
    {
        return [
            
            'name'                  => 'required',
            'bum_id'                => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')
                    ->where('type', UserType::SUPERVISOR)->where('supervisor_type_id', SupervisorType::where('code', 'manager-area')->first()->id),
            ],
            'sales_id'              => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')
                    ->where('type', UserType::SALES),
                
            ],
            'religion_id'           => [
                'nullable',
                'integer',
                Rule::exists('religions', 'id'),
            ],
            'company'               => 'nullable',
            'owner'                 => 'nullable',
            'npwp'                  => 'nullable',
            'address'               => 'nullable',
            'phone'                 => 'nullable',
            'email'                 => 'nullable',
            'bank_account_name'     => 'nullable',
            'bank_account_holder'   => 'nullable',
            'bank_account_number'   => 'nullable',
        ];
    }
}
