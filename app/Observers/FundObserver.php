<?php

namespace App\Observers;

use App\Events\Fund\FundCreatedEvent;
use App\Models\Fund;

class FundObserver
{
    /**
     * Handle the Fund "created" event.
     */
    public function created(Fund $fund): void
    {
        event(new FundCreatedEvent($fund));
    }
}
