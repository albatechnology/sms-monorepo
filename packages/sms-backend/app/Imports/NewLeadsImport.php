<?php

namespace App\Imports;

use App\Models\Address;
use App\Models\Customer;
use App\Models\Lead;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class NewLeadsImport implements ToCollection, WithValidation, WithHeadingRow, SkipsOnFailure, SkipsOnError
{
    use Importable, SkipsFailures, SkipsErrors;

    private $rows = 0;
    private $countFailed = 0;
    private array $dataValidation = [];
    private $dataFailed = [];

    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (in_array($row['email'], $this->dataValidation) || in_array($row['phone'], $this->dataValidation)) {
                // array_push($this->dataFailed, $row->toArray());
                $this->countFailed++;
            } else {
                DB::transaction(function () use ($row) {
                    $is_new_customer = 0;
                    $customer_id = $row['customer_id'] ?? null;
                    if (!$customer_id) {
                        $customer = Customer::create([
                            'title' => $row['name_title_id'] ?? 1,
                            'first_name' => $row['first_name'],
                            'last_name' => $row['last_name'] ?? null,
                            'date_of_birth' => $row['date_of_birth'] ? date('Y-m-d', strtotime($row['date_of_birth'])) : null,
                            'email' => $row['email'] ?? null,
                            'phone' => $row['phone'] ?? null,
                            'description' => $row['description'] ?? null,
                        ]);
                        $is_new_customer = 1;
                        $customer_id = $customer->id;

                        $address = Address::create([
                            'address_line_1' => $row['address_line_1'] ?? null,
                            'address_line_2' => $row['address_line_2'] ?? null,
                            'address_line_3' => $row['address_line_3'] ?? null,
                            'country' => $row['country'] ?? null,
                            'province' => $row['province'] ?? null,
                            'city' => $row['city'] ?? null,
                            'type' => $row['address_type'] ?? 1,
                            'phone' => $row['address_phone'] ?? null,
                            'customer_id' => $customer_id,
                        ]);

                        $customer->default_address_id = $address->id;
                    }

                    $assign_to_id = $row['assign_to_id'] ?? null;
                    if ($assign_to_id != null) {
                        $user = \App\Models\User::find($assign_to_id);
                        if (!$user) {
                            $user = auth()->user();
                        }
                    } else {
                        $user = auth()->user();
                    }

                    Lead::create([
                        'type' => \App\Enums\LeadType::LEADS,
                        'status' => \App\Enums\LeadStatus::GREEN,
                        'label' => $row['label'],
                        'interest' => $row['interest'],
                        'is_unhandled' => $user->type->is(\App\Enums\UserType::SALES) ? 0 : 1,
                        'channel_id' => $user->getDefaultChannel(),
                        'lead_category_id' => $row['lead_category_id'],
                        'sub_lead_category_id' => $row['sub_lead_category_id'] ?? null,
                        'is_new_customer' => $is_new_customer,
                        'customer_id' => $customer_id,
                        'user_id' => $user->id,
                    ]);
                });
                $this->rows++;
            }

            $this->dataValidation[] = $row['email'];
            $this->dataValidation[] = $row['phone'];
        }
    }

    public function rules(): array
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
            'name_title_id' => ['nullable', Rule::in(['1', '2', '3'])],
            'first_name'   => 'required_if:customer_id,null',
            'last_name'    => 'nullable',
            'date_of_birth' => 'nullable',
            'phone'   => 'nullable|unique:customers,phone',
            'email'   => 'nullable|unique:customers,email',
            'description'     => 'nullable',
            'address_line_1'   => 'required_with:first_name',
            'address_line_2'   => 'nullable',
            'address_line_3'   => 'nullable',

            'country'   => 'nullable',
            'province'   => 'nullable',
            'city'   => 'nullable',
            'postcode'   => 'nullable',
            'address_type' => ['nullable', Rule::in(['1', '2', '3'])],
            'address_phone'   => 'nullable',
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        $this->countFailed++;
    }

    public function onError(\Throwable $e)
    {

    }

    public function getRowCount(): int
    {
        return $this->rows;
    }

    public function getFailedCount(): int
    {
        return $this->countFailed;
    }

    public function getDataFailed(): array
    {
        return $this->dataFailed;
    }
}
