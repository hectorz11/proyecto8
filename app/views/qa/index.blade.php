@extends('template_masterpage')

@section('content')

<div class="container">
<h1>{{$title}}</h1>

@if(count($questions))
	@foreach($questions as $question)
		<?php
			//Question's asker and tags info
			$asker = $question->users;
			$tags = $question->tags;	
		?>
		<div class="qwrap questions">

		{{-- Guests cannot see the vote arrows --}}
		@if(Sentry::check())
			<div class="arrowbox">
			{{HTML::linkRoute('vote','',array('up',$question->id),array(
				'class'=>'btn btn-success','title'=>'Upvote'))}}
			{{HTML::linkRoute('vote','',array('down',$question->id),array(
				'class'=>'btn btn-danger','title'=>'Downvote'))}}
			</div>
		@endif

		{{-- class will differ on the situation --}}
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
		
		<?php 
			$answers = $question->answers;
			$accepted = false;

			if ($question->answers != null) {
				foreach ($answers as $answer) {
					if ($answer->correct == 1) {
						$accepted = true;
						break;
					}
				}
			}
		?>
		@if($accepted)
		<div class="btn btn-success">
		@else
		<div class="btn btn-danger">
		@endif
		<div class="cntcount">{{count($answers)}}</div>
		<div class="cnttext">answer</div>
		</div>

		<div class="qtext">
		<div class="qhead">
		{{HTML::linkRoute('question_details',$question->title,array(
			$question->id,Str::slug($question->title)))}}
		</div>
		<div class="qinfo">Asked by 
		<a href="#">
			{{$asker->first_name.' '.$asker->last_name}}
		</a> around {{date('m/d/Y H:i:s',strtotime($question->created_at))}}</div>
		@if($tags!=null)
		<ul class="qtagul">
			@foreach($tags as $tag)
				<li>{{HTML::linkRoute('tagged',$tag->tag,$tag->tagFriendly)}}</li>
			@endforeach
		</ul>
		@endif
		</div>
		</div>
	@endforeach

	{{-- and lastly, the pagination --}} 
	{{$questions->links()}}
@else
	No questions found. {{HTML::linkRoute('ask','Ask a question?')}}
@endif

@stop

