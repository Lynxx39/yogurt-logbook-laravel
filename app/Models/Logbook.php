<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Logbook extends Model {
    protected $fillable = ['user_id'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function stages() {
        return $this->hasMany(LogbookStage::class);
    }

    public function stage(int $n): ?LogbookStage {
        return $this->stages->firstWhere('stage_number', $n);
    }

    public function completedCount(): int {
        return $this->stages->count();
    }

    public function isComplete(): bool {
        return $this->completedCount() === 6;
    }
}
