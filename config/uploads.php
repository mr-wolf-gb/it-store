<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Upload limits (in KB)
    |--------------------------------------------------------------------------
    |
    | Keep app validation limits aligned with your PHP limits:
    | - upload_max_filesize
    | - post_max_size
    |
    */
    'max_file_kb' => (int) env('UPLOAD_MAX_FILE_KB', 102400),
];
