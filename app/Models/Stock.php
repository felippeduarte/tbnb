<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = ['symbol'];
    protected $hidden = ['id','created_at','updated_at'];

    //uppercase symbol
    public function setSymbolAttribute($value)
    {
        $this->attributes['symbol'] = strtoupper($value);
    }
}
