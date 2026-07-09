<?php

namespace App\Http\Controllers\Concerns\Login;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait HandlesLoginCrudWriteActions
{
    use HandlesLoginSuccessHelpers;
    use HandlesLoginValidationHelpers;

    public function iniciarSesion(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string|min:6',
            ]);

            $remember = $request->boolean('remember');
            $user = User::where('email', $credentials['email'])->first();
            $response = null;

            // Validar usuario antes de intentar autenticar
            if ($user && ! $this->validarUsuarioAntesLogin($user)) {
                $response = $this->getRespuestaValidacionUsuario($user);
            } elseif (! Auth::attempt($credentials, $remember)) {
                // Intentar autenticar
                $response = back()
                    ->withInput()
                    ->withErrors(['error' => 'Correo o contraseña inválidos']);
            } else {
                // Procesar autenticación exitosa
                $request->session()->regenerate();
                $response = $this->procesarLoginExitoso($request, Auth::user());
            }

            return $response;
        } catch (QueryException $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'error' => 'Error al conectar con la base de datos. '
                        .'Por favor, inténtelo de nuevo más tarde.',
                ]);
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'error' => 'Error al iniciar sesión. Por favor, inténtelo de nuevo más tarde.',
                ]);
        }
    }

    public function store(Request $request)
    {
        // Este método maneja el POST a /login desde el resource route
        // Redirigir al método iniciarSesion
        return $this->iniciarSesion($request);
    }
}
