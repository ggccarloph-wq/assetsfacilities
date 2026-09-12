<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Reservation Lead Days
    |--------------------------------------------------------------------------
    |
    | Minimum advance notice, in days, before an activity may be scheduled.
    | 0 means a requestor may book for today; 3 means the earliest selectable
    | date is three days from now. This value drives both the "min" attribute
    | on the date pickers and the server-side validation rule, so the two can
    | never drift apart.
    |
    */

    'reservation_lead_days' => (int) env('FMO_RESERVATION_LEAD_DAYS', 0),

];
