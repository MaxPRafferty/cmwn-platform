<?php

namespace app\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class PreviousPassword
{

    public function __construct(Guard $auth)
    {
        $this->user = $auth->user();
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (empty($this->user->previous_password)) {
            $errorCode = 'RESET_PASSWORD';
            $statusCode = 401;
            $message = 'Password must be reset';

            $response = [
                'error' => [
                    'code' => $errorCode,
                    'http_code' => $statusCode,
                    'message' => $message,
                ],
            ];

            return response($response, $statusCode);
        }

        return $next($request);
    }
}
