<div class="container">
{{-- Top error (about login etc.) --}}
<!-- En la actual plantilla cuenta con dos mensajes de error: -->

<!-- El primero es totalmente reservado para el inicion de session y comprobamos si hay
un error presente -->

	<!-- Aplicacmos el metodo has() de la clase Session -->
@if(Session::has('topError'))
	<div class="centerfix" id="infobar">
		<div class="centercontent">
	<!-- aplicacmos el metodo get() de la clase Session -->
			{{ Session::get('topError') }}
		</div>
	</div>
@endif

<!-- el menu de la plantilla template_masterpage dependera si el usuario esta conectado -->
{{-- Check if a user is logged in, login and logout has different templates --}}

<!-- usamos el metodo de check() de usuarios de Sentry 2 y comprobar si el usuario esta conectado-->
<!-- DATO ADICIONAL: se mostrara la forma de conexion que hemos hecho utilizando una clase form
	otra cosa que va a mostrar la barra de navegacion es el perfil del usuario y el boton cerrar seccion-->
@if(!Sentry::check())
	<div class="centerfix" id="login">
		<div class="centercontent">
			{{Form::open(array('route'=>'login_post'))}}

			{{Form::email('email', Input::old('email'),array('placeholder'=>'E-mail Address'))}}
			{{Form::password('password', array('placeholder' => 'Password'))}}
			{{Form::submit('Log in!')}}

			{{Form::close()}}

			{{HTML::linkRoute('signup_form','Register',array(),array('class'=>'wybutton'))}}
			<!--<button><b>{{link_to_route('signup_form','Register')}}</b></button>-->
		</div>
	</div>
@else
	<div class="centerfix" id="login">
		<div class="centercontent">
			<div id="userblock">Hello again,
				{{HTML::link('#',Sentry::getUser()->first_name.' '.Sentry::getUser()->last_name)}}
			</div>
			{{HTML::linkRoute('logout','Logout',array(),array('class'=>'wybutton'))}}
			{{HTML::linkRoute('ask','Ask a Question',array(),array('class'=>'wybutton'))}}
		</div>
	</div>
@endif
</div>