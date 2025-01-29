<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class LoginController extends Controller
{
    public function index(): HtmlString
    {
        return new HtmlString("
                <h1>
                    Do you want to go log in as <a href='/loginAsAdmin'>
                        admin
                    </a>
                    or  
                    <a href='/loginAsCustomer'>
                        customer
                    </a>
                    ?
                </h1>
            ");
    }

    public function loginAsAdmin(): RedirectResponse
    {
        Auth::loginUsingId(1);

        return to_route('filament.admin.auth.login');
    }

    public function loginAsCustomer(): RedirectResponse
    {
        Auth::loginUsingId(2);

        return to_route('filament.customer.auth.login');
    }
}
