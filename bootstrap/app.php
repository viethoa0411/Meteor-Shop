<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Xử lý redirect khi chưa đăng nhập
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            // Nếu là client route (account, checkout), redirect đến client login
            if ($request->is('account/*') || $request->is('checkout*')) {
                return redirect()->route('client.login')->with('error', 'Vui lòng đăng nhập để tiếp tục');
            }
            
            // Mặc định redirect đến admin login
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục');
        });
    })->create();
