<?php

namespace App\UseCases\Fund;

use App\Models\Fund;
use Illuminate\Database\Eloquent\Collection;

class ListFundDuplicitiesUseCase
{
    /**
     * @return Collection<Fund>
     */
    public function execute(): Collection
    {
        return Fund::withWhereHas('duplications')->get();
    }
}
