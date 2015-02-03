<?php

class AuthController extends \BaseController {

	public function getSignup(){

		return View::make('qa.signup')->with('title','Sign Up');
	}

	public function postSignup(){

		$validation = Validator::make(Input::all(),User::$signup_rules);

		if($validation->passes()){

			$user = Sentry::getUserProvider()->create(array(
				'email' => Input::get('email'),
				'password' => Input::get('password'),
				'first_name' => Input::get('first_name'),
				'last_name' => Input::get('last_name'),
				'activated' => 1
			));

			$login = Sentry::authenticate(array(
				'email' => Input::get('email'),
				'password' => Input::get('password')
			));

			return Redirect::route('index')
			->with('success','Te has registrado y inicia sesion con exito!');
		} else {

			return Redirect::route('signup_form')
			//con Input::except decidimos que al momento de volver a la vista los valores seleccionados
			//no se vuelvan en blanco o volver a completar
			->withInput(Input::except('password','re_password'))
			//devuelve la primera cadena de errores del formulario
			->with('error',$validation->errors()->first()); 
		}
	}

	public function postLogin(){

		$validation = Validator::make(Input::all(),User::$login_rules);

		if($validation->fails()){
			return Redirect::route('index')
			->withInput(Input::except('password'))
			->with('topError',$validation->errors()->first());
		} else {
			try {
				$credentials = array(
					'email' => Input::get('email'),
					'password' => Input::get('password'),
				);

				$user = Sentry::authenticate($credentials,false);
				return Redirect::route('index')->with('success','Usted a ingresado a su cuenta');
			}
			catch(Cartalyst\Sentry\Users\LoginRequiredException $e){
				return Redirect::route('index')->withInput(Input::except('password'))
				->with('topError','Se requiere campo Login');
			}
			catch(Cartalyst\Sentry\Users\PasswordRequiredException $e){
				return Redirect::route('index')->withInput(Input::except('password'))
				->with('topError','Se requiere campo Password');
			}
			catch(Cartalyst\Sentry\Users\WrongPasswordRequiredException $e){
				return Redirect::route('index')->withInput(Input::except('password'))
				->with('topError','Password incorrecto vuelva a intentarlo');
			}
			catch(Cartalyst\Sentry\Users\UserNotFoundException $e){
				return Redirect::route('index')->withInput(Input::except('password'))
				->with('topError','Usuario no fue encontrado');
			}
			catch(Cartalyst\Sentry\Users\UserNotActivatedException $e){
				return Redirect::route('index')->withInput(Input::except('password'))
				->with('topError','El usuario no esta activado');
			}

			catch(Cartalyst\Sentry\Throttling\UserSuspendedException $e){
				return Redirect::route('index')->withInput(Input::except('password'))
				->with('topError','El usuario esta suspendido');
			}
			catch(Cartalyst\Sentry\Throttling\UserBannedException $e){
				return Redirect::route('index')->withInput(Input::except('password'))
				->with('topError','el usuario esta prohibido el ingreso');
			}
		}
	}

	public function getLogout(){

		Sentry::logout();

		return Redirect::route('index')->with('success','Usted ha cerrado sesion con exito.');
	}
}
