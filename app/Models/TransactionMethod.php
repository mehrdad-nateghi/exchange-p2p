<?php

namespace App\Models;

use App\Models\Legacy\Transaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionMethod extends Model
{
    use HasFactory;

    protected $table = 'transaction_methods';

    protected $fillable = [
        'name',
        'is_active'
    ];

    public $timestamps = false;

    /*
    * Get the Transactions for the TransactionMethod
    */
    public function transactions(){
        return $this->hasMany(Transaction::class, 'transaction_method_id');
    }
}
