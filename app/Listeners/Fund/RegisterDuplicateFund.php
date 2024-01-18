<?php

namespace App\Listeners\Fund;

use App\Events\Fund\DuplicateFundWarningEvent;
use Illuminate\Support\Facades\DB;

class RegisterDuplicateFund
{
    public function handle(DuplicateFundWarningEvent $event): void
    {
        $duplicateEntries = $event->duplicates->map(function ($duplicate) use ($event) {
            return [
                'original_fund_id' => $event->fund->id,
                'duplicate_fund_id' => $duplicate->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });
        if ($duplicateEntries->count()) {
            DB::table('fund_duplications')->insert($duplicateEntries->toArray());
        }
    }
}
