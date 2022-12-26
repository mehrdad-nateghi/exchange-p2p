<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;

    protected $table = 'emails';

    protected $fillable = [
        'template_id',
        'values',
        'reference_type',
        'reference_id',
        'created_at'
    ];
}
