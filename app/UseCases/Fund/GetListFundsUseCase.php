<?php

namespace App\UseCases\Fund;

use App\Models\Fund;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class GetListFundsUseCase
{
    protected array $allowedFilters = [
        'name',
        'year',
        'manager_id',
    ];

    /**
     * @return Collection<Fund>
     */
    public function execute(array $filters = []): Collection
    {
        $this->validateFilters($filters);

        $fund = $this->applyFilters($filters, Fund::query());

        return $fund->get();
    }

    public function applyFilters(array $filters, Builder $fund): Builder
    {
        if (empty($filters)) {
            return $fund;
        }

        if (isset($filters['name'])) {
            $fund->where('name', 'ilike', $filters['name']);
        }

        if (isset($filters['year'])) {
            $fund->where('year', $filters['year']);
        }

        if (isset($filters['manager_id'])) {
            $fund->where('manager_id', $filters['manager_id']);
        }

        return $fund;
    }

    protected function validateFilters(array $filters): void
    {
        $invalidFilters = array_diff(array_keys($filters), $this->allowedFilters);

        if (! empty($invalidFilters)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid filters: %s', implode(', ', $invalidFilters))
            );
        }
    }
}
