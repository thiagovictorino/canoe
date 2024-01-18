<?php

use App\Models\Fund;
use App\Models\Manager;
use App\UseCases\Fund\ListFundDuplicitiesUseCase;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->useCase = resolve(ListFundDuplicitiesUseCase::class);
});

describe('Given a list of duplication, when', function () {
    it('has duplication, should return', function () {
        $manager = Manager::factory()->create();
        $fund1 = Fund::factory()->create(['name' => 'Fund 1', 'manager_id' => $manager->id]);
        $fund2 = Fund::factory()->create(['name' => 'Fund 2', 'manager_id' => $manager->id]);
        $fund3 = Fund::factory()->create(['name' => 'Fund 3', 'manager_id' => $manager->id]);
        $fund4 = Fund::factory()->create(['name' => 'Fund 4', 'manager_id' => $manager->id]);

        DB::table('fund_duplications')->insert([
            'original_fund_id' => $fund1->id,
            'duplicate_fund_id' => $fund2->id,
            'is_revised' => false,
        ]);

        DB::table('fund_duplications')->insert([
            'original_fund_id' => $fund1->id,
            'duplicate_fund_id' => $fund3->id,
            'is_revised' => false,
        ]);

        DB::table('fund_duplications')->insert([
            'original_fund_id' => $fund2->id,
            'duplicate_fund_id' => $fund1->id,
            'is_revised' => false,
        ]);

        DB::table('fund_duplications')->insert([
            'original_fund_id' => $fund3->id,
            'duplicate_fund_id' => $fund4->id,
            'is_revised' => false,
        ]);

        $duplications = $this->useCase->execute();

        $this->assertEquals(3, $duplications->count());

        $first = $duplications->first();
        expect($first->duplications->count())->toBeGreaterThan(0);
    });
});
