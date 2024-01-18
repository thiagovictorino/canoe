<?php

namespace App\Listeners\Fund;

use App\UseCases\Fund\CheckFundDuplicityUseCase;

class CheckFundDuplicity
{
    public function handle(object $event): void
    {
        $useCase = resolve(CheckFundDuplicityUseCase::class);
        $useCase->execute($event->fund);
    }
}
