<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7">
<![endif]-->
<!--[if IE 7]> <html class="no-js lt-ie9 lt-ie8">
<![endif]-->
<!--[if IE 8]> <html class="no-js lt-ie9">
<![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js">
<!--<![endif]-->
<head>
	<meta charset="utf-8">
	<!-- incluira el titulo ('nombre') del sitio web -->
	<title>LARAVEL Q & A</title>
	<!-- style() es un metodo de la clase HTML que nos permite llamar .CSS -->
	{{ HTML::style('assets/css/styles.css') }}
	<!--{{ HTML::style('assets/SnowFlat/qa-styles.css') }}-->
	{{ HTML::style('assets/css/bootstrap.min.css') }}
	{{ HTML::style('assets/css/bootstrap.css') }}
</head>
<body>
	{{-- We include the top menu view here --}}
	<!-- include es un metodo de la plantilla Blade que indica que hemos agregado otra plantilla-->
	@include('template.topmenu')

	<div class="centerfix" id="header">
	<div class="container">
		<div class="centercontent">
			<a href="{{URL::route('index')}}">
	<!-- image es un metodo de la clase HTML que nos permite llamar imagenes -->
				{{HTML::image('assets/img/header/logo.png')}}
			</a>
		</div>
	</div>
	</div>
	<div class="centerfix" id="main" role="main">
		<div class="centercontent clearfix">
			<div id="contentblock">

				{{-- Showing the Error and Success Message --}}

	<!-- El metodo has() de la clase Session nos retorna un valor booleano para identificar is una
	sesion se estabiliza o no -->
				@if(Session::has('error'))
				<div class="warningx wredy">
	<!-- El metodo get() de la clase Session lo utilizamos en nuestras vistas, controladores y otros -->
					{{Session::get('error')}}
				</div>
				@endif

				@if(Session::has('success'))
				<div class="warningx wgreeny">
					{{Session::get('success')}}
				</div>
				@endif

				{{-- Content section of the template --}}
				@yield('content')
			</div>
		</div>
	</div>

	{{-- JavaScript Files --}}
	{{ HTML::script('assets/js/libs.js') }}
	{{ HTML::script('assets/js/plugins.js') }}
	{{ HTML::script('assets/js/script.js') }}
	{{ HTML::script('assets/js/bootstrap.min.js') }}
	{{ HTML::script('assets/js/bootstrap.js') }}
	<script src="https://code.jquery.com/jquery.js"></script>
	{{-- Each page's custom assets (if available) will be yield here --}}
	@yield('footer_assets')

	{{-- if the user is logged in and on index or question details page --}}
	@if(Sentry::check() && (Route::currentRouteName() == 'index' || Route::currentRouteName() == 
	'tagged' || Route::currentRouteName() == 'question_details'))
		<script type="text/javascript">
			$('.questions .arrowbox .btn-success, .questions .arrowbox .btn-danger').click(function(e){
				e.preventDefault();
				var $this = $(this);
				$.get($(this).attr('href'),function($data){
					$this.parent('.arrowbox').next('.btn').find('.cntcount').text($data);
				}).fail(function(){
					alert('An error has been occurred, please try again later');
				});
			});
		</script>
	@endif

</body>
</html>