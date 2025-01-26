<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class); // Powiązanie z użytkownikiem
    }

    public function histories()
    {
        return $this->hasMany(ActionHistory::class); // Powiązanie z historią akcji
    }
}
