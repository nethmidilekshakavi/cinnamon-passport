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
    private const BUYER_ROLE = 'buyer';

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
     * Everyone signs up as a buyer. A user becomes a seller later, by opening
     * a brand on the "Sell" page. Nobody can pick a role here: the old
     * grower/collector values are still accepted (the form no longer shows
     * them) so the SMS prototype keeps working, and anything else is refused.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // "077 111 2233" and "0771112233" are the same number. Empty means no phone.
        $phone = preg_replace('/[\s\-()]+/', '', (string) $request->input('phone'));
        $request->merge(['phone' => $phone === '' ? null : $phone]);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'phone' => ['nullable', 'regex:/^(0\d{9}|\+\d{7,15})$/', 'unique:users,phone'],
            'role' => ['nullable', Rule::in([User::ROLE_GROWER, User::ROLE_COLLECTOR])],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        // 'role' is not mass assignable on purpose, so it is set here, from the checked list above.
        $user->role = $request->input('role') ?: self::BUYER_ROLE;
        $user->save();

        event(new Registered($user));

        Auth::login($user);

        return redirect(session()->pull('intent') === 'seller' ? route('seller.create') : '/');
    }
}