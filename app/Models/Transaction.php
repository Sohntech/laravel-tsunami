<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transaction';  // Spécifier le nom de la table

    protected $fillable = [
        'montant',
        'destinataire',
        'agent',
        'exp',
        'type_id'
    ];

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function expediteur()
    {
        return $this->belongsTo(User::class, 'exp');
    }

    public function destinataire()
    {
        return $this->belongsTo(User::class, 'destinataire');
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent');
    }
}