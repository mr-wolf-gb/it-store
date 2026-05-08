<?php

return [
    /*
    |--------------------------------------------------------------------------
    | User Registration
    |--------------------------------------------------------------------------
    |
    | This option controls whether user self-registration is enabled.
    | When set to false, the registration routes will be disabled and
    | users will not be able to create new accounts.
    |
    */

    'registration_enabled' => (bool) env('REGISTRATION_ENABLED', true),
];
