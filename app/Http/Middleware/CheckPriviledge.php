<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;
use Session;
use App\Models\Priviledge;
use App\Models\Menu;
use Illuminate\Http\Request;

class CheckPriviledge
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $role = Session::get('user');
        $user_id = Session::get('user')['id'];
        $path = $request->getPathInfo();
        $path = str_replace('/fn_get_data', '', $path);
        $path = str_replace('/fn-get-data', '', $path);
        $allow = false;

        // CREATE
        if (strpos($path, 'create') || strpos($path, 'store')) {
            if (strpos($path, 'create')) {
                $path = str_replace('/create', '', $path);
            } else {
                $path = str_replace('/store', '', $path);
            }
            if ($request->method() == 'GET') {
                $checkMenu = Menu::where('url', $path)->withTrashed()->first();
                if ($checkMenu) {
                    $checkPriv = Priviledge::where('role_id', $role)->where('menu_id', $checkMenu->id)->first();
                    if ($checkPriv && ($checkPriv->add == 1 || $checkPriv->edit == 1)) {
                        $allow = true;
                    }
                }
            } else {
                $checkMenu = Menu::where('url', $path)->withTrashed()->first();
                if ($checkMenu) {
                    $checkPriv = Priviledge::where('role_id', $role)->where('menu_id', $checkMenu->id)->first();
                    if ($checkPriv && ($checkPriv->edit == 1 || $checkPriv->add == 1)) {
                        $allow = true;
                    }
                }
            }
        }

        // UPDATE
        else if (strpos($path, 'edit')) {
            $path = explode('/edit', $path);
            $checkMenu = Menu::where('url', $path[0])->withTrashed()->first();
            if ($checkMenu) {
                $checkPriv = Priviledge::where('role_id', $role)->where('menu_id', $checkMenu->id)->first();
                if ($checkPriv && $checkPriv->edit == 1) {
                    $allow = true;
                }
            }
        } else if (strpos($path, 'update')) {
            $path = explode('/update', $path);
            $checkMenu = Menu::where('url', $path[0])->withTrashed()->first();
            if ($checkMenu) {
                $checkPriv = Priviledge::where('role_id', $role)->where('menu_id', $checkMenu->id)->first();
                if ($checkPriv && $checkPriv->edit == 1) {
                    $allow = true;
                }
            }
        }

        // DELETE
        else if (strpos($path, 'delete')) {
            // $path = str_replace('/delete', '', $path);
            // $checkMenu = Menu::where('url', $path)->withTrashed()->first();
            $path = explode('/delete', $path);
            $checkMenu = Menu::where('url', $path[0])->withTrashed()->first();
            if ($checkMenu) {
                $checkPriv = Priviledge::where('role_id', $role)->where('menu_id', $checkMenu->id)->first();
                if ($checkPriv && $checkPriv->delete == 1) {
                    $allow = true;
                }
            }
        }

        // OTHER
        else if (strpos($path, 'fn_import') || strpos($path, 'fn_export') || strpos($path, 'base_import') || strpos($path, 'export_import_view') || strpos($path, 'fn_pdf') || strpos($path, 'fn_nda')) {
            if (strpos($path, 'fn_import')) {
                $path = explode('/fn_import', $path);
            } else if (strpos($path, 'fn_export')) {
                $path = explode('/fn_export', $path);
            } else if (strpos($path, 'base_import')) {
                $path = explode('/base_import', $path);
            } else if (strpos($path, 'export_import_view')) {
                $path = explode('/export_import_view', $path);
            } else if (strpos($path, 'fn_pdf')) {
                $path = explode('/fn_pdf', $path);
            } else if (strpos($path, 'fn_nda')) {
                $path = explode('/fn_nda', $path);
            }
            if (isset($path[0])) {
                $checkMenu = Menu::where('url', $path[0])->withTrashed()->first();
                if ($checkMenu) {
                    $checkPriv = Priviledge::where('role_id', $role)->where('menu_id', $checkMenu->id)->first();
                    if ($checkPriv && $checkPriv->other == 1) {
                        $allow = true;
                    }
                }
            }
        }

        // VIEW
        else {
            if (strpos($path, 'detail')) {
                $path = str_replace('/detail', '', $path);
                $path = substr($path, 0, strpos($path, '/', 1));
            }
            $checkMenu = Menu::where('url', $path)->withTrashed()->first();
            if ($checkMenu) {
                $checkPriv = Priviledge::where('role_id', $role)->where('menu_id', $checkMenu->id)->first();
                if ($checkPriv && $checkPriv->view == 1) {
                    $allow = true;
                }
            }
        }

        if (!$allow) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 401,
                    'message' => 'You don\'t have priviledge to view this page'
                ], 403);
            }
            abort('403');
        }

        return $next($request);
    }
}
