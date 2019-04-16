<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

use App\AccountActivation;
use App\User;
use App\PasswordReset;
use App\Mail\ActivateAccount;
use App\Mail\PasswordChanged;
use App\Mail\ResetPassword;

class UserController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Display the login/register page
     *
     * @param Request $request
     * @return Response
     */
    public function login(Request $request)
    {
        // If user is authenticated, redirect to main menu
        if (Auth::check()) {
            return redirect('/', 303);
        }
        return view('user.login');
    }

    /**
     * Handle the login attempt
     *
     * @param Request $request
     * @return Response`
     */
    public function authenticate(Request $request)
    {
        $email = strtolower($request->input('email'));
        $password = $request->input('password');
        $remember = $request->input('remember');
        $credentials = array(
            'email' => $email,
            'password' => $password,
            'is_active' => true
        );

        if (Auth::attempt($credentials, $remember)) {
            // The user is active, not suspended, and exists
            return redirect('/', 303)->with('success_message', 'Login berhasil.');
        } elseif (User::where('email', $email)->where('is_active', false)->first()) {
            // The user is not active
            return redirect('/login', 303)
                ->with('warning_message', 'Akun Anda belum aktif. Mohon cek email Anda.');
        } else {
            return redirect('/login', 303)
                ->with('error_message', 'Email atau password Anda tidak dapat dikenali.');
        }
    }

    /**
     * Handle the logout attempt
     *
     * @param Request $request
     * @return Response
     */
    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/')->with('success_info', 'Logout berhasil.');
    }

    /**
     * Insert a new user into the database
     *
     * @param Request $request
     * @return Response
     */
    public function insert(Request $request)
    {
        $name = $request->input('name');
        $mobile_number = $request->input('mobile-number');
        $organization_name = $request->input('organization-name');
        $email = strtolower($request->input('email'));
        $password = $request->input('password');
        // Encrypt the password
        $password_hashed = Hash::make($password);

        $credentials = array(
            'name' => $name,
            'organization_name' => $organization_name,
            'mobile_number' => $mobile_number,
            'email' => $email,
            'password' => $password_hashed
        );

        // Check if the email has been used before
        if (User::where('email', $email)->first()) {
            return view('user.login', ['error_message' => 'Email telah terdaftar.']);
        } else {
            $user = User::create($credentials);
            // By default, the first user in the database is assigned as an admin
            if (User::count() == 1) {
                $user->is_admin = true;
                $user->save();
            }

            // Send email for account activation
            // Generate a token for account activation
            $token = bin2hex(random_bytes(32));
            AccountActivation::create([
                'user_id' => $user->id,
                'token' => $token
            ]);
            Mail::to($user)
                ->bcc(config('mail.from.address'))
                ->send(new ActivateAccount($user, $token));

            return redirect('/login', 303)->with(
                'success_message',
                'Akun Anda sudah berhasil didaftarkan. Silakan cek email Anda untuk aktivasi.'
            );
        }
    }

    /**
     * Activate an user account
     * @param Request $request
     * @param string $token
     * @return Response
     */
    public function activate(Request $request, string $token)
    {
        $account_activation = AccountActivation::where('token', $token)->first();
        if (!$account_activation) {
            return redirect('/login', 303)
                ->with('error_message', 'Tautan yang Anda masukkan tidak valid.');
        }
        $user = $account_activation->user;
        if ($user->is_active) {
            return redirect('/login', 303)
                ->with('success_message', 'Akun Anda sudah berhasil diaktivasi. Silakan login.');
        }
        $user->is_active = true;
        $user->save();

        return redirect('/login', 303)
            ->with('success_message', 'Akun Anda sudah berhasil diaktivasi. Silakan login.');
     }

    /**
     * Display the edit profile form
     *
     * @param Request $request
     * @return Response
     */
    public function edit_profile(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login', 303)
                ->with('error_message', 'Anda diharuskan login terlebih dahulu untuk mengubah profil.');
        }

        $user = Auth::user();
        return view('user.profile.edit', ['user' => $user]);
    }

    /**
     * Update the user password into the database
     *
     * @param Request $request
     * @return Response
     */
    public function update_profile(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login', 303)
                ->with('error_message', 'Anda diharuskan login terlebih dahulu untuk mengubah profil.');
        }

        $name = $request->input('name');
        $organization_name = $request->input('organization-name');
        $mobile_number = $request->input('mobile-number');
        Auth::user()->update([
            'name' => $name,
            'organization_name' => $organization_name,
            'mobile_number' => $mobile_number
        ]);

        return redirect('/', 303)->with('success_message', 'Profil Anda telah berhasil diubah.');
    }

    /**
     * Display the edit password form
     *
     * @param Request $request
     * @return Response
     */
    public function edit_password(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login', 303)
                ->with('error_message', 'Anda diharuskan login terlebih dahulu untuk mengubah password.');
        }
        $user = Auth::user();
        return view('user.password.edit.form', ['user' => $user]);
    }

    /**
     * Update the user password into the database
     *
     * @param Request $request
     * @return Response
     */
    public function update_password(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login', 303)
                ->with('error_message', 'Anda diharuskan login terlebih dahulu untuk mengubah password.');
        }
        $user = Auth::user();
        $old_password = $request->input('old-password');
        $new_password = $request->input('new-password');
        // Compare the old password from the user to the password in the database
        if (!Hash::check($old_password, $user->password)) {
            return redirect('/edit_password', 303)
                ->with('error_message', 'Password lama yang Anda masukkan salah.');
        } else {

            // Encrypt the password
            $new_password_hashed = Hash::make($new_password);
            $user->update([
                'password' => $new_password_hashed
            ]);
            // Send email to user for acknowledgement
            Mail::to($user)
                ->bcc(config('mail.from.address'))
                ->send(new PasswordChanged($user));
            // Logout
            Auth::logout();
            return redirect('/login', 303)
                ->with('success_message', 'Password Anda telah berhasil diganti. Silakan login ulang.');
        }
    }

    /**
     * Display the forget password form
     *
     * @param Request $request
     * @return Response
     */
    public function forget_password(Request $request)
    {
        if (Auth::check()) {
            return redirect('/', 303);
        }
        return view('user.password.forget.form');
    }

    /**
     * Add password reset record into the database
     *
     * @param Request $request
     * @return Response
     */
    public function insert_password_reset(Request $request)
    {
        $email = $request->input('email');
        $email = strtolower($email);
        // Generate token
        $token = bin2hex(random_bytes(32));
        $password_reset = PasswordReset::create([
            'email' => $email,
            'token' => $token
        ]);
        $user = User::where('email', $email)->first();
        if ($user) {
            $create_time = $password_reset->create_timestamp->toDateTimeString();
            // Send email to user for follow up
            Mail::to($user)
                ->bcc(config('mail.from.address'))
                ->send(new ResetPassword($user, $token, $create_time));
        }
        return redirect('/login', 303)
            ->with('success_message',
                   'Permintaan untuk mengatur ulang (reset) password Anda telah berhasil diproses. '.
                   'Jika email Anda terdaftar di database, '.
                   'maka Anda akan menerima email instruksi untuk mengatur ulang (reset) password. '.
                   'Silakan cek email Anda untuk proses lebih lanjut.');
   }

    /**
     * Display the reset password form
     *
     * @param Request $request
     * @return Response
     */
    public function reset_password(Request $request, $token)
    {
        $now = Carbon::now();
        $password_reset = PasswordReset::where('token', $token)->first();
        if (!$password_reset) {
            return redirect('/login', 303)
                ->with('error_message', 'Tautan yang Anda masukkan tidak valid.');
        } elseif ($password_reset->is_used ||
            ($now->timestamp > ($password_reset->create_timestamp->timestamp + 86400))) {
            return redirect('/login', 303)
                ->with('error_message', 'Tautan yang Anda masukkan sudah tidak berlaku.');
        }
        // Make sure the token cannot be used again
        PasswordReset::where('token', $token)->update([
            'is_used' => true,
            'use_timestamp' => $now->timestamp

        ]);
        $user = $password_reset->user;
        return view('user.password.reset.form', ['user' => $user]);
    }

    /**
     * Update the user password into the database
     *
     * @param Request $request
     * @return Response
     */
    public function update_forgotten_password(Request $request)
    {
        $email = $request->input('email');
        $email = strtolower($email);
        $password = $request->input('password');
        // Encrypt the password
        $password_hashed = Hash::make($password);

        $user = User::where('email', $email)->first();
        $user->update([
            'password' => $password_hashed
        ]);

        // Send email to user for acknowledgement
        Mail::to($user)
            ->bcc(config('mail.from.address'))
            ->send(new PasswordChanged($user));

        // Logout
        Auth::logout();
        return redirect('/login', 303)
            ->with('success_message', 'Password Anda telah berhasil diganti. Silakan login ulang.');
    }
}
