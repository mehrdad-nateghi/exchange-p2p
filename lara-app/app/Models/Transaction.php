<?php

namespace App\Models;

use App\Enums\TransactionStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = [
        'payment_id',
        'transaction_method_id',
        'amount',
        'description',
        'created_at'
    ];

    public $timestamps = false;

    /*
    * Get the TransactionMethod for the Transaction
    */
    public function transactionMethod(){
        return $this->belongsTo(TransactionMethod::class, 'transaction_method_id');
    }

    /*
    * Enum casting for the status field
    */
    protected $casts = [
        'status' => TransactionStatusEnum::class
    ];

}
