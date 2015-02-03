<?php

class Question extends Eloquent{

	protected $table = 'questions';

	protected $fillable = array('title','userID','question','viewed','votes');

	public function users(){
		return $this->belongsTo('User','userID');
	}

	public function tags(){
		return $this->belongsToMany('Tag','question_tags')->withTimestamps();
	}

	public function answers(){
		return $this->hasMany('Answer','questionID');
	} 

	public static $add_rules = array(
		'title' => 'required|min:2',
		'question' => 'required|min:10'
	);
}