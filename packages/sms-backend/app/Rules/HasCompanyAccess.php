<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * Currently login user must have company access to
 * the provided company id
 * Class HasCompanyAccess
 * @package App\Rules
 */
class HasCompanyAccess implements Rule
{
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value company id
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return tenancy()->getCompanies()
                        ->pluck('id')
                        ->contains(fn(int $id) => $id == $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'User does not have access to this company.';
    }
}