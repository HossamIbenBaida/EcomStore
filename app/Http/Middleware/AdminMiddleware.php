<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Session ;
use App\Models\Client ;
class AdminMiddleware
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
        if(Session::get('client')){ 
        $client = Client::where('id', Session::get('client')->id)->first();
        if($client->role == 1){
            return $next($request);
        }
        else{
            return redirect('/');
        }
    }else{
        return redirect('/');
    }
        
    }
}
