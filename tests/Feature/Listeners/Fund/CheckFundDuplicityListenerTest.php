<?php

use App\Events\Fund\FundCreatedEvent;
use App\Listeners\Fund\CheckFundDuplicity;
use App\Models\Fund;
use Illuminate\Support\Facades\Event;

describe('Given a new fund created, when', function () {
    it('is created, then should dispatch the listener', function () {
        Event::fake([
            FundCreatedEvent::class,
        ]);

        $fund = Fund::factory()->make();
        $fund->save();
        Event::assertDispatched(FundCreatedEvent::class);
        Event::assertListening(
            FundCreatedEvent::class,
            CheckFundDuplicity::class
        );
    });
});
