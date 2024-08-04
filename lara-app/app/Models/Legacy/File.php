<?php

namespace App\Models\Legacy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $table = 'files';

    protected $fillable = [
        'url',
        'alt',
        'type',
        'transaction_id'
    ];

    public $timestamps = false;


    /*
    * Get the Transaction that owns the File
    */
    public function transaction(){
        return $this->belongsTo(Transaction::class);
    }
}
