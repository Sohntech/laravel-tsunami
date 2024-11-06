<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transaction';

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

    protected $dates = [
        'created_at',
        'updated_at',
        'cancelled_at'
    ];

    protected $casts = [
        'montant' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'cancelled_at' => 'datetime'
    ];

    protected $with = ['expediteur', 'beneficiaire', 'type'];

    public function expediteur()
    {
        return $this->belongsTo(User::class, 'exp');
    }

    public function beneficiaire()
    {
        return $this->belongsTo(User::class, 'destinataire');
    }

    public function type()
    {
        return $this->belongsTo(Type::class);
    }
}