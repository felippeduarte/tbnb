<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = ['date','quote','stock_id'];
    protected $hidden = ['id','stock','stock_id','timestamp','created_at','updated_at'];
    protected $casts = [
        'quote' => 'decimal:2',
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
}
