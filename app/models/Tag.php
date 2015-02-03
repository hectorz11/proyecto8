<?php

class Tag extends Eloquent{

	protected $table = 'tags';

	protected $fillable = array('tag','tagFriendly');

	public function questions(){
		return $this->belongsToMany('Question','question_tags')->withTimestamps();
	}
}