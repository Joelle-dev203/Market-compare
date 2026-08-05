<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $fillable = [
    'departure_city',
    'arrival_city',
];
    public function trips() {
    return $this->hasMany(Trip::class);
}
}
