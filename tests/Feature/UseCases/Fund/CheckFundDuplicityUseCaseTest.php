<?php

use App\Events\Fund\DuplicateFundWarningEvent;
use App\Models\Fund;
use App\Models\Manager;
use App\UseCases\Fund\CheckFundDuplicityUseCase;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->useCase = new CheckFundDuplicityUseCase();
});

describe('Given a new fund created, when', function () {

    it('has a duplication by name, it should register it', function () {
        $manager = Manager::factory()->create();
        $fund = Fund::factory()->create(['name' => 'fund 1', 'manager_id' => $manager->id]);
        $fund2 = Fund::factory()->create(['name' => 'Fund 1', 'manager_id' => $manager->id]);

        $this->useCase->execute($fund);

        $this->assertDatabaseHas('fund_duplications', [
            'original_fund_id' => $fund2->id,
            'duplicate_fund_id' => $fund->id,
            'is_revised' => false,
        ]);

    });

    it('has a duplication by name, it should not register it twice', function () {
        $manager = Manager::factory()->create();
        $fund = Fund::factory()->create(['name' => 'fund 1', 'manager_id' => $manager->id]);
        $fund2 = Fund::factory()->create(['name' => 'Fund 1', 'manager_id' => $manager->id]);

        $this->useCase->execute($fund);
        $this->useCase->execute($fund2);

        $this->assertDatabaseCount('fund_duplications', 1);
    });

    it('has a duplication by name, it should not register it if the fund is not from the same manager', function () {
        $fund = Fund::factory()->create(['name' => 'fund 1']);
        $fund2 = Fund::factory()->create(['name' => 'Fund 1']);

        $this->useCase->execute($fund);
        $this->useCase->execute($fund2);

        $this->assertDatabaseCount('fund_duplications', 0);
    });

    it('has a duplication by alias, it should register a duplication', function () {
        $manager = Manager::factory()->create();
        $fund = Fund::factory()->create(['name' => 'Fund 2', 'manager_id' => $manager->id]);
        $fund2 = Fund::factory()->create(['name' => 'Fund 1', 'manager_id' => $manager->id]);
        $fund2->aliases()->create(['name' => 'fund 2']);

        $this->useCase->execute($fund);

        $this->assertDatabaseHas('fund_duplications', [
            'original_fund_id' => $fund->id,
            'duplicate_fund_id' => $fund2->id,
            'is_revised' => false,
        ]);
    });

    it('has a duplication by name, it should dispatch the event DuplicateFundWarning with the fund and the duplicities as parameters', function () {
        Event::fake([
            DuplicateFundWarningEvent::class,
        ]);
        $manager = Manager::factory()->create();
        $fund = Fund::factory()->create(['name' => 'Fund 1', 'manager_id' => $manager->id]);
        $fund2 = Fund::factory()->create(['name' => 'Fund 1', 'manager_id' => $manager->id]);

        $this->useCase->execute($fund);

        Event::assertDispatched(DuplicateFundWarningEvent::class, function ($event) use ($fund, $fund2) {
            return $event->fund->is($fund2) && $event->duplicates->first()->is($fund);
        });
    });
});
