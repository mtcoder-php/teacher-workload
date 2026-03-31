<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user() ? [
                    'id'        => $request->user()->id,
                    'name'      => $request->user()->name,
                    'email'     => $request->user()->email,
                    'avatar'    => $request->user()->avatar,
                    'avatar_url'=> $request->user()->avatar_url,
                    'role'      => $request->user()->role ? [
                        'id'           => $request->user()->role->id,
                        'name'         => $request->user()->role->name,
                        'display_name' => $request->user()->role->display_name,
                    ] : null,
                    'roles' => $request->user()->role
                        ? [$request->user()->role->name]
                        : [],
                ] : null,
            ],

            // Flash xabarlar — success, error, warning
            'flash' => [
                'success' => $request->session()->get('success'),
                'error'   => $request->session()->get('error'),
                'warning' => $request->session()->get('warning'),
                'info'    => $request->session()->get('info'),
            ],

            // Validation xatolari ham toast orqali ko'rsatiladi
            'errors' => (object) ($request->session()->get('errors')
                ? $request->session()->get('errors')->getBag('default')->getMessages()
                : []
            ),
        ]);
    }
}
