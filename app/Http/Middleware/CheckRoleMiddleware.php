<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class CheckRoleMiddleware
{
  /**
   * Handle the incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @paramstring  $role
   * @return mixed
   */
  public function handle($request, Closure $next, $role)
  {
    if ($request->user()->role == User::ROLE_USER) {
      return $next($request);
    } else if ($request->user()->role == User::ROLE_STUDENT) {
      return $next($request);
    } else if ($request->user()->role == User::ROLE_OSIS) {
      return $next($request);
    } else if ($request->user()->role == User::ROLE_ADMIN) {
      return $next($request);
    }
    abort(401);
  }
}
