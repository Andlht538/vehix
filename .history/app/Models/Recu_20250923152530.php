<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recu extends Model
{
    /** @use HasFactory<\Database\Factories\RecuFactory> */
    use HasFactory;

        protected $fillable = [
        'user_id',
        'receiptable_id',
        'receiptable_type',
        'receipt_number',
        'receipt_date',
        'amount',
        'vendor',
        'description',
        'file_path',
    ];

    protected $casts = [
        'receipt_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function receiptable()
    {
        return $this->morphTo();
    }
}
