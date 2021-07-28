<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = ['date','quote','stock_id'];
    protected $appends = ['symbol'];
    protected $hidden = ['id','stock','stock_id','timestamp','created_at','updated_at'];

    public function getSymbolAttribute()
    {
        return $this->stock->symbol;
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
}
