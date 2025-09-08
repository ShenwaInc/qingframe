<?php

use Maatwebsite\Excel\Excel;

return [

    'exports' => [

        /*
        |--------------------------------------------------------------------------
        | Chunk size
        |--------------------------------------------------------------------------
        |
        | When using FromQuery, the query is automatically chunked.
        | Here you can specify the chunk size when exporting.
        |
        */
        'chunk_size' => 1000,

        /*
        |--------------------------------------------------------------------------
        | Temporary files
        |--------------------------------------------------------------------------
        |
        | Export jobs can use temporary files to store data before
        | downloading. Here you can configure that.
        |
        */
        'temp_path' => sys_get_temp_dir(),

        /*
        |--------------------------------------------------------------------------
        | CSV Settings
        |--------------------------------------------------------------------------
        */
        'csv' => [
            'delimiter' => ',',
            'enclosure' => '"',
            'escape_character' => '\\',
            'contiguous' => false,
            'input_encoding' => 'UTF-8',
        ],

        /*
        |--------------------------------------------------------------------------
        | Worksheet properties
        |--------------------------------------------------------------------------
        */
        'properties' => [
            'creator' => 'Laravel',
            'lastModifiedBy' => 'Laravel',
            'title' => 'Laravel Excel Export',
            'description' => 'Laravel Excel Export',
            'subject' => 'Laravel Excel Export',
            'keywords' => 'laravel,excel,export',
            'category' => 'Laravel Excel Export',
            'manager' => 'Laravel',
            'company' => 'Laravel',
        ],

    ],

    'imports' => [

        /*
        |--------------------------------------------------------------------------
        | Read Only
        |--------------------------------------------------------------------------
        |
        | When importing you might want to have the data read-only, so you
        | can't accidentally change data while importing.
        |
        */
        'read_only' => true,

        /*
        |--------------------------------------------------------------------------
        | Heading Row Formatter
        |--------------------------------------------------------------------------
        |
        | Configure the heading row formatter.
        | Available options: none|slug|custom
        |
        */
        'heading_row' => [
            'formatter' => 'slug',
        ],

        /*
        |--------------------------------------------------------------------------
        | CSV Settings
        |--------------------------------------------------------------------------
        */
        'csv' => [
            'delimiter' => ',',
            'enclosure' => '"',
            'escape_character' => '\\',
            'contiguous' => false,
            'input_encoding' => 'UTF-8',
        ],

        /*
        |--------------------------------------------------------------------------
        | Worksheet properties
        |--------------------------------------------------------------------------
        */
        'properties' => [
            'creator' => 'Laravel',
            'lastModifiedBy' => 'Laravel',
            'title' => 'Laravel Excel Import',
            'description' => 'Laravel Excel Import',
            'subject' => 'Laravel Excel Import',
            'keywords' => 'laravel,excel,import',
            'category' => 'Laravel Excel Import',
            'manager' => 'Laravel',
            'company' => 'Laravel',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Extension detector
    |--------------------------------------------------------------------------
    |
    | Configure here which Writer/Reader type should be used when the package
    | needs to guess the correct type based on the extension alone.
    |
    */
    'extension_detector' => [
        'xlsx' => Excel::XLSX,
        'xlsm' => Excel::XLSX,
        'xltx' => Excel::XLSX,
        'xltm' => Excel::XLSX,
        'xls' => Excel::XLS,
        'xlt' => Excel::XLS,
        'ods' => Excel::ODS,
        'ots' => Excel::ODS,
        'slk' => Excel::SLK,
        'xml' => Excel::XML,
        'gnumeric' => Excel::GNUMERIC,
        'htm' => Excel::HTML,
        'html' => Excel::HTML,
        'csv' => Excel::CSV,
        'tsv' => Excel::TSV,

        /*
        |--------------------------------------------------------------------------
        | PDF Extension
        |--------------------------------------------------------------------------
        |
        | Configure here which Pdf driver should be used by default.
        | Available options: Excel::MPDF | Excel::TCPDF | Excel::DOMPDF
        */
        'pdf' => Excel::MPDF,
    ],

    /*
    |--------------------------------------------------------------------------
    | Value Binder
    |--------------------------------------------------------------------------
    |
    | PhpSpreadsheet offers a way to hook into the process of a value being
    | written to a cell. In there some assumptions are made on how the
    | value should be formatted. If you want to change those assumptions,
    | you can use a custom value binder.
    |
    | Possible value binders:
    |
    | [x] Maatwebsite\Excel\DefaultValueBinder::class
    | [x] Maatwebsite\Excel\StringValueBinder::class
    | [x] Maatwebsite\Excel\NumberFormatValueBinder::class
    | [x] Maatwebsite\Excel\NullValueBinder::class
    |
    | Default value binder is used when no value binder is specified.
    |
    */
    'value_binder' => [
        'default' => Maatwebsite\Excel\DefaultValueBinder::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | By default PhpSpreadsheet keeps all cell values in memory, however when
    | dealing with large files, this might result into memory issues. If you
    | want to mitigate that, you can configure a cache driver here.
    | When using the illuminate driver, it will store each value in the
    | cache store. This can slow down the process, because it needs to
    | store each value. You can use the "batch" store if you want to
    | only persist to the store when the memory limit is reached.
    |
    | Default: illuminate
    | Supported: memory|illuminate|batch
    |
    */
    'cache' => [
        'driver' => 'memory',
        'batch' => [
            'memory_limit' => 60000,
        ],
        'illuminate' => [
            'store' => null,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Transaction handler
    |--------------------------------------------------------------------------
    |
    | By default the import is wrapped in a transaction. This is useful
    | for when an import may fail and you want to retry it. With the
    | transactions, the previous import gets rolled-back.
    |
    | You can disable the transaction handler by setting this to null.
    | Or you can choose a custom made transaction handler here.
    |
    | Default handlers: null|db
    |
    */
    'transactions' => [
        'handler' => 'db',
    ],

];
