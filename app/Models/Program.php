<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['code', 'name', 'description', 'is_active'])]
class Program extends Model
{
    /** @return HasMany<Section, $this> */
    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    /** @return HasMany<Curriculum, $this> */
    public function curriculums(): HasMany
    {
        return $this->hasMany(Curriculum::class);
    }

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
