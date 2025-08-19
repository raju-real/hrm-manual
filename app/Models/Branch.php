<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "branches";

    protected $fillable = ['name', 'address'];
    public function devices()
    {
        return $this->hasMany(Device::class);
    }
    public function employees()
    {
        return $this->hasMany(User::class);
    }
}
