<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLocation extends Model
{
    use HasFactory;

    protected $table = 'user_locations';

    protected $fillable = [
        'home_latitude', 'home_longitude',
        'work_latitude', 'work_longitude',
        'other_latitude', 'other_longitude',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
