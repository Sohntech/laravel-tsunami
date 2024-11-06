<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favori extends Model
{
    use HasFactory;

    protected $table = 'favoris';

    protected $fillable = [
        'user_id',
        'favori_id',
        'alias'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function favori()
    {
        return $this->belongsTo(User::class, 'favori_id');
    }
}