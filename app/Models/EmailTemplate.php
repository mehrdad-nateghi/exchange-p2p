<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $table = 'email_templates';

    protected $fillable = [
        'name',
        'body',
        'attributes'
    ];

    public $timestamps = false;


    /*
    * Get the Emails for the EmailTemplate
    */
    public function emails(){
        return $this->hasMany(Email::class, 'template_id');
    }
}
