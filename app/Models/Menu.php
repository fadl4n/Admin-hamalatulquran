<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'menus';

    protected $fillable = [
        'group_menu_id',
        'name',
        'url',
        'sequence',
        'icon',
        'created_at',
        'updated_at',
    ];

    public function groupMenu()
    {
        return $this->belongsTo(GroupMenu::class, 'group_menu_id', 'id');
    }
}
