<?php

use App\Models\Fund;
use App\Models\Manager;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->useCase = resolve(\App\UseCases\Fund\UpdateFundUseCase::class);
});

describe('Given a created fund, when', function () {
    it('sends data to be updated, then should update the fund', function () {
        $manager = Manager::factory()->create();
        $fund = Fund::factory()->create();
        $fund->aliases()->create(['name' => 'Alias 1']);

        $data = [
            'name' => 'New Fund Name',
            'year' => 2021,
            'manager_id' => $manager->id,
            'aliases' => [fake()->word, fake()->word, fake()->word],
        ];

        $this->useCase->execute($fund->id, $data);

        $this->assertDatabaseHas('funds', [
            'id' => $fund->id,
            'name' => 'New Fund Name',
            'year' => 2021,
            'manager_id' => $manager->id,
        ]);

        $fund->refresh();

        expect($fund->aliases->pluck('name'))
            ->toHaveCount(3)
            ->toContain($data['aliases'][0])
            ->toContain($data['aliases'][1])
            ->toContain($data['aliases'][2]);
    });

    it('sends data invalid data, then should thrown an exception', function () {
        $fund = Fund::factory()->create();
        $fund->aliases()->create(['name' => 'Alias 1']);

        $data = [
            'name' => 'New Fund Name',
            'year' => 'invalid year',
            'manager_id' => 'invalid manager id',
            'aliases' => [fake()->word, fake()->word, fake()->word],
        ];

        $this->expectException(ValidationException::class);
        $this->useCase->execute($fund->id, $data);

    });
});
