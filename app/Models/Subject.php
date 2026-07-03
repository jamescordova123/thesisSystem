<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['code', 'name', 'units', 'description', 'is_active'])]
class Subject extends Model
{
    /** @return BelongsToMany<Curriculum, $this> */
    public function curriculums(): BelongsToMany
    {
        return $this->belongsToMany(Curriculum::class, 'curriculum_subject')->withTimestamps();
    }

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
