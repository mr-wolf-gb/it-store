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

    /*
    |--------------------------------------------------------------------------
    | Allowed file extensions for resource uploads
    |--------------------------------------------------------------------------
    |
    | Comma-separated list of allowed file extensions.
    | These extensions will be accepted when uploading files to resources.
    |
    */
    'allowed_extensions' => explode(',', env('UPLOAD_ALLOWED_EXTENSIONS', 'zip,7z,rar,pdf,txt,md,doc,docx,xls,xlsx,ppt,pptx,msi,exe,bat,ps1,sh,json,xml,csv,log,iso,img,dmg,tar,gz,bz2,xz,sql,db,sqlite,apk,ipsw,firmware,rom,bin,nrg,cue,wim,swm,vhd,vhdx')),
];
