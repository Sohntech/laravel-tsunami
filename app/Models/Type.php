<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    use HasFactory;

    protected $table = 'type';

    protected $fillable = [
        'libelle',
        'description'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}