<?php

namespace App\Exceptions;

use App\Contracts\Errors;
use Exception;
use Illuminate\Support\Facades\Log;
use ReflectionClass;
use Throwable;

/**
 * Custom exception triggered when attempting to reduce item stock
 * more than what is available
 * Class InsufficientStockException.
 */
class CustomApiException extends Exception
{
    protected array $additional_render_data = [];

    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getErrorContract()
    {
        $errorCode = Errors::getConstant((new ReflectionClass(get_called_class()))->getShortName());
        if (!$errorCode) {
            Log::critical("Unrecognised error code for exception class: ".get_called_class());
        }

        return Errors::body($errorCode);
    }

    /**
     * @param  array  $additional_render_data
     */
    public function setAdditionalRenderData(array $additional_render_data): void
    {
        $this->additional_render_data = $additional_render_data;
    }

    public function render($request)
    {
        $errorContract = $this->getErrorContract();

        $data = [
            'message' => empty($this->getMessage()) ? $errorContract['label'] : $this->getMessage(),
            'code' => $errorContract['error_code'],
        ];

        return response()->json(array_merge($data, $this->additional_render_data), $errorContract['http_code']);
    }
}
