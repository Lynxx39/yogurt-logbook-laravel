<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware {
    public function handle(Request $request, Closure $next, string $role) {
        $user = session('user');
        if (!$user || $user['role'] !== $role) {
            $redirect = ($user && $user['role'] === 'guru') ? '/teacher' : '/student';
            return redirect($redirect);
        }
        return $next($request);
    }
}
