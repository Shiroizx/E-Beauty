<?php

namespace App\Providers;

use App\Http\Support\AuthIntended;
use App\Models\WishlistItem;
use App\Services\CartService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Pagination\Paginator::defaultView('pagination.tailwind-pink');

        View::composer('layouts.app', function ($view) {
            if (request()->routeIs('login')) {
                $loginReturnUrl = route('login');
                $rd = (string) request()->query('redirect', '');
                $registerReturnUrl = AuthIntended::isSafeRelative($rd)
                    ? route('register', ['redirect' => $rd])
                    : route('register');
            } elseif (request()->routeIs('register')) {
                $rd = (string) request()->query('redirect', '');
                $loginReturnUrl = AuthIntended::isSafeRelative($rd)
                    ? route('login', ['redirect' => $rd])
                    : route('login');
                $registerReturnUrl = route('register');
            } else {
                $rel = AuthIntended::relativeFromRequest(request());
                $loginReturnUrl = AuthIntended::isSafeRelative($rel)
                    ? route('login', ['redirect' => $rel])
                    : route('login');
                $registerReturnUrl = AuthIntended::isSafeRelative($rel)
                    ? route('register', ['redirect' => $rel])
                    : route('register');
            }

            $view->with('navCartCount', app(CartService::class)->getTotalQuantity());
            $view->with(
                'navWishlistCount',
                auth()->check()
                    ? WishlistItem::query()->where('user_id', auth()->id())->count()
                    : 0
            );
            $view->with('loginReturnUrl', $loginReturnUrl);
            $view->with('registerReturnUrl', $registerReturnUrl);
            $view->with('loginCartUrl', route('login', ['redirect' => '/cart']));
        });

        View::composer(['catalog.index', 'home'], function ($view) {
            $view->with(
                'wishlistProductIds',
                auth()->check()
                    ? WishlistItem::query()->where('user_id', auth()->id())->pluck('product_id')->all()
                    : []
            );
        });
    }
}
