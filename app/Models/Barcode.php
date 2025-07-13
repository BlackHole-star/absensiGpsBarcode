<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Barcode extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'value',
        'latitude',
        'longitude',
        'radius',
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
