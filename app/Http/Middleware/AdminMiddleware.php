<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Kiểm tra nếu người dùng không phải admin
        if (!Auth::check() || !Auth::user()->isAdmin) {
            abort(403, 'Access denied');  // Trả về lỗi 403 nếu không phải admin
        }

        return $next($request);
    }

   
}

