<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CareerJob extends Model
{
    protected $fillable = ['name', 'slug'];

    public function applyLists()
    {
        return $this->hasMany(ApplyList::class, 'job_id');
    }
}
