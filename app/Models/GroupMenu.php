<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupMenu extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'group_menus';

    protected $fillable = [
        'name',
        'icon',
        'sequence',
        'created_at',
        'updated_at',
    ];

    public function menu()
    {
        return $this->hasMany(Menu::class, 'group_menu_id', 'id');
    }
}
