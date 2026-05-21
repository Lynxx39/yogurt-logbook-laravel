<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogbookStage extends Model {
    protected $fillable = ['logbook_id', 'stage_number', 'data', 'submitted_at'];
    protected $casts    = ['data' => 'array', 'submitted_at' => 'datetime'];

    public function logbook() {
        return $this->belongsTo(Logbook::class);
    }
}
