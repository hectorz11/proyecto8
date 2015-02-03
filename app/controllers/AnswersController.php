<?php

class AnswersController extends \BaseController {

	public function postReply($id,$title){

		$question = Question::find($id);

		if($question) {
			$validation = Validator::make(Input::all(),Answer::$add_rules);

			if($validation->passes()) {
				Answer::create(array(
					'questionID' => $question->id,
					'userID' => Sentry::getUser()->id,
					'answer' => Input::get('answer')
				));

				return Redirect::route('question_details',array($id,$title))
				->with('success','Answer submitted successfully'); 
			} else {
				return Redirect::route('question_details',array($id,$title))
				->withInput()
				->with('error',$validation->errors()->first());
			}
		} else {
			return Redirect::route('index')->with('error','Question not found');
		}
	}

	public function getVote($direction,$id){

		if(Request::ajax()){
			$answer = Answer::find($id);

			if($answer) {
				if($direction == 'up') {
					$newVote = $answer->votes + 1;
				} else {
					$newVote = $answer->votes - 1;
				}

				$update = $answer->update(array(
					'votes' => $newVote
				));

				return $newVote;
			} else {
				Response::make('FAIL', 400);
			}
		} else {
			return Redirect::route('index');
		}
	} 

	public function getChoose($id){

		$answer = Answer::with('questions')->find($id);

		if($answer) {
			if(Sentry::getUser()->hasAccess('admin') || $answer->userID == Sentry::getUser()->id) {
				Answer::where('questionID',$answer->questionID)->update(array(
					'correct' => 0
				));

				$answer->update(array(
					'correct' => 1
				));

				return Redirect::route('question_details',array(
					$answer->questionID, Str::slug($answer->questions->title)))
				->with('success','Best answer chosen successfully');
			} else {
				return Redirect::route('question_details',array(
					$answer->questionID, Str::slug($answer->questions->title)))
				->with('error','You dont have access to this attempt');
			}
		} else {
			return Redirect::route('index')->with('error','Answer not found');
		}
	}
}
