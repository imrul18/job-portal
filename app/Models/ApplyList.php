<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplyList extends Model
{
    protected $fillable = ['job_name', 'name', 'email', 'phone', 'resume', 'status'];

    public function getResumeUrlAttribute()
    {
        return asset('storage/uploads/' . $this->resume);
    }

    public function job()
    {
        return $this->belongsTo(CareerJob::class, 'job_id');
    }
}
