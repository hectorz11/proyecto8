<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

/*Route::get('/', function()
{
	return View::make('hello');
});*/

//Auth Resource: autentificacion
Route::get('signup', array(	'as' => 'signup_form',
							'before' => 'is_guest',
							'uses' => 'AuthController@getSignup'));

Route::post('signup',array( 'as' => 'signup_form_post',
							'before' => 'csrf|is_guest',
							'uses' => 'AuthController@postSignup'));

Route::post('login', array( 'as' => 'login_post',
							'before' => 'csrf|is_guest',
							'uses' => 'AuthController@postLogin'));

Route::get('logout', array(	'as' => 'logout',
							'before' => 'user',
							'uses' => 'AuthController@getLogout'));
//-------------------------------------------------------------------------------------------------------------
//---- Q & A Resource
Route::get('/',	array( 'as' => 'index', 'uses' => 'MainController@getIndex'));
//-------------------------------------------------------------------------------------------------------------

Route::get('ask', array(	'as' => 'ask',
							'before' => 'user',
							'uses' => 'QuestionsController@getNew'));

Route::post('ask', array(	'as' => 'ask_post',
							'before' => 'user|csrf',
							'uses' => 'QuestionsController@postNew'));

Route::get('question/{id}/{title}',array(
							'as' => 'question_details',
							'uses' => 'QuestionsController@getDetails'))
->where(array('id' => '[0-9]+', 'title' => '[0-9a-zA-Z\-\_]+'));

//Upvoting and Downvoting
Route::get('question/vote/{direction}/{id}',array(
							'as' => 'vote',
							'before' => 'user',
							'uses' => 'QuestionsController@getVote'))
->where(array('direction' => '(up|down)', 'id' => '[0-9]+'));

//Question tag page
Route::get('question/tagged/{tag}',array(
							'as' => 'tagged',
							'uses' => 'QuestionsController@getTaggedWith'))
->where(array('tag','[0-9a-zA-Z\-\]+'));

//Reply Question
Route::post('question/{id}/{title}',array(
							'as' => 'question_reply',
							'before' => 'csrf|user',
							'uses' => 'AnswersController@postReply'))
->where(array('id' => '[0-9]+', 'title' => '[0-9a-zA-Z\-\_]+'));

//Delete Question
Route::get('question/delete/{id}',array(
							'as' => 'delete_question',
							'before' => 'access_check:admin',
							'uses' => 'QuestionsController@getDelete'))
->where('id','[0-9]+');

//Answer upvoting and downvoting
Route::get('answer/vote/{direction}/{id}',array(
							'as' => 'vote_answer',
							'before' => 'user',
							'uses' => 'AnswersController@getVote'))
->where(array('direction' => '(up|down)', 'id' => '[0-9]+'));

Route::get('question/{id}',array(
							'as' => 'choose_answer',
							'uses' => 'AnswersController@getChoose'));
/*
|--------------------------------------------------------------------------------------------------------------
//Para agregar un usuario con el permiso de administrador = 'admin' */
/*Route::get('create_user',function(){

	$user = Sentry::getUserProvider()->create(array(
		'email' => 'hector.zapana.condori@gmail.com',
		'password' => '123456',
		'first_name' => 'Hector',
		'last_name' => 'Zapana',
		'activated' => 1,
		'permissions' => array(
			'admin' => 1
		)
	));
	return 'admin created with id of '.$user->id;
});*/