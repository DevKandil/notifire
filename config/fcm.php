<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Firebase Project ID
    |--------------------------------------------------------------------------
    |
    | Your Firebase project ID from the Firebase Console
    |
    */
    'project_id' => env('FIREBASE_PROJECT_ID'),

    /*
    |--------------------------------------------------------------------------
    | Firebase Credentials Path
    |--------------------------------------------------------------------------
    |
    | Path to the Firebase service account credentials JSON file.
    | By default, the package looks for the firebase.json file in your Laravel storage directory.
    | It's recommended to keep your Firebase credentials in the storage directory for security.
    | 
    | If you need to use a different location, you can modify this value directly in the published config.
    |
    */
    'credentials_path' => storage_path('firebase.json'),

    /*
    |--------------------------------------------------------------------------
    | FCM API URL
    |--------------------------------------------------------------------------
    |
    | The base URL for the FCM API. This will be automatically constructed
    | using your project ID, but you can override it if needed.
    |
    */
    'api_url' => null, // Will be auto-generated if null

    /*
    |--------------------------------------------------------------------------
    | Default Notification Settings
    |--------------------------------------------------------------------------
    |
    | Default settings for FCM notifications
    |
    */
    'defaults' => [
        'sound' => 'default',
        'priority' => 'normal',
        'time_to_live' => 86400, // 24 hours
    ],

    /*
    |--------------------------------------------------------------------------
    | Test FCM Token
    |--------------------------------------------------------------------------
    |
    | This token is used for testing purposes only. It should be a properly formatted
    | FCM token that follows Firebase's requirements. This is useful when running tests
    | as users won't be able to modify test files in the vendor directory.
    |
    */
    'test_token' => env('FCM_TEST_TOKEN', 'fMYt3W8XSJqTMEIvYR1234:APA91bEH_3kDyFMuXO5awEcbkwqg9LDyZ8QK-9qAw3qsF-4NvUq98Y5X9iJKX2JkpRGLEN_2PXXXPmLTCWtQWYPmL3RKJki_6GVQgHGpXzD8YG9z1EUlZ6LWmjOUCxGrYD8QVnqH1234')
];