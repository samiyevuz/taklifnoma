<?php

namespace App\Events;

use App\Models\RsvpResponse;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RsvpResponseSubmitted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly RsvpResponse $response,
    ) {}
}
