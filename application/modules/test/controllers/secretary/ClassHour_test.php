<?php

/**
 * CourseHour class test class.
 * Provide unit tests for the CourseHour class methods.
 * Remember to call your test methods in the index method to run them in the test report
 * To access the report generated by these tests, type on the URL: '../classHour_test'
 */

require_once(MODULESPATH."/test/controllers/TestCase.php");

require_once(MODULESPATH."/secretary/domain/ClassHour.php");
require_once(MODULESPATH."/secretary/exception/ClassHourException.php");

class ClassHour_Test extends TestCase{

	public function __construct(){
        parent::__construct($this);
	}

/*Valid entries test set*/

	public function shouldInstantiateWithParams_1_1_String(){

		try{
			$classHour = new ClassHour(1, 1, "Sala 1");
		}catch (Exception $caughtException){
			$classHour = FALSE;
		}

		$test_name = "Test the class constructor with valid arguments";

		$this->unit->run($classHour, 'is_object', $test_name);
	}

// Ranging first parameter
	public function shouldInstantiateWithParams_rand_1_String(){

		try{
			$classHour = new ClassHour(rand(ClassHour::MIN_HOUR, ClassHour::MAX_HOUR), 1, "Sala 1");
		}catch (Exception $caughtException){
			$classHour = FALSE;
		}

		$test_name = "Test the class constructor with valid arguments by ranging the first parameter";

		$this->unit->run($classHour, 'is_object', $test_name);
	}

	public function shouldInstantiateWithParams_MaxHour_1_String(){

		try{
			$classHour = new ClassHour(ClassHour::MAX_HOUR, 1, "Sala 1");
		}catch (Exception $caughtException){
			$classHour = FALSE;
		}

		$test_name = "Test the class constructor with valid arguments by ranging the first parameter";

		$this->unit->run($classHour, 'is_object', $test_name);
	}
//

// Ranging second parameter
	public function shouldInstantiateWithParams_1_rand_String(){

		try{
			$classHour = new ClassHour(1, rand(ClassHour::MIN_DAY,ClassHour::MAX_DAY), "Sala 1");
		}catch (Exception $caughtException){
			$classHour = FALSE;
		}

		$test_name = "Test the class constructor with valid arguments by ranging second parameter";

		$this->unit->run($classHour, 'is_object', $test_name);
	}

	public function shouldInstantiateWithParams_1_6_String(){

		try{
			$classHour = new ClassHour(ClassHour::MIN_DAY, ClassHour::MAX_DAY, "Sala 1");
		}catch (Exception $caughtException){
			$classHour = FALSE;
		}

		$test_name = "Test the class constructor with valid arguments by ranging second parameter";

		$this->unit->run($classHour, 'is_object', $test_name);
	}

//

// Ranging the third parameter
	public function shouldInstantiateWithParams_1_1_EmptyString(){

		try{
			$classHour = new ClassHour(1, 1, "");
		}catch (Exception $caughtException){
			$classHour = FALSE;
		}

		$test_name = "Test the class constructor with valid arguments by ranging third parameter";

		$this->unit->run($classHour, 'is_object', $test_name);
	}

	public function shouldInstantiateWithParams_1_1_NULL(){

		try{
			$classHour = new ClassHour(1, 1, NULL);
		}catch (Exception $caughtException){
			$classHour = FALSE;
		}

		$test_name = "Test the class constructor with valid arguments by ranging third parameter";

		$this->unit->run($classHour, 'is_object', $test_name);
	}
//

// Testing getClassHour() method

	public function shouldReturnAnArrayWithData(){

		$classHour = new ClassHour(1, 2, "Sala 1");

		$classHourData = $classHour->getClassHour();

		$test_name = "Test the method getClassHour()";

		$this->unit->run($classHourData, 'is_array', $test_name);	
		$this->unit->run($classHourData['hour'], 1, $test_name);	
		$this->unit->run($classHourData['day'], 2, $test_name);	
		$this->unit->run($classHourData['local'], "Sala 1", $test_name);
	}

//

// Testing getDayHourPair() method

	public function shouldReturnString_MondayHours(){

		$test_name = "Test the method getDayHourPair() for monday hours";

		$hours = array(
			'1' => "08h-09:50h",
			'2' => "10h-11:50h",
			'3' => "14h-15:50h",
			'4' => "16h-17:50h",
			'5' => "19h-20:50h",
			'6' => "21h-22:50h"
		);

		for($i = 1; $i <= ClassHour::MAX_HOUR; $i++){
			
			$classHour = new ClassHour($i, 1, "Sala 1");

			$dayHour = $classHour->getDayHourPair();

			$this->unit->run($dayHour, "Segunda ".$hours[$i], $test_name);
		}
	}
	
	public function shouldReturnString_TuesdayHours(){

		$test_name = "Test the method getDayHourPair() for tuesday hours";

		$hours = array(
			'1' => "08h-09:50h",
			'2' => "10h-11:50h",
			'3' => "14h-15:50h",
			'4' => "16h-17:50h",
			'5' => "19h-20:50h",
			'6' => "21h-22:50h"
		);

		for($i = 1; $i <= ClassHour::MAX_HOUR; $i++){
			
			$classHour = new ClassHour($i, 2, "Sala 1");

			$dayHour = $classHour->getDayHourPair();

			$this->unit->run($dayHour, "Terça ".$hours[$i], $test_name);
		}
	}

	public function shouldReturnString_WednesdayHours(){

		$test_name = "Test the method getDayHourPair() for wednesday hours";

		$hours = array(
			'1' => "08h-09:50h",
			'2' => "10h-11:50h",
			'3' => "14h-15:50h",
			'4' => "16h-17:50h",
			'5' => "19h-20:50h",
			'6' => "21h-22:50h"
		);

		for($i = 1; $i <= ClassHour::MAX_HOUR; $i++){
			
			$classHour = new ClassHour($i, 3, "Sala 1");

			$dayHour = $classHour->getDayHourPair();

			$this->unit->run($dayHour, "Quarta ".$hours[$i], $test_name);
		}
	}

	public function shouldReturnString_ThursdayHours(){

		$test_name = "Test the method getDayHourPair() for thursday hours";

		$hours = array(
			'1' => "08h-09:50h",
			'2' => "10h-11:50h",
			'3' => "14h-15:50h",
			'4' => "16h-17:50h",
			'5' => "19h-20:50h",
			'6' => "21h-22:50h"
		);

		for($i = 1; $i <= ClassHour::MAX_HOUR; $i++){
			
			$classHour = new ClassHour($i, 4, "Sala 1");

			$dayHour = $classHour->getDayHourPair();

			$this->unit->run($dayHour, "Quinta ".$hours[$i], $test_name);
		}
	}

	public function shouldReturnString_FridayHours(){

		$test_name = "Test the method getDayHourPair() for friday hours";

		$hours = array(
			'1' => "08h-09:50h",
			'2' => "10h-11:50h",
			'3' => "14h-15:50h",
			'4' => "16h-17:50h",
			'5' => "19h-20:50h",
			'6' => "21h-22:50h"
		);

		for($i = 1; $i <= ClassHour::MAX_HOUR; $i++){
			
			$classHour = new ClassHour($i, 5, "Sala 1");

			$dayHour = $classHour->getDayHourPair();

			$this->unit->run($dayHour, "Sexta ".$hours[$i], $test_name);
		}
	}

	public function shouldReturnString_SaturdayHours(){

		$test_name = "Test the method getDayHourPair() for saturday hours";

		$hours = array(
			'1' => "08h-09:50h",
			'2' => "10h-11:50h",
			'3' => "14h-15:50h",
			'4' => "16h-17:50h",
			'5' => "19h-20:50h",
			'6' => "21h-22:50h"
		);

		for($i = 1; $i <= ClassHour::MAX_HOUR; $i++){
			
			$classHour = new ClassHour($i, 6, "Sala 1");

			$dayHour = $classHour->getDayHourPair();

			$this->unit->run($dayHour, "Sábado ".$hours[$i], $test_name);
		}
	}
//

/*End of tests for valid entries */

/*Invalid entries test set*/

// Ranging the first parameter
	public function shouldNotInstantiateWithParams_0_1_String(){

		try{
			$classHour = new ClassHour(0, 1, "Sala 1");
		}catch (Exception $caughtException){
			$classHour = $caughtException->getMessage();
		}

		$test_name = "Test the class constructor with invalid arguments by ranging first parameter";

		$this->unit->run($classHour, ClassHour::ERR_INVALID_HOUR, $test_name);
	}

	public function shouldNotInstantiateWithParams_10_1_String(){

		try{
			$classHour = new ClassHour(10, 1, "Sala 1");
		}catch (Exception $caughtException){
			$classHour = $caughtException->getMessage();
		}

		$test_name = "Test the class constructor with invalid arguments by ranging first parameter";

		$this->unit->run($classHour, ClassHour::ERR_INVALID_HOUR, $test_name);
	}

	public function shouldNotInstantiateWithParams_randMax_1_String(){

		try{
			$classHour = new ClassHour(rand(10, PHP_INT_MAX), 1, "Sala 1");
		}catch (Exception $caughtException){
			$classHour = $caughtException->getMessage();
		}

		$test_name = "Test the class constructor with invalid arguments by ranging first parameter";

		$this->unit->run($classHour, ClassHour::ERR_INVALID_HOUR, $test_name);
	}

	public function shouldNotInstantiateWithParams_randMin_1_String(){

		try{
			$classHour = new ClassHour(rand((-1)*PHP_INT_MAX, 0), 1, "Sala 1");
		}catch (Exception $caughtException){
			$classHour = $caughtException->getMessage();
		}

		$test_name = "Test the class constructor with invalid arguments by ranging first parameter";

		$this->unit->run($classHour, ClassHour::ERR_INVALID_HOUR, $test_name);
	}
// 

// Ranging the second parameter

	public function shouldNotInstantiateWithParams_1_0_String(){

		try{
			$classHour = new ClassHour(1, 0, "Sala 1");
		}catch (Exception $caughtException){
			$classHour = $caughtException->getMessage();
		}

		$test_name = "Test the class constructor with invalid arguments by ranging second parameter";

		$this->unit->run($classHour, ClassHour::ERR_INVALID_DAY, $test_name);
	}
	
	public function shouldNotInstantiateWithParams_1_7_String(){

		try{
			$classHour = new ClassHour(1, 7, "Sala 1");
		}catch (Exception $caughtException){
			$classHour = $caughtException->getMessage();
		}

		$test_name = "Test the class constructor with invalid arguments by ranging second parameter";

		$this->unit->run($classHour, ClassHour::ERR_INVALID_DAY, $test_name);
	}

	public function shouldNotInstantiateWithParams_1_randMax_String(){

		try{
			$classHour = new ClassHour(1, rand(7, PHP_INT_MAX), "Sala 1");
		}catch (Exception $caughtException){
			$classHour = $caughtException->getMessage();
		}

		$test_name = "Test the class constructor with invalid arguments by ranging second parameter";

		$this->unit->run($classHour, ClassHour::ERR_INVALID_DAY, $test_name);
	}

	public function shouldNotInstantiateWithParams_1_randMin_String(){

		try{
			$classHour = new ClassHour(1, rand((-1)*PHP_INT_MAX, 0), "Sala 1");
		}catch (Exception $caughtException){
			$classHour = $caughtException->getMessage();
		}

		$test_name = "Test the class constructor with invalid arguments by ranging second parameter";

		$this->unit->run($classHour, ClassHour::ERR_INVALID_DAY, $test_name);
	}

// 

// Ranging the third parameter
	
	public function shouldNotInstantiateWithParams_1_1_TRUE(){

		try{
			$classHour = new ClassHour(1, 1, TRUE);
		}catch (Exception $caughtException){
			$classHour = $caughtException->getMessage();
		}

		$test_name = "Test the class constructor with invalid arguments by ranging third parameter";

		$this->unit->run($classHour, ClassHour::ERR_INVALID_LOCAL, $test_name);
	}
	
	public function shouldNotInstantiateWithParams_1_1_FALSE(){

		try{
			$classHour = new ClassHour(1, 1, FALSE);
		}catch (Exception $caughtException){
			$classHour = $caughtException->getMessage();
		}

		$test_name = "Test the class constructor with invalid arguments by ranging third parameter";

		$this->unit->run($classHour, ClassHour::ERR_INVALID_LOCAL, $test_name);
	}
	
	public function shouldNotInstantiateWithParams_1_1_1(){

		try{
			$classHour = new ClassHour(1, 1, 1);
		}catch (Exception $caughtException){
			$classHour = $caughtException->getMessage();
		}

		$test_name = "Test the class constructor with invalid arguments by ranging third parameter";

		$this->unit->run($classHour, ClassHour::ERR_INVALID_LOCAL, $test_name);
	}
	
	public function shouldNotInstantiateWithParams_1_1_array(){

		try{
			$classHour = new ClassHour(1, 1, array());
		}catch (Exception $caughtException){
			$classHour = $caughtException->getMessage();
		}

		$test_name = "Test the class constructor with invalid arguments by ranging third parameter";

		$this->unit->run($classHour, ClassHour::ERR_INVALID_LOCAL, $test_name);
	}

// 

/*End of tests for invalid entries*/

}
