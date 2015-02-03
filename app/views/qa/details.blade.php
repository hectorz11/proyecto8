<!-- primera parte -->
@extends('template_masterpage')

@section('content')
<div class="container">
<h1 id="replyh">{{$question->title}}</h1>

<div class="qwrap questions">
<div id="rcount">Viewed {{$question->viewed}} time{{$question->viewed > 0?'s':''}}.</div>

@if(Sentry::check())
<div class="arrowbox">
	{{HTML::linkRoute('vote','',array('up',$question->id),array(
		'class'=>'btn btn-success','title'=>'Upvote'))}}
	{{HTML::linkRoute('vote','',array('down',$question->id),array(
		'class'=>'btn btn-danger','title'=>'Downvote'))}}
</div>
@endif

{{-- clase sera diferente a la situacion --}}
@if($question->votes > 0)
<div class="btn btn-success">
@elseif($question->votes == 0)
<div class="btn btn-default">
@else
<div class="btn btn-danger">
@endif

<div class="cntcount">{{$question->votes}}</div>
	<div class="cnttext">vote</div>
</div>

<!-- segunda parte  -->
<div class="rblock">
	<div class="rbox">
		<p>{{nl2br($question->question)}}</p>
	</div>
	<div class="qinfo">Asked by 
		<a href="#">
			{{$question->users->first_name.' '.$question->users->last_name}}
		</a> 
		around {{date('m/d/Y H:i:s',strtotime($question->created_at))}}
	</div>

	{{-- Si la pregunta tiene 'tags', mostrarlos --}}
	@if($question->tags!=null)
	<ul class="qtagul">
	@foreach($question->tags as $tag)
		<li>{{HTML::linkRoute('tagged',$tag->tag,$tag->tagFriendly)}}</li>
	@endforeach
	</ul>
	@endif

<!-- tercera parte -->
	{{-- if the user/admin is logged in, we will have a buttons section --}}
	@if(Sentry::check())
	<div class="qwrap">
		<ul class="fastbar">
		@if(Sentry::getUser()->hasAccess('admin'))
			<li class="btn btn-danger">{{HTML::linkRoute('delete_question','delete',$question->id)}}</li>
		@endif
			<li class="answer"><a href="#">answer</a></li>
		</ul>
	</div>
	@endif
</div>
<div id="rreplycount">{{count($question->answers)}} answers</div>

<!-- cuarta parte -->
{{-- si se trata del usuario, tambien tendremos el bloque de respuesta dentro de nuestra vista --}}
@if(Sentry::check())
<div class="rrepol" id="replyarea" style="margin-bottom:10px">
	{{Form::open(array('route'=>array('question_reply',$question->id,Str::slug($question->title))))}}
	<p class="minihead">Provide your Answer:</p>
	{{Form::textarea('answer',Input::old('answer'),array('class'=>'fullinput'))}}
	{{Form::submit('Answer the Question!')}}
	{{Form::close()}}
</div>
@endif
</div>

<!-- sexta parte -->
@if(count($question->answers))
	@foreach($question->answers as $answer)
		@if($answer->correct == 1)
		<div class="rrepol correct">
		@else
		<div class="rrepol">
		@endif

		@if(Sentry::check())
		<div class="arrowbox">
			{{HTML::linkRoute('vote_answer', '', array('up', $answer->id), array(
			'class' => 'btn btn-success', 'title' => 'Upvote'))}}
			{{HTML::linkRoute('vote_answer', '', array('down', $answer->id), array(
			'class' => 'btn btn-danger', 'title' => 'Downvote'))}}
		</div>
		@endif

		<div class="btn btn-default">
			<div class="cntcount">{{$answer->votes}}</div>
			<div class="cnttext">vote</div>
		</div>

		@if($answer->correct == 1)
		<div class="bestanswer">best answer</div>
		@else
			@if(Sentry::check())
				@if(Sentry::getUser()->hasAccess('admin') || Sentry::getUser()->id == $question->userID)
				<a href="{{URL::route('choose_answer',$answer->id)}}" class="chooseme">
					<div class="choosebestanswer">
						choose
					</div>
				</a>
				@endif
			@endif
		@endif

		<div class="rblock">
			<div class="rbox">
				<p>{{nl2br($answer->answer)}}</p>
			</div>
			<div class="rrepolinf">
				<p>Answered by 
				<a href="#">{{$answer->users->first_name.' '.$answer->users->last_name}} </a>
				 around {{date('m/d/Y H:i:s', strtotime($answer->created_at))}}
				</p>	
			</div>
		</div>
		</div>
	@endforeach
@endif

@stop

@section('footer_assets')
<!-- quinta parte -->
{{-- Si el usuario quiere ocultar el area de respuesta --}}
@if(Sentry::check())
<script type="text/javascript">
	var $replyarea = $('div#replyarea');
	$replyarea.hide();
	$('li.answer a').click(function(e){
		e.preventDefault();
		if($replyarea.is(':hidden')) {
			$replyarea.fadeIn('fast');
		} else {
			$replyarea.fadeOut('fast');
		}
	});
</script>
@endif

{{-- Si el administrador a iniciado sesion, una confirmacion para borrar el intento --}}
@if(Sentry::check())
	@if(Sentry::getUser()->hasAccess('admin'))
	<script type="text/javascript">
	$('li.close a').click(function(){
		return confirm('Are you sure you want to delete this? There is no turning back!');
	});
	</script>
	@endif
@endif

<!-- septima parte -->
{{-- for admins and question owners --}}
@if(Sentry::check())
	@if(Sentry::getUser()->hasAccess('admin') || Sentry::getUser()->id == $question->userID)
	<script type="text/javascript">
		$('a.chooseme').click(function()){
			return confirm ('Are you sure you want to choose this answer as best answer?');
		}
	</script>
	@endif
@endif

@stop