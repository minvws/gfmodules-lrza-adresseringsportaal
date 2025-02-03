<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Supplier extends Model
{
    use HasUuids;

    protected $fillable = ['endpoint', 'ura_id'];

    protected $table = 'suppliers';

    /**
     * @psalm-suppress TooManyTemplateParams
     * @return BelongsTo<Ura, Supplier>
     */
    public function ura(): BelongsTo
    {
        return $this->belongsTo(Ura::class);
    }
}
