<?php

namespace App\UseCases\Fund;

use App\Events\Fund\DuplicateFundWarningEvent;
use App\Models\Fund;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CheckFundDuplicityUseCase
{
    public function execute(Fund $fund): void
    {
        $duplicities = $this->getDuplicities($fund);

        if ($duplicities->count()) {
            DB::transaction(function () use ($fund, $duplicities) {
                event(new DuplicateFundWarningEvent($fund, $duplicities));
            });
        }
    }

    /**
     * @return Collection<Fund>
     */
    public function getDuplicities(Fund $fund): Collection
    {
        return Fund::query()
            ->where('id', '<>', $fund->id)
            ->where('manager_id', $fund->manager_id)
            ->where(function ($query) use ($fund) {
                $query->where('name', 'ilike', $fund->name)
                    ->orWhereExists(function ($subQuery) use ($fund) {
                        $subQuery->select(DB::raw(1))
                            ->from('fund_aliases')
                            ->whereColumn('fund_aliases.fund_id', 'funds.id')
                            ->where('fund_aliases.name', 'ilike', $fund->name)
                            ->where('fund_aliases.id', '<>', $fund->id);
                    });
            })
            ->whereNotExists(function ($query) use ($fund) {
                $query->select(DB::raw(1))
                    ->from('fund_duplications')
                    ->where(function ($query) use ($fund) {
                        $query->where('original_fund_id', $fund->id)
                            ->orWhere('duplicate_fund_id', $fund->id);
                    });
            })
            ->get();
    }
}
