<?php

return [
    'credentials' => [
        'file' => env('FIREBASE_CREDENTIALS', storage_path('firebase-credentials.json')),
    ],

    'database_url' => env('FIREBASE_DATABASE_URL', 'https://wizi-learn.firebaseio.com'),

    'project_id' => env('FIREBASE_PROJECT_ID', 'wizi-learn'),

    'storage_bucket' => env('FIREBASE_STORAGE_BUCKET', 'wizi-learn.firebasestorage.app'),

    'messaging_sender_id' => env('FIREBASE_MESSAGING_SENDER_ID', '69521612278'),

    'app_id' => env('FIREBASE_APP_ID', '1:69521612278:web:94878d39de047f667c7bd7'),

    'measurement_id' => env('FIREBASE_MEASUREMENT_ID', 'G-01Y7WC8383'),
];
