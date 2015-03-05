<?php

require_once(APPPATH."/controllers/course.php");
require_once(APPPATH."/controllers/program.php");
require_once(APPPATH."/controllers/offer.php");
require_once(APPPATH."/controllers/syllabus.php");
require_once(APPPATH."/controllers/usuario.php");
require_once(APPPATH."/controllers/module.php");

function courseTableToSecretaryPage($courses){

	$courseController = new Course();

	echo "<div class=\"box-body table-responsive no-padding\">";
	echo "<table class=\"table table-bordered table-hover\">";
		echo "<tbody>";
		    echo "<tr>";
		        echo "<th class=\"text-center\">Código</th>";
		        echo "<th class=\"text-center\">Curso</th>";
		        echo "<th class=\"text-center\">Tipo</th>";
		        echo "<th class=\"text-center\">Ações</th>";
		    echo "</tr>";

		    	foreach($courses as $courseData){

		    		$courseId = $courseData['id_course'];
		    		$courseType = $courseController->getCourseTypeByCourseId($courseId);

					echo "<tr>";
			    		echo "<td>";
			    		echo $courseId;
			    		echo "</td>";

			    		echo "<td>";
			    		echo $courseData['course_name'];
			    		echo "</td>";

			    		echo "<td>";
			    		echo $courseType['description'];
			    		echo "</td>";

			    		echo "<td>";
			    		echo anchor("enrollStudent/{$courseId}","<i class='fa fa-plus-square'>Matricular Aluno</i>", "class='btn btn-primary'");
			    		echo "</td>";
		    		echo "</tr>";	
		    	}
		    
		echo "</tbody>";
	echo "</table>";
echo "</div>";

}

function displayOfferDisciplineClasses($idDiscipline, $idOffer, $offerDisciplineClasses, $teachers){

	if($offerDisciplineClasses !== FALSE){

		$user = new Usuario();

		foreach($offerDisciplineClasses as $class){
			
			$mainTeacher = $user->getUserById($class['main_teacher']);

			if($class['secondary_teacher'] !== NULL){
				$secondaryTeacher = $user->getUserById($class['secondary_teacher']);
				$secondaryTeacher = $secondaryTeacher['name'];
			}else{
				$secondaryTeacher = "-";
			}

			echo "<div class=\"box-body table-responsive no-padding\">";
			echo "<table class=\"table table-bordered table-hover\">";
				echo "<tbody>";
				    echo "<tr>";
				        echo "<th class=\"text-center\">Turma</th>";
				        echo "<th class=\"text-center\">Vagas totais</th>";
				        echo "<th class=\"text-center\">Vagas atuais</th>";
				        echo "<th class=\"text-center\">Professor principal</th>";
				        echo "<th class=\"text-center\">Professor secundário</th>";
				        echo "<th class=\"text-center\">Ações</th>";
				    echo "</tr>";

				    echo "<tr>";

				    	echo "<td>";
				    	echo $class['class'];
				    	echo "</td>";

				    	echo "<td>";
				    	echo $class['total_vacancies'];
				    	echo "</td>";

				    	echo "<td>";
				    	echo $class['current_vacancies'];
				    	echo "</td>";

				    	echo "<td>";
				    	echo $mainTeacher['name'];
				    	echo "</td>";
				    	
				    	echo "<td>";
				    	echo $secondaryTeacher;
				    	echo "</td>";

				    	echo "<td>";
		    			echo anchor("","Editar turma", "class='btn btn-warning' style='margin-right:5%;'");
		    			echo anchor("offer/deleteDiscipline/{$idOffer}/{$idDiscipline}/{$class['class']}","Remover turma", "class='btn btn-danger'");
				    	echo "</td>";

				    echo "</tr>";
				    
				echo "</tbody>";
			echo "</table>";
			echo "</div>";
		}

		formToNewOfferDisciplineClass($idDiscipline, $idOffer, $teachers);

	}else{
		echo "<div class=\"callout callout-info\">";
			echo "<h4>Nenhuma turma cadastrada no momento.</h4>";
			echo "<p>Cadastre logo abaixo.</p>";
		echo "</div>";

		formToNewOfferDisciplineClass($idDiscipline, $idOffer, $teachers);
	}
}

function formToNewOfferDisciplineClass($idDiscipline, $idOffer, $teachers){

	if($teachers !== FALSE){
		// Nothing to do
	}else{
		$teachers = array('0' => 'Nenhum professor cadastrado.');
	}

	$disciplineClass = array(
		"name" => "disciplineClass",
		"id" => "disciplineClass",
		"type" => "text",
		"class" => "form-campo",
		"class" => "form-control",
		"maxlength" => "3"
	);

	$totalVacancies = array(
		"name" => "totalVacancies",
		"id" => "totalVacancies",
		"type" => "number",
		"class" => "form-campo",
		"class" => "form-control",
		"min" => "0",
		"value" => "0"
	);

	$submitBtn = array(
		"class" => "btn bg-olive btn-block",
		"type" => "sumbit",
		"content" => "Cadastrar turma"
	);

	echo form_open("offer/newOfferDisciplineClass/{$idDiscipline}/{$idOffer}");

		echo "<div class='form-box'>";
			echo"<div class='header'>Nova turma para oferta</div>";
			echo "<div class='body bg-gray'>";

				echo "<div class='form-group'>";
					echo form_label("Turma", "disciplineClass");
					echo form_input($disciplineClass);
					echo form_error("disciplineClass");
				echo "</div>";

				echo "<div class='form-group'>";
					echo form_label("Vagas totais", "totalVacancies");
					echo form_input($totalVacancies);
					echo form_error("disciplineClass");
				echo "</div>";

				echo "<div class='form-group'>";
					echo form_label("Professor principal", "mainTeacher");
					echo form_dropdown("mainTeacher", $teachers);
					echo form_error("mainTeacher");
				echo "</div>";

				echo "<div class='form-group'>";
					echo form_label("Professor secundário", "secondaryTeacher");
					echo form_dropdown("secondaryTeacher", $teachers);
					echo form_error("secondaryTeacher");
				echo "</div>";
		
			echo "</div>";
			
			echo "<div class='footer bg-gray'>";
				echo form_button($submitBtn);
			echo "</div>";
		echo "</div>";

	echo form_close();
}

function displayRegisteredCoursesToProgram($programId, $courses){

	$program = new Program();

	echo "<div class=\"box-body table-responsive no-padding\">";
		echo "<table class=\"table table-bordered table-hover\">";
			echo "<tbody>";

			    echo "<tr>";
			        echo "<th class=\"text-center\">Código do curso</th>";
			        echo "<th class=\"text-center\">Curso</th>";
			        echo "<th class=\"text-center\">Ações</th>";
			    echo "</tr>";

			    if($courses !== FALSE){

				    foreach($courses as $course){
				    	
				    	$courseAlreadyExistsOnProgram = $program->checkIfCourseIsOnProgram($programId, $course['id_course']);
				    	
				    	echo "<tr>";

				    		echo "<td>";
				    			echo $course['id_course'];
				    		echo "</td>";

			    			echo "<td>";
			    				echo $course['course_name'];
			    			echo "</td>";

			    			echo "<td>";
			    				if($courseAlreadyExistsOnProgram){
		    						echo anchor("program/removeCourseFromProgram/{$course['id_course']}/{$programId}","<i class='fa fa-plus'></i> Remover do programa", "class='btn btn-danger'");
			    				}else{
		    						echo anchor("program/addCourseToProgram/{$course['id_course']}/{$programId}","<i class='fa fa-plus'></i> Adicionar ao programa", "class='btn btn-primary'");
			    				}
			    			echo "</td>";

				    	echo "</tr>";
				    }
			    }else{
					echo "<td colspan=2>";
    					echo "<div class=\"callout callout-info\">";
							echo "<h4>Nenhum curso cadastrado.</h4>";
						echo "</div>";
	    			echo "</td>";
			    }
		
			echo "</tbody>";
		echo "</table>";
	echo "</div>";
}

function displayRegisteredPrograms($programs){
	echo "<div class=\"box-body table-responsive no-padding\">";
		echo "<table class=\"table table-bordered table-hover\">";
			echo "<tbody>";

			    echo "<tr>";
			        echo "<th class=\"text-center\"><h3>Programas cadastrados</h3></th>";
			        echo "<th class=\"text-center\"><h3>Ações</h3></th>";
			    echo "</tr>";

			    if($programs !== FALSE){

			    	foreach($programs as $program){
			    		echo "<tr>";

			    			echo "<td>";
			    				echo $program['program_name']." - ".$program['acronym'];
			    			echo "</td>";

			    			echo "<td>";
			    				echo anchor("program/editProgram/{$program['id_program']}", "<span class='glyphicon glyphicon-edit'></span>", "class='btn btn-primary' style='margin-right: 5%' id='edit_program_btn' data-container=\"body\"
		             				data-toggle=\"popover\" data-placement=\"top\" data-trigger=\"hover\"
		             				data-content=\"Aqui é possível editar os dados do programa e adicionar cursos a ele.\"");
			    				
			    				echo anchor("program/removeProgram/{$program['id_program']}", "<span class='glyphicon glyphicon-remove'></span>", "class='btn btn-danger' id='remove_program_btn' data-container=\"body\"
		             				data-toggle=\"popover\" data-placement=\"top\" data-trigger=\"hover\"
		             				data-content=\"OBS.: Ao deletar um programa, todos os cursos associados a ele serão desassociados.\"");
			    			echo "</td>";

			    		echo "</tr>";
			    	}

			    }else{
			    	echo "<td colspan=2>";
    					echo "<div class=\"callout callout-info\">";
							echo "<h4>Não existem programas cadastrados</h4>";
					    	echo anchor("program/registerNewProgram", "Cadastrar Programa", "class='btn btn-primary'");
						echo "</div>";
	    			echo "</td>";	
			    }
		
			echo "</tbody>";
		echo "</table>";
	echo "</div>";	
}

function displayCourseSyllabus($syllabus){
	$course = new Course();
	
	echo "<div class=\"box-body table-responsive no-padding\">";
		echo "<table class=\"table table-bordered table-hover\">";
			echo "<tbody>";

			    echo "<tr>";
			        echo "<th class=\"text-center\">Curso</th>";
			        echo "<th class=\"text-center\">Código Currículo</th>";
			        echo "<th class=\"text-center\">Ações</th>";
			    echo "</tr>";

			    if($syllabus !== FALSE){

				    foreach($syllabus as $courseName => $syllabus){
				    	
				    	$foundCourse = $course->getCourseByName($courseName);
						$courseId = $foundCourse['id_course'];

				    	echo "<tr>";

				    		echo "<td>";
				    			echo $courseName;
				    		echo "</td>";

				    		if($syllabus !== FALSE){

				    			echo "<td>";
				    				echo $syllabus['id_syllabus'];
				    			echo "</td>";

				    			echo "<td>";
				    				echo "<div class=\"callout callout-info\">";
										echo "<h4>Editar</h4>";		    					
				    					echo anchor("syllabus/displayDisciplinesOfSyllabus/{$syllabus['id_syllabus']}/{$courseId}","<i class='fa fa-edit'></i>", "class='btn btn-danger'");
									    echo "<p> <b><i>Aqui é possível adicionar e retirar disciplinas ao currículo do curso.</i><b/></p>";
									echo "</div>";
				    			echo "</td>";

				    		}else{
								echo "<td colspan=2>";
			    					echo "<div class=\"callout callout-info\">";
										echo "<h4>Nenhum currículo cadastrado para esse curso.</h4>";
								    	echo anchor("syllabus/newSyllabus/{$courseId}", "Novo Currículo", "class='btn btn-primary'");
									echo "</div>";
				    			echo "</td>";
				    		}

				    	echo "</tr>";
				    }
			    }else{
					echo "<td colspan=2>";
    					echo "<div class=\"callout callout-info\">";
							echo "<h4>Nenhum curso cadastrado.</h4>";
					    	echo anchor("syllabus/newSyllabus/{$courseId}", "Novo Currículo", "class='btn btn-primary'");
						echo "</div>";
	    			echo "</td>";
			    }
		
			echo "</tbody>";
		echo "</table>";
	echo "</div>";	
}

function displaySyllabusDisciplines($syllabusId, $syllabusDisciplines, $courseId){

	echo "<div class=\"box-body table-responsive no-padding\">";
		echo "<table class=\"table table-bordered table-hover\">";
			echo "<tbody>";

			    echo "<tr>";
			        echo "<th class=\"text-center\">Disciplinas</th>";
			    echo "</tr>";

			    if($syllabusDisciplines !== FALSE){

			    	foreach($syllabusDisciplines as $discipline){
				    	
				    	echo "<tr>";
					    	echo "<td>";
					    		echo $discipline['discipline_code']." - ".$discipline['discipline_name']." (".$discipline['name_abbreviation'].")";
					    	echo "</td>";
				    	echo "</tr>";
			    	}

			    	echo "<tr>";
			    		echo "<td>";
							echo anchor("syllabus/addDisciplines/{$syllabusId}/{$courseId}", "Adicionar disciplinas", "class='btn btn-primary'");
			    		echo "</td>";
			    	echo "</tr>";

			    }else{

			    	echo "<tr>";
			    		echo "<td>";
			    			echo "<div class=\"callout callout-info\">";
								echo "<h4>Nenhuma disciplina adicionada ao currículo.</h4>";
							   	echo anchor("syllabus/addDisciplines/{$syllabusId}/{$courseId}", "Adicionar disciplinas", "class='btn btn-primary'");
							echo "</div>";
			    		echo "</td>";
			    	echo "</tr>";
			    }
			    
			echo "</tbody>";
		echo "</table>";
	echo "</div>";
}

function displayDisciplinesToSyllabus($syllabusId, $allDisciplines, $courseId){

	echo "<h4>Lista de disciplinas:</h4>";
	echo "<div class=\"box-body table-responsive no-padding\">";
		echo "<table class=\"table table-bordered table-hover\">";
			echo "<tbody>";

			    echo "<tr>";
			        echo "<th class=\"text-center\">Código: </th>";
			        echo "<th class=\"text-center\">Sigla</th>";
			        echo "<th class=\"text-center\">Disciplina</th>";
			        echo "<th class=\"text-center\">Créditos</th>";
			        echo "<th class=\"text-center\">Ações</th>";
			    echo "</tr>";

			    if($allDisciplines !== FALSE){

				    foreach($allDisciplines as $discipline){
					    
					    $syllabus = new Syllabus();
			    		$disciplineAlreadyExistsInSyllabus = $syllabus->disciplineExistsInSyllabus($discipline['discipline_code'], $syllabusId);

					    echo "<tr>";
					    	echo "<td>";
				    			echo $discipline['discipline_code'];
					    	echo "</td>";

					    	echo "<td>";
					    		echo $discipline['name_abbreviation'];
					    	echo "</td>";
					    	
					    	echo "<td>";
					    		echo $discipline['discipline_name'];
					    	echo "</td>";
					    	
					    	echo "<td>";
					    		echo $discipline['credits'];
					    	echo "</td>";

					    	echo "<td>";
					    		if($disciplineAlreadyExistsInSyllabus){
					    			echo anchor("syllabus/removeDisciplineFromSyllabus/{$syllabusId}/{$discipline['discipline_code']}/{$courseId}", "Remover disciplina", "class='btn btn-danger'");
					    		}else{
					    			echo anchor("syllabus/addDisciplineToSyllabus/{$syllabusId}/{$discipline['discipline_code']}/{$courseId}", "Adicionar disciplina", "class='btn btn-primary'");
					    		}
					    	echo "</td>";

					    echo "</tr>";
				    }

			    }else{

			    	echo "<tr>";
					    	echo "<td colspan=5>";
						    	echo "<div class=\"callout callout-warning\">";
	                            	echo "<h4>Não há disciplinas cadastradas no momento.</h4>";
	                            echo "</div>";
					    	echo "</td>";
					echo "</tr>";
			    }

			echo "</tbody>";
		echo "</table>";
	echo "</div>";
}

function displayOffersList($offers){

	define("PROPOSED", "proposed");
	define("APPROVED", "approved");

	$course = new Course();
	
	echo "<div class=\"box-body table-responsive no-padding\">";
		echo "<table class=\"table table-bordered table-hover\">";
			echo "<tbody>";

			    echo "<tr>";
			        echo "<th class=\"text-center\">Curso</th>";
			        echo "<th class=\"text-center\">Lista de Oferta</th>";
			        echo "<th class=\"text-center\">Status</th>";
			        echo "<th class=\"text-center\">Ações</th>";
			    echo "</tr>";

			    foreach($offers as $courseName => $offer){
			    	
			    	$foundCourse = $course->getCourseByName($courseName);
					$courseId = $foundCourse['id_course'];

			    	echo "<tr>";

			    		echo "<td>";
			    			echo $courseName;
			    		echo "</td>";

			    		if($offer !== FALSE){

			    			switch($offer['offer_status']){
								case PROPOSED:
									$status = "Proposta";
									break;

								case APPROVED:
									$status = "Aprovada";
									break;

								default:
									$status = "-";
									break;
							}

				    		echo "<td>";
				    			echo $offer['id_offer'];
				    		echo "</td>";
				    		
				    		echo "<td>";
				    			echo $status;
				    		echo "</td>";

				    		echo "<td>";
		    					echo "<div class=\"callout callout-info\">";
				    			if($offer['offer_status'] === PROPOSED){
									echo "<h4>Editar</h4>";
			    					
			    					echo anchor("offer/displayDisciplines/{$offer['id_offer']}/{$courseId}","<i class='fa fa-edit'></i>", "class='btn btn-danger'");
								    echo "<p> <b><i>Aqui é possível adicionar disciplinas a lista de oferta e aprová-la.</i><b/></p>";
				    			}else{
			    					echo anchor("", "<i class='fa fa-edit'></i>", "class='btn btn-danger disabled'");
								    echo "<p> <b><i>Somente as listas de ofertas com status \"proposta\" podem ser alteradas.</i><b/></p>";
				    			}
								echo "</div>";
				    		echo "</td>";

			    		}else{

			    			echo "<td colspan=3>";
		    					echo "<div class=\"callout callout-info\">";
									echo "<h4>Nenhuma lista de ofertas proposta para o semestre atual.</h4>";
							    	echo anchor("offer/newOffer/{$courseId}", "Nova Lista de Ofertas", "class='btn btn-primary'");
								    echo "<p> <b><i>OBS.: A lista de oferta será criada para o semestre atual.</i><b/></p>";
								echo "</div>";
			    			echo "</td>";
			    		}

			    	echo "</tr>";
			    }
		
			echo "</tbody>";
		echo "</table>";
	echo "</div>";
}

function displayOfferDisciplines($idOffer, $course, $disciplines){

	echo "<h3>Lista de Oferta</h3>";
	echo "<h3><b>Curso</b>: ".$course['course_name']."</h3>";

	echo "<div class=\"box-body table-responsive no-padding\">";
		echo "<table class=\"table table-bordered table-hover\">";
			echo "<tbody>";

			    echo "<tr>";
			        echo "<th class=\"text-center\">Código da Lista: ".$idOffer."</th>";
			        echo "<th class=\"text-center\">Status: Proposta</th>";
			    echo "</tr>";

			    echo "<tr>";
			    	echo "<td colspan=2>";
			    	echo "<b>Disciplinas</b>";
			    	echo "</td>";
			    echo "</tr>";

			    if($disciplines !== FALSE){

				    foreach($disciplines as $discipline){
					    
					    echo "<tr>";
					    	echo "<td colspan=2>";
				    		echo $discipline['discipline_code']." - ".$discipline['discipline_name']."(".$discipline['name_abbreviation'].")";
					    	echo "</td>";
					    echo "</tr>";
				    }

				    echo "<tr>";
						echo "<td colspan=2>";
		                echo anchor("offer/addDisciplines/{$idOffer}/{$course['id_course']}",'Adicionar disciplinas', "class='btn btn-primary'");
		                echo "</td>";
				    echo "</tr>";
			    }else{

			    	echo "<tr>";
					    	echo "<td colspan=2>";
						    	echo "<div class=\"callout callout-info\">";
	                            	echo "<h4>Nenhuma disciplina adicionada a essa lista de oferta no momento.</h4>";

	                            	echo anchor("offer/addDisciplines/{$idOffer}/{$course['id_course']}",'Adicionar disciplinas', "class='btn btn-primary'");
	                            echo "</div>";
					    	echo "</td>";
					echo "</tr>";
			    }

			echo "</tbody>";
		echo "</table>";
	echo "</div>";

	echo "<div class=\"row\">";
		echo "<div class=\"col-xs-3\">";
			if($disciplines !== FALSE){

				echo anchor("offer/approveOfferList/{$idOffer}", "Aprovar lista de oferta", "id='approve_offer_list_btn' class='btn btn-primary' data-container=\"body\"
		             data-toggle=\"popover\" data-placement=\"top\" data-trigger=\"hover\"
		             data-content=\"OBS.: Ao aprovar a lista de oferta não é possível adicionar ou retirar disciplinas.\"");
			}else{
				echo anchor("", "Aprovar lista de oferta", "id='approve_offer_list_btn' class='btn btn-primary' data-container=\"body\"
		             data-toggle=\"popover\" data-placement=\"top\" data-trigger=\"hover\" disabled='true'
		             data-content=\"Não é possível aprovar uma lista sem disciplinas.\"");
			}
		echo "</div>";
		echo "<div class=\"col-xs-3\">";
			echo anchor("usuario/secretary_offerList", "Voltar", "class='btn btn-danger'");
		echo "</div>";
	echo "</div>";
}

function displayRegisteredDisciplines($allDisciplines, $course, $idOffer){

	echo "<div class=\"box-body table-responsive no-padding\">";
		echo "<table class=\"table table-bordered table-hover\">";
			echo "<tbody>";

			    echo "<tr>";
			        echo "<th class=\"text-center\">Código: </th>";
			        echo "<th class=\"text-center\">Sigla</th>";
			        echo "<th class=\"text-center\">Disciplina</th>";
			        echo "<th class=\"text-center\">Créditos</th>";
			        echo "<th class=\"text-center\">Ações</th>";
			    echo "</tr>";

			    if($allDisciplines !== FALSE){

				    foreach($allDisciplines as $discipline){
					    
					    echo "<tr>";
					    	echo "<td>";
				    			echo $discipline['discipline_code'];
					    	echo "</td>";

					    	echo "<td>";
					    		echo $discipline['name_abbreviation'];
					    	echo "</td>";
					    	
					    	echo "<td>";
					    		echo $discipline['discipline_name'];
					    	echo "</td>";
					    	
					    	echo "<td>";
					    		echo $discipline['credits'];
					    	echo "</td>";

					    	echo "<td>";
					    		// if($disciplineAlreadyExistsInOffer){					    			
					    		// 	echo anchor("offer/removeDisciplineFromOffer/{$discipline['discipline_code']}/{$idOffer}/{$course['id_course']}", "Remover disciplina da lista", "class='btn btn-danger'");
					    		// }else{
				    			// 	echo anchor("offer/addDisciplineToOffer/{$discipline['discipline_code']}/{$idOffer}/{$course['id_course']}", "Adicionar à lista de oferta de ".$course['course_name'], "class='btn btn-primary'");
					    		// }
								echo anchor("offer/displayDisciplineClasses/{$discipline['discipline_code']}/{$idOffer}", "<i class='fa fa-tasks'></i> Gerenciar turmas para a oferta", "class='btn btn-primary'");
					    	echo "</td>";

					    echo "</tr>";
				    }

			    }else{

			    	echo "<tr>";
				    	echo "<td colspan=5>";
					    	echo "<div class=\"callout callout-warning\">";
                            	echo "<h4>Não há disciplinas cadastradas no currículo deste curso no momento.</h4>";
                            echo "</div>";
				    	echo "</td>";
					echo "</tr>";
			    }

			echo "</tbody>";
		echo "</table>";
	echo "</div>";
}

function displayRegisteredStudents($students, $studentNameToSearch){

	$thereIsStudents = sizeof($students) > 0;

	if($thereIsStudents){

		$enrollStudentBtn = array(
			"id" => "enroll_student_btn",
			"class" => "btn bg-olive btn-block",
			"content" => "Matricular aluno",
			"type" => "submit",
			"style" => "width:35%"
		);
	
		echo form_label("Usuários encontrados:","user_to_enroll");
		echo "<h4><small>OBS.: Usuários pertencentes ao grupo convidado apenas.</small></h4>";
		echo form_dropdown('user_to_enroll', $students, "", "id = user_to_enroll class='form-control'");

		echo "<br>";
		echo form_button($enrollStudentBtn);
		
	}else{
		echo "<div class=\"callout callout-info\">";
			echo "<h4>Nenhum aluno encontrado com a chave '".$studentNameToSearch."'.<br><small>OBS.: Usuários pertencentes ao grupo convidado apenas.</small></h4>";
		echo "</div>";
	}
}

function displayRegisteredUsers($allUsers){
	
	echo "<h3>Lista de Usuários:</h3>";
	echo "<br>";

	echo "<div class=\"box-body table-responsive no-padding\">";
		echo "<table class=\"table table-bordered table-hover\">";
			echo "<tbody>";

			    echo "<tr>";
			        echo "<th class=\"text-center\">Código</th>";
			        echo "<th class=\"text-center\">Nome</th>";
			        echo "<th class=\"text-center\">CPF</th>";
			        echo "<th class=\"text-center\">E-mail</th>";
			        echo "<th class=\"text-center\">Ações</th>";
			    echo "</tr>";

			    if($allUsers !== FALSE){

				    foreach($allUsers as $user){
				    	
				    	echo "<tr>";

					    	echo "<td>";
					    		echo $user['id'];
					    	echo "</td>";

					    	echo "<td>";
					    		echo $user['name'];
					    	echo "</td>";

					    	echo "<td>";
					    		echo $user['cpf'];
					    	echo "</td>";

					    	echo "<td>";
					    	 	echo $user['email'];
					    	echo "</td>";

					    	echo "<td>";
					    		echo anchor("usuario/manageGroups/{$user['id']}", "<i class='fa fa-group'></i> Gerenciar Grupos", "class='btn btn-primary'");
					    	echo "</td>";

				    	echo "</tr>";
				    }

			    }else{

			    	echo "<tr>";
					    	echo "<td colspan=5>";
						    	echo "<div class=\"callout callout-warning\">";
	                            	echo "<h4>Não há usuários cadastradas no momento.</h4>";
	                            echo "</div>";
					    	echo "</td>";
					echo "</tr>";
			    }

			echo "</tbody>";
		echo "</table>";
	echo "</div>";
}

function displayUserGroups($idUser, $userGroups){
	
	$user = new Usuario();
	$foundUser = $user->getUserById($idUser);
	echo "<h3>Grupos pertencentes a <b>".$foundUser['name']."</b>:</h3>";
	echo "<br>";

	echo "<div class=\"box-body table-responsive no-padding\">";
		echo "<table class=\"table table-bordered table-hover\">";
			echo "<tbody>";

			    echo "<tr>";
			        echo "<th class=\"text-center\">Grupo</th>";
			        echo "<th class=\"text-center\">Ações</th>";
			    echo "</tr>";

			    if($userGroups !== FALSE){

				    foreach($userGroups as $group){
				    	
				    	echo "<tr>";

					    	echo "<td>";
					    		echo $group['group_name'];
					    	echo "</td>";

					    	echo "<td>";
					    		echo anchor("usuario/removeUserGroup/{$idUser}/{$group['id_group']}", "Remover Grupo", "class='btn btn-danger'");
					    	echo "</td>";

				    	echo "</tr>";
				    }

			    }else{
			    	echo "<tr>";
				    	echo "<td colspan=2>";
					    	echo "<div class=\"callout callout-warning\">";
                            	echo "<h4>Não há grupos cadastrados para esse usuário.</h4>";
                            echo "</div>";
				    	echo "</td>";
					echo "</tr>";
			    }

			echo "</tbody>";
		echo "</table>";
	echo "</div>";
}

function displayAllGroupsToUser($idUser, $allGroups, $userGroups){

	$user = new Usuario();
	$foundUser = $user->getUserById($idUser);

	echo "<h3>Grupos Existentes:</h3>";
	echo "<br>";

	echo "<div class=\"box-body table-responsive no-padding\">";
		echo "<table class=\"table table-bordered table-hover\">";
			echo "<tbody>";

			    echo "<tr>";
			        echo "<th class=\"text-center\">Grupo</th>";
			        echo "<th class=\"text-center\">Ações</th>";
			    echo "</tr>";

			    if($allGroups !== FALSE){

				    foreach($allGroups as $idGroup => $groupName){
				    	
				    	$alreadyHaveThisGroup = FALSE;
				    	if($userGroups !== FALSE){

					    	foreach($userGroups as $group){
					    		if($idGroup == $group['id_group']){
				    				$alreadyHaveThisGroup = TRUE;
				    				break;
					    		}
					    	}
				    	}else{
				    		$alreadyHaveThisGroup = FALSE;
				    	}

				    	echo "<tr>";

					    	echo "<td>";
					    		echo $groupName;
					    	echo "</td>";

					    	echo "<td>";
					    		if($alreadyHaveThisGroup){
				    				echo anchor("", "<i class='fa fa-plus'></i> <i class='fa fa-user'></i> <b>".$foundUser['name']."</b>", "class='btn btn-primary disabled'");
					    		}else{
				    				echo anchor("usuario/addGroupToUser/{$idUser}/{$idGroup}", "<i class='fa fa-plus'></i> <i class='fa fa-user'></i> <b>".$foundUser['name']."</b>", "class='btn btn-primary'");
					    		}
					    	echo "</td>";

				    	echo "</tr>";
				    }

			    }else{

			    	echo "<tr>";
					    	echo "<td colspan=2>";
						    	echo "<div class=\"callout callout-warning\">";
	                            	echo "<h4>Não há grupos cadastrados no sistema no momento.</h4>";
	                            echo "</div>";
					    	echo "</td>";
					echo "</tr>";
			    }

			echo "</tbody>";
		echo "</table>";
	echo "</div>";
}

function displayRegisteredGroups($allGroups){
	echo "<h3>Grupos Cadastrados:</h3>";
	echo "<br>";

	echo "<div class=\"box-body table-responsive no-padding\">";
		echo "<table class=\"table table-bordered table-hover\">";
			echo "<tbody>";

			    echo "<tr>";
			        echo "<th class=\"text-center\">Grupo</th>";
			        echo "<th class=\"text-center\">Ações</th>";
			    echo "</tr>";

			    if($allGroups !== FALSE){

				    foreach($allGroups as $idGroup => $groupName){

				    	echo "<tr>";

					    	echo "<td>";
					    		echo $groupName;
					    	echo "</td>";

					    	echo "<td>";
					    		echo anchor("usuario/listUsersOfGroup/{$idGroup}", "<i class='fa fa-list-ol'></i> Listar usuários", "class='btn btn-primary' style='margin-right:5%;'");
					    		echo anchor("usuario/removeAllUsersOfGroup/{$idGroup}", "<i class='fa fa-eraser'></i> Remover todos usuários do grupo", "class='btn btn-danger'");
					    	echo "</td>";

				    	echo "</tr>";
				    }

			    }else{

			    	echo "<tr>";
					    	echo "<td colspan=2>";
						    	echo "<div class=\"callout callout-warning\">";
	                            	echo "<h4>Não há grupos cadastrados no sistema no momento.</h4>";
	                            echo "</div>";
					    	echo "</td>";
					echo "</tr>";
			    }

			echo "</tbody>";
		echo "</table>";
	echo "</div>";
}

function displayUsersOfGroup($idGroup, $usersOfGroup){
	
	$group = new Module();
	$foundGroup = $group->getGroupById($idGroup);
	echo "<h3>Usuários do grupo <b>".$foundGroup['group_name']."</b>:</h3>";
	echo "<br>";

	echo "<div class=\"box-body table-responsive no-padding\">";
		echo "<table class=\"table table-bordered table-hover\">";
			echo "<tbody>";

			    echo "<tr>";
			        echo "<th class=\"text-center\">Código</th>";
			        echo "<th class=\"text-center\">Nome</th>";
			        echo "<th class=\"text-center\">CPF</th>";
			        echo "<th class=\"text-center\">E-mail</th>";
			        echo "<th class=\"text-center\">Ações</th>";
			    echo "</tr>";

			    if($usersOfGroup !== FALSE){

				    foreach($usersOfGroup as $user){

				    	echo "<tr>";

					    	echo "<td>";
					    		echo $user['id'];
					    	echo "</td>";

					    	echo "<td>";
					    		echo $user['name'];
					    	echo "</td>";

					    	echo "<td>";
					    		echo $user['cpf'];
					    	echo "</td>";

					    	echo "<td>";
					    	 	echo $user['email'];
					    	echo "</td>";

					    	echo "<td>";
					    		echo anchor("usuario/removeUserFromGroup/{$user['id']}/{$idGroup}", "<i class='fa fa-eraser'></i> Remover Usuário", "class='btn btn-danger'");
					    	echo "</td>";

				    	echo "</tr>";
				    }

			    }else{

			    	echo "<tr>";
					    	echo "<td colspan=5>";
						    	echo "<div class=\"callout callout-warning\">";
	                            	echo "<h4>Não há usuários cadastrados nesse grupo no momento.</h4>";
	                            echo "</div>";
					    	echo "</td>";
					echo "</tr>";
			    }

			echo "</tbody>";
		echo "</table>";
	echo "</div>";
}