<?php

namespace App\Contracts;

use App\Exceptions\CustomApiException;
use App\Exceptions\DefaultCompanyAccountDoesNotExistException;
use App\Exceptions\DefaultTenantRequiredException;
use App\Exceptions\GenericAuthorizationException;
use App\Exceptions\GenericErrorException;
use App\Exceptions\InvalidOrderCancellationException;
use App\Exceptions\LeadIsUnassignedException;
use App\Exceptions\SalesOnlyActionException;
use App\Exceptions\SupervisorDoesNotExistException;
use App\Exceptions\UnapprovedOrderException;
use App\Exceptions\UnauthorisedTenantAccessException;
use Exception;

/**
 * Contain custom errors with internal code and description.
 *
 * Class Errors
 */
class Errors
{
    public const SupervisorDoesNotExistException            = 'US01';
    public const DefaultTenantRequiredException             = 'AU01';
    public const UnauthorisedTenantAccessException          = 'AU02';
    public const SalesOnlyActionException                   = 'AU03';
    public const GenericAuthorizationException              = 'ER01';
    public const ExpectedOrderPriceMismatchException        = 'CH01';
    public const DefaultCompanyAccountDoesNotExistException = 'CO01';
    public const LeadIsUnassignedException                  = 'LE01';
    public const GenericErrorException                      = 'GE01';
    public const UnapprovedOrderException                   = 'OR01';
    public const InvalidOrderCancellationException          = 'OR02';

    public static function getErrorByException(string $exception)
    {
        if (!is_a($exception, CustomApiException::class, true)) {
            throw new Exception("$exception must extend from CustomApiException class");
        }

        return collect(self::body())->keyBy('exception')->get($exception);
    }

    public static function body($errorCode = null)
    {
        $data = [
            self::SupervisorDoesNotExistException            => [
                'error_code'  => self::SupervisorDoesNotExistException,
                'label'       => 'Supervisor does not exist for the target user.',
                'exception'   => SupervisorDoesNotExistException::class,
                'description' => 'Occurs when requesting the supervisor detail of a user that does not have a supervisor.',
                'http_code'   => 422,
            ],
            self::DefaultTenantRequiredException             => [
                'error_code'  => self::DefaultTenantRequiredException,
                'label'       => 'User must have default channel to access this resource.',
                'exception'   => DefaultTenantRequiredException::class,
                'description' => 'Occurs when requesting a tenanted resource but user does not have a default channel_id.',
                'http_code'   => 403,
            ],
            self::UnauthorisedTenantAccessException          => [
                'error_code'  => self::UnauthorisedTenantAccessException,
                'label'       => 'User does not have tenant access for this action or resource.',
                'exception'   => UnauthorisedTenantAccessException::class,
                'description' => 'Occurs when user does not have tenant authority for the requested tenanted resource',
                'http_code'   => 403,
            ],
            self::GenericAuthorizationException              => [
                'error_code'  => self::GenericAuthorizationException,
                'label'       => 'Custom user friendly message will be generated depending on the context',
                'exception'   => GenericAuthorizationException::class,
                'description' => 'Occurs when users encounter a generic forbidden issue. Specific reason will be returned along with the exception',
                'http_code'   => 403,
            ],
            self::SalesOnlyActionException                   => [
                'error_code'  => self::SalesOnlyActionException,
                'label'       => 'Only sales are allowed to perform this action!',
                'exception'   => SalesOnlyActionException::class,
                'description' => 'Occurs when a non sales attempting to perform sales only action',
                'http_code'   => 403,
            ],
            self::ExpectedOrderPriceMismatchException        => [
                'error_code'  => self::ExpectedOrderPriceMismatchException,
                'label'       => 'Order price does not match the given expected price!',
                'exception'   => SalesOnlyActionException::class,
                'description' => 'Products and/or discounts could have been updated. App should re fetch cart and discount.',
                'http_code'   => 400,
            ],
            self::DefaultCompanyAccountDoesNotExistException => [
                'error_code'  => self::DefaultCompanyAccountDoesNotExistException,
                'label'       => 'Active company does not have default account',
                'exception'   => DefaultCompanyAccountDoesNotExistException::class,
                'description' => 'Occurs when requesting the default company account, but the active company does not have default account set.',
                'http_code'   => 400,
            ],
            self::LeadIsUnassignedException                  => [
                'error_code'  => self::LeadIsUnassignedException,
                'label'       => 'Creating activity for unassigned lead is not allowed!',
                'exception'   => LeadIsUnassignedException::class,
                'description' => 'Occurs when requesting to create an activity, but the lead is not assigned.',
                'http_code'   => 400,
            ],
            self::GenericErrorException                      => [
                'error_code'  => self::GenericErrorException,
                'label'       => 'Generic error message generated during runtime.',
                'exception'   => GenericErrorException::class,
                'description' => 'Generic exception caught during execution.',
                'http_code'   => 400,
            ],
            self::UnapprovedOrderException                   => [
                'error_code'  => self::UnapprovedOrderException,
                'label'       => 'The requested action is invalid as the related order require approval',
                'exception'   => UnapprovedOrderException::class,
                'description' => 'Occurs when requesting action that require related order to be approved.',
                'http_code'   => 400,
            ],
            self::InvalidOrderCancellationException                   => [
                'error_code'  => self::InvalidOrderCancellationException,
                'label'       => 'The requested action involve cancellation of an order, but is not allowed as the order is no longer on quotation',
                'exception'   => InvalidOrderCancellationException::class,
                'description' => 'Occurs when requesting action that require cancellation of an order.',
                'http_code'   => 400,
            ],
        ];

        return $errorCode ? $data[$errorCode] : $data;
    }

    public static function getConstant(string $constant)
    {
        return constant('self::' . $constant);
    }
}
