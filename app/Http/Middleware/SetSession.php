<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SiteSettings;

class SetSession
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
   * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
   */
  public function handle(Request $request, Closure $next)
  {
    $get_data = SiteSettings::where('id', '=', 1)->first()->toArray();
    session(['siteSettingsData' => $get_data]);
    if ($get_data)
      session(['settings' => json_decode($get_data['ui_settings'])]);
    return $next($request);
  }
}
