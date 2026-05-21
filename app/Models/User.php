<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model {
    protected $fillable = ['name', 'username', 'password', 'role', 'group_name'];
    protected $hidden   = ['password'];

    public function logbook() {
        return $this->hasOne(Logbook::class);
    }
}
