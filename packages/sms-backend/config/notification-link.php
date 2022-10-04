<?php

/*
|--------------------------------------------------------------------------
| THIS IS READ-ONLY FILE..!!
| To update the config here, change the notification contract at
| /contracts/notifications.json (this is outside of laravel application folder)
| and run the command "php artisan contract:notification"
*/

return [

    /*
    |--------------------------------------------------------------------------
    | Notification link
    |--------------------------------------------------------------------------
    |
    | Map notification code to its desired mobile app link.
    |
    */
    'ActivityReminder' => "{'index':0,'routes':[{'name':'Customer','state':{'routes':[{'name':'CustomerList'},{'name':'ActivityDetail','params':{'id':%s,'isDeals':false}}]}}]}",
    'NewLeadAssigned' => "{'index':0,'routes':[{'name':'Customer'}]}",
    'DiscountApproval' => "{'index':0,'routes':[{'name':'DiscountApproval'}]}",
    'UnhandledLead' => "{'index':0,'routes':[{'name':'Customer'}]}",
];
