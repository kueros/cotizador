<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\LogAdministracion;
use App\Http\Controllers\MyController;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $users = User::paginate();

        return view('user.index', compact('users'))
            ->with('i', ($request->input('page', 1) - 1) * $users->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $user = new User();

        return view('user.create', compact('user'));
    }


	/**
	 * Store a newly created resource in storage.
	 */
	public function store()
	{
		#dd(Auth::user()->username);

		User::create($_POST);
		$clientIP = \Request::ip();
		$userAgent = \Request::userAgent();
		#dd($userAgent);
		$message = "Creó el usuario " . $_POST['username'];
		Log::info($message);
		$log = LogAdministracion::create([
			'username' => Auth::user()->username,
			'action' => "users.store",
			'detalle' => $message,
			'ip_address' => json_encode($clientIP),
			'user_agent' => json_encode($userAgent)
		]);
		$log->save();

		return Redirect::route('users.index')
		->with('success', 'Usuario creado correctamente.');
	}
    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $user = User::find($id);

        return view('user.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $user = User::find($id);

        return view('user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
#	public function update($request) //UserRequest $request, User $user): RedirectResponse
	public function update(UserRequest $request, User $user): RedirectResponse
    {
		#dd('kdk1 ' . $request);
        $user->update($request->validated());

        return Redirect::route('users.index')
            ->with('success', 'User updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        User::find($id)->delete();

        return Redirect::route('users.index')
            ->with('success', 'User deleted successfully');
    }

}
