<?php

use App\Models\Fund;
use App\Models\Manager;
use App\UseCases\Fund\GetListFundsUseCase;

beforeEach(function () {
    $this->useCase = resolve(GetListFundsUseCase::class);
});

describe('Given a list of founds', function () {

    it('when it executes, then should return all funds', function () {
        Fund::factory()->count(10)->create();
        $funds = $this->useCase->execute();
        expect($funds)->toHaveCount(10);
    });

    it('when it executes filtering by name, it should return the correct funds ', function () {
        Fund::factory()->create(['name' => 'Fund 1']);
        Fund::factory()->create(['name' => 'Fund 2']);
        Fund::factory()->create(['name' => 'Fund 3']);

        $funds = $this->useCase->execute(['name' => 'fund 1']);
        expect($funds)
            ->toHaveCount(1)
            ->and($funds->first()->name)
            ->toBe('Fund 1');
    });

    it('when it executes filtering by year, it should return the correct funds ', function () {
        Fund::factory()->create(['year' => 2020]);
        Fund::factory()->create(['year' => 2021]);
        Fund::factory()->create(['year' => 2022]);

        $funds = $this->useCase->execute(['year' => 2020]);
        expect($funds)
            ->toHaveCount(1)
            ->and($funds->first()->year)
            ->toBe(2020);
    });

    it('when it executes filtering by manager_id, it should return the correct funds ', function () {
        $manager = Manager::factory()->create();
        Fund::factory()->create();
        Fund::factory()->create(['manager_id' => $manager->id]);
        Fund::factory()->create();

        $funds = $this->useCase->execute(['manager_id' => $manager->id]);
        expect($funds)
            ->toHaveCount(1)
            ->and($funds->first()->manager_id)
            ->toBe($manager->id);
    });

    it('when it executes filtering using an invalid filter, it should throw an exception ', function () {
        $this->expectException(\InvalidArgumentException::class);
        $this->useCase->execute([
            'name' => 'valid_name',
            'invalid_filter' => 'invalid_value'
        ]);
    });
});
