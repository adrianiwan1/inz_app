<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActionHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'action_id', 'user_id', 'start_time', 'end_time', 'elapsed_time',
    ];

    public function action()
    {
        return $this->belongsTo(Action::class); // Powiązanie z akcją
    }

    public function user()
    {
        return $this->belongsTo(User::class); // Powiązanie z użytkownikiem
    }
}
