<?php

namespace App\ViewComposers;

use Illuminate\View\View;
use Session;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Menu;

class SidebarMenuComposer
{
    public function __construct()
    {
        //
    }

    public function compose(View $view)
    {
        $priviledge = Role::where('id', Session::get('user')['role_id'])->with('priviledge.menu.groupMenu')->first();
        if ($priviledge) {
            // GET MENU
            $menu = [];
            $iter = 0;
            foreach ($priviledge->priviledge as $key => $value) {
                // check if view is allowed and menu is not empty
                if ($value->view == 1 && $value->menu != []) {
                    // check if current accessed menu is not the same as the previous menu
                    // then increase the iteration for array key & unset the previous menu item
                    if (isset($menu[$iter]) && $menu[$iter]['name'] != $value->menu->groupMenu->name) {
                        $iter++;
                        unset($menuItem);
                    }
                    $menuItem['name'] = $value->menu->groupMenu->name;
                    $menuItem['icon'] = $value->menu->groupMenu->icon;
                    $menuItem['menuItem'][$key]['name'] = $value->menu->name;
                    $menuItem['menuItem'][$key]['url'] = $value->menu->url;
                    
                    $menu[$iter] = $menuItem;
                }
            }
        }
        $view->with('sidebarMenus', isset($menu) ? $menu : []);
    }
}
