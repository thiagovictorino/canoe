<?php

namespace App\UseCases\Fund;

use App\Models\Fund;
use App\Models\FundAlias;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UpdateFundUseCase
{
    public function execute(int $id, array $data): void
    {
        $this->validate($data);
        $aliases = $data['aliases'] ?? [];
        unset($data['aliases']);

        $fund = Fund::findOrFail($id);

        DB::transaction(function () use ($fund, $data, $aliases) {
            $fund->update($data);
            $this->replaceAliases($fund, $aliases);
        });
    }

    protected function replaceAliases(Fund $fund, array $aliases): void
    {
        $currentAliases = $fund->aliases()->pluck('name')->toArray();
        $aliasesToDelete = array_diff($currentAliases, $aliases);

        if (! empty($aliasesToDelete)) {
            $fund->aliases()->whereIn('name', $aliasesToDelete)->delete();
        }

        $aliasesToInsert = array_diff($aliases, $currentAliases);

        $bulkInsertData = collect($aliasesToInsert)->map(function ($alias) use ($fund) {
            return ['fund_id' => $fund->id, 'name' => $alias];
        })->toArray();

        if (! empty($bulkInsertData)) {
            FundAlias::insert($bulkInsertData);
        }
    }

    protected function validate(array $data): void
    {
        $rules = [
            'name' => 'string',
            'year' => 'integer',
            'manager_id' => 'integer|exists:managers,id',
            'aliases' => 'array',
            'aliases.*' => 'string',
        ];

        Validator::make($data, $rules)->validate();
    }
}
