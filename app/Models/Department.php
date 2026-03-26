<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'short_name',
        'code',
        'description',
        'manager_id',
    ];

    public function employees()
    {
        return $this->hasMany(User::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($department) {
            if ($department->employees()->count() > 0) {
                throw new \Exception('Cannot delete department with associated employees.');
            }
        });
    }
}
