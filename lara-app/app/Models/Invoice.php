<?php

namespace App\Models;

use App\Enums\InvoiceTypeEnum;
use App\Enums\old\InvoiceStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoices';

    protected $fillable = [
        'support_id',
        'applicant_id',
        'trade_id',
        'trade_net_value',
        'target_account_snapshot',
        'payment_reason',
        'created_at'
    ];

    public $timestamps = false;

    /**
    * Get the LinkedMethod that owns the Invoice
    */
    public function linkedMethod(){
        return $this->belongsTo(LinkedMethod::class,'target_account_id');
    }

    /*
    * Get the User owns the Invoice
    */
    public function user(){
        return $this->belongsTo(User::class, 'applicant_id');
    }

    /*
    * Get the Trade owns the Invoice
    */
    public function trade(){
        return $this->belongsTo(Trade::class, 'trade_id');
    }

    /*
    * Get the Transactions for the Invoice.
    */
    public function transactions(){
        return $this->hasMany(Transaction::class, 'invoice_id');
    }

    /*
    * Enum casting for the status and trade_stage fields
    */
    protected $casts = [
        'status' => InvoiceStatusEnum::class,
        'trade_stage' => InvoiceTypeEnum::class
    ];

}
