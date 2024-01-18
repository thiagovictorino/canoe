<?php

namespace App\Events\Fund;

use App\Models\Fund;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DuplicateFundWarningEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Fund $fund;

    /**
     * @var Collection<Fund>
     */
    public Collection $duplicates;

    /**
     * Create a new event instance.
     */
    public function __construct(Fund $fund, Collection $duplicates)
    {
        $this->fund = $fund;
        $this->duplicates = $duplicates;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
