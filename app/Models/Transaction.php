<?php
// =====================================================================
// App\Models\Transaction.php
// =====================================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = ['user_id', 'type', 'amount', 'category', 'description', 'date'];

    protected $casts = ['date' => 'date', 'amount' => 'decimal:2'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}


// =====================================================================
// App\Models\Schedule.php
// =====================================================================
// class Schedule extends Model {
//     protected $fillable = ['user_id', 'title', 'date', 'time', 'description'];
//     protected $casts = ['date' => 'date'];
//     public function user(): BelongsTo { return $this->belongsTo(User::class); }
// }


// =====================================================================
// App\Models\Goal.php
// =====================================================================
// class Goal extends Model {
//     protected $fillable = ['user_id', 'title', 'target_amount', 'current_amount', 'deadline'];
//     protected $casts = ['deadline' => 'date', 'target_amount' => 'decimal:2', 'current_amount' => 'decimal:2'];
//     public function user(): BelongsTo { return $this->belongsTo(User::class); }
//     public function getProgressAttribute(): int {
//         return $this->target_amount > 0 ? round(($this->current_amount / $this->target_amount) * 100) : 0;
//     }
// }


// =====================================================================
// App\Models\DailyNote.php
// =====================================================================
// class DailyNote extends Model {
//     protected $fillable = ['user_id', 'date', 'mood', 'note'];
//     protected $casts = ['date' => 'date'];
//     public function user(): BelongsTo { return $this->belongsTo(User::class); }
// }
