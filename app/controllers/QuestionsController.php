<?php

class QuestionsController extends \BaseController {

	public function getNew(){

		return View::make('qa.ask')->with('title','Question');
	}

	public function postNew(){

		//primero validamos el formulario
		$validation = Validator::make(Input::all(),Question::$add_rules);

		if($validation->passes()){
			//primero, creamos una 'question'
			$create = Question::create(array(
	//para obtener la ID del usuario actual, se utilizara el ID del objeto del metodo getUser()
	// por Sentry 2 que devuelve la informacion del usuario conectado.
				'userID' => Sentry::getUser()->id,
				'title' => Input::get('title'),
				'question' => Input::get('question')
			));

			//tenemos la id insertada de la 'questions'
			$insert_id = $create->id;

			//ahora tenemos que volver a encontrar la 'question' "attach" el 'tag' de la 'question'
			$question = Question::find($insert_id);

			//hay que comprobar si la etique 'tag' esta vacia, y dividir la cadena y agregar una
			//nueva 'tag' y una relacion
	//despues de crear la pregunta marcaremos la longitud del campo de 'tag'. si el campo no esta vacio, nos
	//dividiremos la cadena en las comas y hacemos una matriz de 'tag'.
			if(Str::length(Input::get('tags'))){
				//vamos aexplorar todas las 'tags' de la coma
				$tags_array = explode(',', Input::get('tags'));
				//si hay 'tags', comprobaremos si son nuevas, de ser asi, los agregaremos a la base de datos
				//despues de comprobar los 'tags', tendremos que "adjuntar" 'tags(s)' a la nueva pregunta 
				if(count($tags_array)){
					foreach($tags_array as $tag){
						//primero, vamos a recortar y deshacerse de los espacios adicionales entre comas
						//(tag1, tag2 vs tag1,tag2)
						$tag = trim($tag);
						//debemos duplicar la comprobacion de longitud, ya que el usuario puede haber acabado
						//de escribir ",tag1,tag2" (dos o mas comas) accidentalmente.
						//comprobamos la version slugging de 'tag', porque los caracteres siguientes pueden no
						//tener sentido, como "tag1,+++//,tag2"
	//despues de eso el ciclo que recorre cada 'tag' que se habia separado, y hacer su version URL amigable
	//utilizando el metodo slug() de la clase String, si la version de slugging tiene una longitud, es una
	//'tag' valida.
						if(Str::length(Str::slug($tag))){
							//la version URL-friendly de 'tag'
							$tag_friendly = Str::slug($tag);
							//ahora vamos a ver si hay una 'tag' con la version amigable de la URL prevista ya
							//en nuestra base de datos.
							$tag_check = Tag::where('tagFriendly',$tag_friendly);
							//si 'tag' es una nueva 'tag', luego vamoa crear uno nuevo
	//despues de encontrar todas 'tag' validas, comprobaremos en la base de datos si ya una 'tag' ya creada
	//si es asi obtenemos su ID, si la 'tag' es nueva en el sistema, entonces se crea una nueva 'tag'.
	//asi de esta manera evitamos varias etiquetas innecesarias en nuestro sistema
							if($tag_check->count() == 0){
								$tag_info = Tag::create(array(
									'tag' => $tag,
									'tagFriendly' => $tag_friendly
								));
							//si 'tag' no es nueva. eso significa que uno previamente agregado con el mismo
							//nombre, a otra pregunta previamente
							////todavia tenemos que conseguir informacion de ese 'tag' de nuestra bae de datos
							} else {
								$tag_info = $tag_check->first();
							}
						}
						//ahora la "adjuntacion" de 'tag' actual a la pregunta
	//despues de esto utilizaremos el metodo attach(), para crear una nueva etiqueta en la tabla dinamica,
	//para adjuntar una nueva relacion, tenemos que encontrar el ID que queremos adjuntar, y luego vamos al
	//modelo de datos adjuntos  y utilizar el metodo attach() 
						$question->tags()->attach($tag_info->id);
					}
				}
			}
			//por ultimo, hay que devolver al usuario a la pagina de venta con un link permanente de 'question'
			return Redirect::route('ask')
			->with('success','Su pregunta ha sido creada con exito'.HTML::linkRoute(
				'question_details',
				'Click aqui para ver tu pregunta',
				array('id' => $insert_id, 'title' => Str::slug($question->title))
			));
		} else {
			return Redirect::route('ask')->withInput()->with($validation->errors()->first());
		}
	}

	public function getVote($direction,$id){

		if(Request::ajax()) {

			$question = Question::find($id);

			if($question) {
				if($direction == 'up') {
					$newVote = $question->votes + 1;
				} else {
					$newVote = $question->votes - 1;
				}
				$update = $question->update(array(
					'votes' => $newVote
				));
				return $newVote;
			} else {
				Response::make("FAIL",400);
			}
		} else {
			return Redirect::route('index');
		}
	}

	public function getDetails($id,$title){

		$question = Question::with('users','tags','answers')->find($id);

		if($question){
			$question->update(array(
				'viewed' => $question->viewed + 1
			));

			return View::make('qa.details')->with('title',$question->title)->with('question',$question);
		} else {
			return Redirect::route('index')->with('error','Pregunta no encontrada');
		}
	}

	public function getDelete($id){

		$question = Question::find($id);

		if($question) {
			Question::delete();

			return Redirect::route('index')->with('success','La pregunta ha sido eliminada con exito!');
		} else {
			return Redirect::route('index')->with('error','No se ha podido eliminar!');
		}
	}

	public function getTaggedWith($tag){

		$tag = Tag::where('tagFriendly',$tag)->first();

		if($tag) {
			return View::make('qa.index')
			->with('title','Questions Tagged with: '.$tag->tag)
			->with('questions',$tag->questions()->with('users','tags','answers')->paginate(2));
		} else {
			return Redirect::route('index')->with('error','Tag not found');
		}
	}
}