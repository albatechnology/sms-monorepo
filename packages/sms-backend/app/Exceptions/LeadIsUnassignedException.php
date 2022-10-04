<?php

namespace App\Exceptions;

/**
 * Class LeadIsUnassignedException
 * Occurs when an action is denied as a related lead is unassigned.
 * E.g cant create activity for unassigned lead
 *
 * @package App\Exceptions
 */
class LeadIsUnassignedException extends CustomApiException
{

}