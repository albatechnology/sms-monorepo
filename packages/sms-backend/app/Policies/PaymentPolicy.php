<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\Payment as PaymentPolicyModel;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class PaymentPolicy
{
    use HandlesAuthorization;

    public function __construct()
    {
        //
    }

    /**
     * Determine whether the user can view any payment policies.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user)
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can view the payment policy.
     *
     * @param User $user
     * @param PaymentPolicyModel $paymentPolicy
     * @return Response
     */
    public function view(User $user, PaymentPolicyModel $paymentPolicy)
    {
        return ($user->is_admin || $paymentPolicy->added_by_id == $user->id)
            ? $this->allow()
            : $this->deny('Unauthorised');
    }


    /**
     * Determine whether the user can create payment policies.
     *
     * @param User $user
     */
    public function create(User $user, PaymentPolicyModel $payment, Order $order)
    {
        return ($user->is_admin || $order->user_id == $user->id)
            ? $this->allow()
            : $this->deny('Unauthorised');
    }

    /**
     * Determine whether the user can update the payment policy.
     *
     * @param User $user
     * @param PaymentPolicyModel $paymentPolicy
     */
    public function update(User $user, PaymentPolicyModel $paymentPolicy)
    {
        return ($user->is_admin || $paymentPolicy->added_by_id == $user->id)
            ? $this->allow()
            : $this->deny('Unauthorised');
    }
}