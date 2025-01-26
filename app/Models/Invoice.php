<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'seller_name',
        'seller_address',
        'seller_nip',
        'buyer_name',
        'buyer_address',
        'buyer_nip',
        'service_name',
        'invoice_number',
        'net_value',
        'tax_rate',
        'gross_value',
        'bank_account_number',
        'issue_date',
        'sale_date',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
