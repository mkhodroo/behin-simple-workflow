<?php

namespace Behin\SimpleWorkflow\Middlewares;

use Closure;
use Illuminate\Http\Request;

class RedirectOldRoutes
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->routeIs('simpleWorkflow.inbox.index')) {
            return redirect()->route('simpleWorkflow.inbox.categorized');
        }

        return $next($request);
    }
}