<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Complimentary access emails
    |--------------------------------------------------------------------------
    |
    | Users with these emails (or is_complimentary flag) get full platform access
    | without payment. Catalog prices remain visible in the builder.
    |
    */
    'emails' => array_values(array_filter(array_map(
        static fn (?string $email) => strtolower(trim((string) $email)),
        explode(',', (string) env('COMPLIMENTARY_EMAILS', 'sardorsamiyevuz@gmail.com'))
    ))),
];
