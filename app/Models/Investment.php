<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Investment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'investment_instrument_id',
        'date',
        'description',
        'type',
        'quantity',
        'price',
        'total',
        'conversion_rate',
    ];

    protected static function booted()
    {
        static::addGlobalScope('user', function (Builder $builder) {
            if (Auth::check()) {
                $builder->where('user_id', Auth::id());
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function instrument()
    {
        return $this->belongsTo(InvestmentInstrument::class, 'investment_instrument_id');
    }
}
