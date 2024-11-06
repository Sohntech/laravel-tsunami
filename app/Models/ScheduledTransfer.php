<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'exp',
        'destinataire',
        'montant',
        'frequence',
        'date_debut',
        'date_fin',
        'heure_execution',
        'is_active',
        'last_execution',
        'next_execution'
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'heure_execution' => 'datetime',
        'last_execution' => 'datetime',
        'next_execution' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function expediteur()
    {
        return $this->belongsTo(User::class, 'exp');
    }

    public function beneficiaire()
    {
        return $this->belongsTo(User::class, 'destinataire');
    }
}