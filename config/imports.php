<?php

return [
    // Poll interval in seconds for AJAX fallback
    'poll_interval' => env('IMPORTS_POLL_INTERVAL', 5),

    // Number of reports to keep when pruning
    'keep_reports' => env('IMPORTS_KEEP_REPORTS', 50),
];
