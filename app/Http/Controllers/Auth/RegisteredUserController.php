<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * Only growers and collectors can sign up here. Buyers need no account
     * (the verify page is public) and nobody can pick any other role.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // "077 111 2233" and "0771112233" are the same number.
        $request->merge(['phone' => preg_replace('/\s+/', '', (string) $request->phone)]);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'phone' => ['required', 'regex:/^0\d{9}$/', 'unique:users,phone'],
            'role' => ['required', Rule::in([User::ROLE_GROWER, User::ROLE_COLLECTOR])],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        // 'role' is not mass assignable on purpose, so it is set here, from the checked list above.
        $user->role = $request->role;
        $user->save();

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
