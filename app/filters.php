<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	//
});


App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest())
	{
		if (Request::ajax())
		{
			return Response::make('Unauthorized', 401);
		}
		else
		{
			return Redirect::guest('login');
		}
	}
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() !== Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});

/*
|--------------------------------------------------------------------------
| Q&A Custom Filters
|--------------------------------------------------------------------------
*/

Route::filter('user',function($route,$request){
	//check() es una funcion auxiliar de Sentry 2 y devuelve un valor booleano si el usuario esta conectado.
	if(Sentry::check()) {
		//R = está conectado
	} else {
		return Redirect::route('index')->with('error','Tienes que entrar primero');
	}
});

Route::filter('is_guest',function($route,$request){
	//comprobamos si es usuario ya esta dentro del sistema
	if(!Sentry::check()) {
		//R = es un invitado
	} else {
		return Redirect::route('index')->with('error','Usted ya está en el sistema');
	}
});

//se ha agregado un parametro mas $right
//
Route::filter('access_check',function($route,$request,$right){
	//comprobamos si el usuario esta conectado con el metodo Sentry
	if(Sentry::check()) {
		//comprobamos si el ususario tiene acceso a la session mediate el metodo hasAccess
		// pero antes, requiere de un usuario conectado primero, con el metodo getUser()
		if(Sentry::getUser()->hasAccess($right)) {
			//R = conectado y se puede acceder.
		} else {
			return Redirect::route('index')
			->with('error','Usted no tiene suficientes privilegios para acceder a esa página');
		}
	} else {
		return Redirect::route('index')->with('error','Tienes que entrar primero');
	}
});
