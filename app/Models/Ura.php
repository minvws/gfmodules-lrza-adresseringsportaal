<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ura extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [ 'ura'];

    protected $table = 'uras';

    // At this point there can be multiple suppliers for a single Ura. However, the current system only uses the
    // first supplier.
    /**
     * @return HasMany<Supplier>
     */
    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class);
    }
}
