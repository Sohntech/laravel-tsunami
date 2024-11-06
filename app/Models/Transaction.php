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
        'type_id',
        'status',
        'cancelled_at',
        'cancel_reason',
        'cancelled_by'
    ];

    protected $casts = [
        'cancelled_at' => 'datetime'
    ];

    // ... relations existantes ...

    public function cancelledByUser()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }
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