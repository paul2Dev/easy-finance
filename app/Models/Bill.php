<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'amount',
        'is_fixed',
        'status',
        'updated_at'
    ];

    public static function resetPendingStatus()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Update bills from previous months
        DB::table('bills') // Use raw DB query to prevent infinite loop
            ->whereMonth('updated_at', '<>', $currentMonth)
            ->orWhereYear('updated_at', '<>', $currentYear)
            ->update(['status' => 'pending']);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
