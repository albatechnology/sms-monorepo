<?php

namespace App\Http\Requests\API\V1\Auth;

use App\Classes\DocGenerator\BaseApiRequest;
use App\Classes\DocGenerator\Interfaces\ApiDataExample;
use App\Classes\DocGenerator\RequestData;
use App\Models\SmsChannel;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class RegisterRequest extends BaseApiRequest
{
    public static function data(): array
    {
        return [
            RequestData::make('name', Schema::TYPE_STRING, 'difa al maksud', 'required|string'),
            RequestData::make('email', Schema::TYPE_STRING, ApiDataExample::EMAIL, 'required|string|email|unique:users,email'),
            RequestData::make('password', Schema::TYPE_STRING, 'mypassword', 'required|string'),
            RequestData::make('phone', Schema::TYPE_STRING, '085691977176', 'required|string'),
            RequestData::make('channel_id', Schema::TYPE_INTEGER, 1, 'required|integer|exists:sms_channels,id'),
            RequestData::make('channel_id', Schema::TYPE_INTEGER, 1,
                ['required', 'integer', function($attribute, $value, $fail){
                    if(!$value){
                        return;
                    }
                    $smsChannel = SmsChannel::find($value);

                    if(!$smsChannel) $fail("Channel not found");
                    if(!$smsChannel->user) $fail("This channel doesn't supervisor");
                    if(!$smsChannel->channel) $fail("This channel doesn't moves channel");

                }]
            )
        ];
    }

    public function authorize()
    {
        return true;
    }
}
