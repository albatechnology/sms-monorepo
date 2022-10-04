<?php

use App\Enums\LeadStatus;

return [

    /*
    |--------------------------------------------------------------------------
    | Generated Open API file
    |--------------------------------------------------------------------------
    |
    | Setting for filename
    |
    */
    'open_api'                  => [
        'filename' => 'open-api.json',
    ],

    /*
    |-------------------------------------------
    | Current API Version
    |-------------------------------------------
    | That is the default API version of your API (Last version).
    | The idea is that if there is no version when calling the API, this will be used
    */
    'api_latest'                => 'v1',

    /*
    |-------------------------------------------
    | Lead status setting
    |-------------------------------------------
    | Lead status are updated from GREEN => YELLOW => RED => EXPIRED
    | When a lead has stayed in a given status for the defined duration below,
    | we will update the lead to the next status.
    */
    'lead_status_duration_days' => [
        LeadStatus::GREEN  => 14,
        LeadStatus::YELLOW => 14,
        LeadStatus::RED    => 14,
    ],

    /*
    |-------------------------------------------
    | File import
    |-------------------------------------------
    | file import option setting
    */
    'import'                    => [
        'max_size' => 1024 * 20, //20 MB
    ],

    /*
    |-------------------------------------------
    | Invoker setting
    |-------------------------------------------
    | Custom setting for Invoker. These setting should never
    | be changed here. These setting would be overridden when
    | executed from Invoker tool
    |
    */
    'invoker'      => [
        // accepted value: value, key, or description
        'enum_value' => 'value',
    ],

    /*
    |-------------------------------------------
    | Media
    |-------------------------------------------
    | Set how long temporary url for private media should be valid for
    |
    */
    'media'        => [
        'temporary_url_seconds' => 60 * 60 * 24, // 24 hours
    ],

    /*
    |-------------------------------------------
    | Notification
    |-------------------------------------------
    | Notification mode provide option to send notifications to all devices
    | in a single request or device per request at a time. The single request mode
    | should only be used in development.
    |
    */
    'notification' => [
        // available values: single | bulk
        // bulk send notification to all users in a single request
        // single send notification to one user at a time
        'mode' => env('NOTIFICATION_MODE', 'bulk'),
    ],

    /*
    |-------------------------------------------
    | Default Quotation valid time
    |-------------------------------------------
    | Default life time of the quotation for an invoice
    |
    */
    'quotation_valid_for_minutes' => 60 * 24 * 14, // 14 days
];
