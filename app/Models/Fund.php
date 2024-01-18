<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fund extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'year',
        'manager_id',
    ];

    /**
     * @return BelongsTo<Manager>
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(Manager::class);
    }

    /**
     * @return HasMany<FundAlias>
     */
    public function aliases(): HasMany
    {
        return $this->hasMany(FundAlias::class);
    }

    /**
     * @return BelongsToMany<Fund>
     */
    public function duplications(): BelongsToMany
    {
        return $this->belongsToMany(
            Fund::class, 'fund_duplications',
            'original_fund_id',
            'duplicate_fund_id');
    }
}
