<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Illuminate\Support\Str;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Filament\Navigation\NavigationItem;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use App\Filament\Customer\Resources\OrderResource;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class CustomerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('customer')
            ->path('customer') // TODO set to '' for no suffix of the URL
            ->brandName('Store')
            ->login()
            ->registration()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->navigationItems([
                NavigationItem::make('Admin Panel')
                    ->visible(fn() => Auth::user()->is_admin)
                    ->url(fn() => route('filament.admin.auth.login'))
                    ->icon('heroicon-o-presentation-chart-line'),
                NavigationItem::make('Shopping Cart')
                    ->icon('heroicon-s-shopping-cart')
                    ->group('Orders')
                    ->hidden(function () {
                        if (
                            Str::containsAll(URL::current(), [
                                'orders',
                                'edit',
                            ])
                            &&
                            Auth::user()?->hasShoppingCart()
                        ) {
                            return true;
                        }
                        if (! Auth::user() | ! Auth::user()?->hasShoppingCart()) {
                            return true;
                        }

                        return false;
                    })
                    ->url(function () {
                        if (Auth::user()?->hasShoppingCart()) {
                            return OrderResource::getUrl('edit', [
                                'record' => Auth::user()->shoppingCart->id,
                            ]);
                        }
                    }),
            ])
            ->discoverResources(in: app_path('Filament/Customer/Resources'), for: 'App\\Filament\\Customer\\Resources')
            ->discoverPages(in: app_path('Filament/Customer/Pages'), for: 'App\\Filament\\Customer\\Pages')
            ->pages([
                Pages\Dashboard::class, // TODO remove or replace with custom welcome page
            ])
            ->discoverWidgets(in: app_path('Filament/Customer/Widgets'), for: 'App\\Filament\\Customer\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class, // TODO make this wider or add another widget beside it?
                // Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
