<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH."/controllers/course.php");
require_once(APPPATH."/controllers/discipline.php");

class Syllabus_model extends CI_Model {

	public function getCourseSyllabus($courseId){

		$foundSyllabus = $this->getSyllabusByCourseId($courseId);

		return $foundSyllabus;
	}

	public function newSyllabus($courseId){

		$course = new Course();

		$courseExists = $course->checkIfCourseExists($courseId);

		if($courseExists){

			$syllabus = array(
				'id_course' => $courseId
			);
			$this->saveNewSyllabus($syllabus);

			$foundSyllabus = $this->getSyllabusByCourseId($courseId);

			if($foundSyllabus !== FALSE){
				$wasSaved = TRUE;
			}else{
				$wasSaved = FALSE;
			}


		}else{
			$wasSaved = FALSE;
		}

		return $wasSaved;
	}

	public function getSyllabusDisciplines($syllabusId){

		$this->db->select('discipline.*');
		$this->db->from('discipline');
		$this->db->join('syllabus_discipline', "syllabus_discipline.id_discipline = discipline.discipline_code");
		$this->db->where('syllabus_discipline.id_syllabus', $syllabusId);
		$foundDisciplines = $this->db->get()->result_array();

		if(sizeof($foundDisciplines) > 0){
			// Nothing to do
		}else{
			$foundDisciplines = FALSE;
		}

		return $foundDisciplines;
	}

	public function disciplineExistsInSyllabus($disciplineId, $syllabusId){

		$searchResult = $this->db->get_where('syllabus_discipline', array('id_syllabus' => $syllabusId, 'id_discipline' => $disciplineId));

		$foundSyllabusDiscipline = $searchResult->row_array();

		$disciplineExists = sizeof($foundSyllabusDiscipline) > 0;

		return $disciplineExists;
	}

	public function addDisciplineToSyllabus($syllabusId, $disciplineId){

		$discipline = new Discipline();
		$disciplineExists = $discipline->checkIfDisciplineExists($disciplineId);

		$syllabusExists = $this->checkIfSyllabusExists($syllabusId);

		$dataIsOk = $disciplineExists && $syllabusExists;

		if($dataIsOk){

			$syllabusDiscipline = array(
				'id_syllabus' => $syllabusId,
				'id_discipline' => $disciplineId
			);
			$this->db->insert('syllabus_discipline', $syllabusDiscipline);

			$foundSyllabusDiscipline = $this->getSyllabusDiscipline($syllabusId, $disciplineId);

			if($foundSyllabusDiscipline !== FALSE){
				$wasSaved = TRUE;
			}else{
				$wasSaved = FALSE;
			}

		}else{
			$wasSaved = FALSE;
		}

		return $wasSaved;
	}

	public function removeDisciplineFromSyllabus($syllabusId, $disciplineId){

		$discipline = new Discipline();
		$disciplineExists = $discipline->checkIfDisciplineExists($disciplineId);

		$syllabusExists = $this->checkIfSyllabusExists($syllabusId);

		$dataIsOk = $disciplineExists && $syllabusExists;

		if($dataIsOk){

			$syllabusDiscipline = array(
				'id_syllabus' => $syllabusId,
				'id_discipline' => $disciplineId
			);
			$this->db->delete('syllabus_discipline', $syllabusDiscipline);

			$foundSyllabusDiscipline = $this->getSyllabusDiscipline($syllabusId, $disciplineId);

			if($foundSyllabusDiscipline !== FALSE){
				$wasDeleted = FALSE;
			}else{
				$wasDeleted = TRUE;
			}

		}else{
			$wasDeleted = FALSE;
		}

		return $wasDeleted;
	}

	public function checkIfSyllabusExists($syllabusId){

		$searchResult = $this->db->get_where('course_syllabus', array('id_syllabus' => $syllabusId));
		$foundSyllabus = $searchResult->row_array();

		$syllabusExists = sizeof($foundSyllabus) > 0;

		return $syllabusExists;
	}

	private function getSyllabusDiscipline($syllabusId, $disciplineId){
		
		$searchResult = $this->db->get_where('syllabus_discipline', array('id_syllabus'=> $syllabusId, 'id_discipline' => $disciplineId));
		
		$foundSyllabusDiscipline = $searchResult->row_array();

		if(sizeof($foundSyllabusDiscipline) > 0){
			// Nothing to do
		}else{
			$foundSyllabusDiscipline = FALSE;
		}

		return $foundSyllabusDiscipline;
	}

	private function getSyllabusByCourseId($courseId){

		$searchResult = $this->db->get_where('course_syllabus', array('id_course' => $courseId));
		$foundSyllabus = $searchResult->row_array();

		if(sizeof($foundSyllabus) > 0){
			// Nothing to do
		}else{
			$foundSyllabus = FALSE;
		}

		return $foundSyllabus;
	}

	private function saveNewSyllabus($syllabus){

		$this->db->insert('course_syllabus', $syllabus);
	}

}