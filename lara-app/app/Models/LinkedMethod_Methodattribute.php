<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LinkedMethod_Methodattribute extends Model
{
    use HasFactory;

    protected $table = 'lnkedmethod_methodattribute';

    protected $fillable = [
        'method_attribute_id',
        'linked_method_id',
        'value'
    ];

    public $timestamps = false;

}
