<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory;

    protected $table = 'contact_groups';

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    /**
     * @return HasMany
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class, 'group_id');
    }
}
