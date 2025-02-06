<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Priviledge extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role_id',
        'menu_id',
        'view',
        'add',
        'edit',
        'delete',
        'other'
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class,'menu_id','id');
    }
}
