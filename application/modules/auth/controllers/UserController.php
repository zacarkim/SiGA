<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once('Useractivation.php');

require_once(MODULESPATH."auth/constants/GroupConstants.php");
require_once(MODULESPATH."auth/constants/PermissionConstants.php");
require_once(MODULESPATH."secretary/constants/EnrollmentConstants.php");
require_once(MODULESPATH."auth/domain/User.php");

require_once(MODULESPATH."notification/domain/emails/RestorePasswordEmail.php");

require_once(MODULESPATH."auth/exception/UserException.php");
require_once(MODULESPATH."auth/exception/LoginException.php");

class UserController extends MX_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('auth/usuarios_model');
	}

	public function guest_index(){

		$coursesName = $this->getCoursesName();
		$session = getSession();
		$user = $session->getUserData();
		$courseGuest = $this->usuarios_model->checkIfUserChooseCourse($user->getId());

		$data = array(
			'coursesName' => $coursesName,
			'user' => $user,
			'courseGuest' => $courseGuest
		);
		loadTemplateSafelyByGroup(GroupConstants::GUEST_GROUP,'auth/user/guest_index', $data);

	}

	private function getCoursesName(){

		$this->load->model("program/course_model");
		$availableCourses = $this->course_model->getAllCourses();

		$this->load->model('program/program_model');

		$coursesName = array();
		if($availableCourses){
			foreach ($availableCourses as $course) {
				$id = $course['id_course'];
				$program = $this->program_model->getProgramById($course['id_program']);
				$programAcronym = $program['acronym'];
				$coursesName[$id] = $programAcronym." - ".$course['course_name'];
			}
		}

		return $coursesName;
	}

	public function validateUser($login, $password){

		try{
			$foundUser = $this->usuarios_model->validateUser($login, $password);
		}catch(LoginException $e){
			throw $e;
		}

		if($foundUser !== FALSE){
			try{
				$id = $foundUser['id'];
				$name = $foundUser['name'];
				$login = $foundUser['login'];
				$email = $foundUser['email'];
				$active = $foundUser['active'];

				$user = new User($id, $name, FALSE, $email, $login, FALSE, FALSE, FALSE, FALSE, $active);

			}catch(UserException $e){
				$user = FALSE;
			}
		}else{
			$user = FALSE;
		}

		return $user;
	}

	public function usersReport(){

		$allUsers = $this->usuarios_model->getAllUsers();

		$this->load->model("auth/module_model");

		$allGroups = $this->module_model->getAllModules();

		$groups = array();
		foreach($allGroups as $group){
			$groups[$group['id_group']] = $group['group_name'];
		}

		$data = array(
			'allUsers' => $allUsers,
			'allGroups' => $groups
		);

		loadTemplateSafelyByPermission(PermissionConstants::USER_PERMISSION,'auth/user/user_report', $data);
	}

	public function manageGroups($idUser){


		$this->load->model("auth/module_model");
		$allGroups = $this->module_model->getAllModules();
		$userGroups = $this->module_model->getUserGroups($idUser);

		$groups = array();
		foreach($allGroups as $group){
			$groups[$group['id_group']] = $group['group_name'];
		}

		$data = array(
			'idUser' => $idUser,
			'userGroups' => $userGroups,
			'allGroups' => $groups
		);

		loadTemplateSafelyByPermission(PermissionConstants::USER_PERMISSION,'auth/user/manage_user_groups', $data);
	}

	public function listUsersOfGroup($idGroup){

		$usersOfGroup = $this->usuarios_model->getUsersOfGroup($idGroup);

		$data = array(
			'idGroup' => $idGroup,
			'usersOfGroup' => $usersOfGroup
		);

		loadTemplateSafelyByPermission(PermissionConstants::USER_PERMISSION,'auth/user/users_of_group', $data);
	}

	public function removeAllUsersOfGroup($idGroup){

		$wasDeleted = $this->usuarios_model->removeAllUsersOfGroup($idGroup);

		if($wasDeleted){
			$status = "success";
			$message = "Usuários removidos com sucesso.";
		}else{
			$status = "danger";
			$message = "Não foi possível remover os usuários do grupo informado. Tente novamente.";
		}

		$session = getSession();
		$session->showFlashMessage($status, $message);
		redirect("user_report");
	}

	public function addGroupToUser($idUser, $idGroup){

		$wasSaved = $this->usuarios_model->addGroupToUser($idUser, $idGroup);

		if($wasSaved){
			$status = "success";
			$message = "Grupo adicionado com sucesso.";
		}else{
			$status = "danger";
			$message = "Não foi possível adicionar o grupo informado. Tente novamente.";
		}

		$session = getSession();
		$session->showFlashMessage($status, $message);
		redirect("auth/userController/manageGroups/{$idUser}");
	}

	/**
	 * Get the group of the user to edit program
	 * @param userId - The id of the current user
	 * @return userGroup - Return the academic secretary or admin group
	 */
	public function getGroup(){

		$userGroup = "";

		$session = getSession();
		$user = $session->getUserData();
		$userId = $user->getId();

		$userGroups = $this->usuarios_model->getGroups($userId);

		if($userGroups !== FALSE){

			foreach ($userGroups as $userGroup) {
				if($userGroup['group_name'] == GroupConstants::ACADEMIC_SECRETARY_GROUP){
					$userGroup = GroupConstants::ACADEMIC_SECRETARY_GROUP;
					break;
				}
				elseif ($userGroup['group_name'] == GroupConstants::ADMIN_GROUP) {
					$userGroup = GroupConstants::ADMIN_GROUP;
					break;
				}
			}
		}


		return $userGroup;
	}

	public function removeUserGroup($idUser, $idGroup){

		$wasDeleted = $this->usuarios_model->removeUserGroup($idUser, $idGroup);

		if($wasDeleted){
			$status = "success";
			$message = "Grupo removido com sucesso.";
		}else{
			$status = "danger";
			$message = "Não foi possível remover o grupo informado. Tente novamente.";
		}
		
		if($idGroup == GroupConstants::ACADEMIC_SECRETARY_GROUP_ID){
			
			$this->removeCoursesWhenIsSecretaryGroup($idUser);
		}


		$session = getSession();
		$session->showFlashMessage($status, $message);
		redirect("auth/userController/manageGroups/{$idUser}");
	}

	public function removeUserFromGroup($idUser, $idGroup){

		$wasDeleted = $this->usuarios_model->removeUserGroup($idUser, $idGroup);

		if($wasDeleted){
			$status = "success";
			$message = "Usuário removido com sucesso.";
		}
		else{
			$status = "danger";
			$message = "Não foi possível remover o usuário informado. Tente novamente.";
		}

		if($idGroup == GroupConstants::ACADEMIC_SECRETARY_GROUP_ID){
			
			$this->removeCoursesWhenIsSecretaryGroup($idUser);
		}

		$session = getSession();
		$session->showFlashMessage($status, $message);
		redirect("auth/userController/listUsersOfGroup/{$idGroup}");
	}

	private function removeCoursesWhenIsSecretaryGroup($idUser){

		$this->load->model("program/course_model");
		$deletedSecretary = FALSE;
		$courses = $this->course_model->getCoursesOfSecretary($idUser);
		foreach ($courses as $course) {
			$idCourse = $course['id_course'];
			$deletedSecretary = $this->course_model->deleteSecretary($idCourse, FALSE, $idUser);
		}
	}

	public function checkIfUserExists($idUser){

		$userExists = $this->usuarios_model->checkIfUserExists($idUser);

		return $userExists;
	}

	public function getUsersToBeSecretaries(){

		$this->load->model("auth/module_model");
		$groupData = $this->module_model->getGroupByGroupName(GroupConstants::SECRETARY_GROUP);
		$idGroup = $groupData['id_group'];

		$users = $this->usuarios_model->getUsersOfGroup($idGroup);

		return $users;
	}

	public function getUserCourses($userId){

		$userCourses = $this->usuarios_model->getUserCourse($userId);

		return $userCourses;
	}

	public function getUserStatus($userId){

		$userStatus = $this->usuarios_model->getUserStatus($userId);

		return $userStatus;
	}

	public function register($groups=array(), $email="", $hidden=array()){

		if(!empty($groups)){
			$userGroups = $groups;
		}else{
			$userGroups = $this->usuarios_model->getAllowedUserGroupsForFirstRegistration();
		}

		$data = array(
			'userGroups' => $userGroups,
			'email' => $email,
			'hidden' => $hidden
		);

		if(!empty($groups)){
			$this->load->view("auth/user/new_user", $data);
		}else{
			$this->load->template("auth/user/new_user", $data);
		}
	}

	public function conta(){

		$session = getSession();
		$loggedUser = $session->getUserData();

		$data = array("user" => $loggedUser);

		$this->load->template("auth/user/conta", $data);
	}

	public function profile() {

		$session = getSession();
		$loggedUser = $session->getUserData();
		$userId = $loggedUser->getId();

		$user = $this->usuarios_model->getObjectUser($userId);

		$data = array(
			'user' => $user
		);

		$this->load->template("auth/user/conta", $data);
	}


	public function restorePassword(){
		$validData = $this->validateDataForRestorePassword();

		if($validData){
			$session = getSession();
			$email = $this->input->post("email");
			$user = $this->usuarios_model->getUserByEmail($email);
			if($user !== FALSE){
				$userWithNewPassword = $this->generateNewPassword($user);
				$email = new RestorePasswordEmail($userWithNewPassword);
				$success = $email->notify();
				if($success){
					$id = $user->getId();
					$newPassword = $userWithNewPassword->getPassword();
					$encryptedPassword = $this->usuarios_model->encryptPassword($newPassword);
        			$updated = $this->usuarios_model->updatePassword($id, $encryptedPassword, TRUE); // Saving new temporary password
					if($updated){
						$session->showFlashMessage("success", "Email enviado com sucesso.");
						redirect("/");
					}
					else{
						$session->showFlashMessage("danger", "O email foi enviado, mas houve um problema com a definição da nova senha. Solicite o envio novamente.");
						redirect("auth/userController/restorePassword");
					}

				}
				else{
					$session->showFlashMessage("danger", "Não foi possível enviar o email. Tente novamente.");
					redirect("auth/userController/restorePassword");
				}
			}
			else{
				$session->showFlashMessage("danger", "Não foi encontrado nenhum usuário com esse email.");
				redirect("auth/userController/restorePassword");
			}
		}
		else{
			$this->load->template("auth/user/restore_password");
		}

	}

    private function generateNewPassword($user){

        define('PASSWORD_LENGTH', 4); // The length of the binary to generate new password

        $newPassword = bin2hex(openssl_random_pseudo_bytes(PASSWORD_LENGTH));

        $id = $user->getId();

        $user = new User($id, $user->getName(), FALSE, $user->getEmail(), $user->getLogin(), $newPassword, FALSE);

        return $user;
    }
	private function validateDataForRestorePassword(){

		$this->load->library("form_validation");
		$this->form_validation->set_rules("email", "E-mail", "required|valid_email");
		$this->form_validation->set_error_delimiters("<p class='alert-danger'>", "</p>");
		$success = $this->form_validation->run();

		return $success;
	}
	public function changePassword(){

		$success = $this->validatePasswordField();

		$session = getSession();
		$userData = $session->getUserData();
		if ($success) {

			$password = $this->input->post("password");
			$confirmPassword = $this->input->post("confirm_password");

			$isValidPassword = $this->verifyIfPasswordsAreEquals($password, $confirmPassword);

			$password = $this->usuarios_model->encryptPassword($password);
			$confirmPassword = $this->usuarios_model->encryptPassword($confirmPassword);
			if($isValidPassword){


				$userId = $userData->getId();
				$temporaryPassword = FALSE;

				$isUpdated = $this->usuarios_model->updatePassword($userId, $password, $temporaryPassword);

				if($isUpdated){
					$session->showFlashMessage("success", "Senha alterada com sucesso.");
					redirect('/');
				}
				else{
					$session->showFlashMessage("danger", "Não foi possível alterar a senha. Tente novamente.");
					redirect('auth/userController/changePassword');
				}
			}
			else{
				$session->showFlashMessage("danger", "As senhas devem ser iguais.");
				redirect('auth/userController/changePassword');
			}
		}
		else{

			$this->load->template("auth/user/change_password");
		}
	}
	public function validatePasswordField(){

		$this->load->library("form_validation");
		$this->form_validation->set_rules("password", "Digite sua nova senha", "required");
		$this->form_validation->set_rules("confirm_password", "Confirme sua nova senha", "required");
		$this->form_validation->set_error_delimiters("<p class='alert-danger'>", "</p>");
		$success = $this->form_validation->run();

		return $success;
	}

	/**
		* Verify if password and confirm password are equals
		* @param: password: Receive the password
		* @param: confirmPassword: Receive the confirm password
	*/
	public function verifyIfPasswordsAreEquals($password, $confirmPassword){

		if ($password == $confirmPassword){
			$validPassword = TRUE;
		}
		else{
			$validPassword = FALSE;
		}

		return $validPassword;
	}

	public function newUser() {

		$name  = $this->input->post("name");
		$cpf   = $this->input->post("cpf");
		$email = $this->input->post("email");
		$group = $this->input->post("userGroup");
		$login = $this->input->post("login");
		$password =  $this->usuarios_model->encryptPassword($this->input->post("password"));

		$user = array(
			'name'       => $name,
			'cpf'        => $cpf,
			'email'      => $email,
			'login'      => $login,
			'password' 	 => $password,
			'active' => 0
		);

		$invitation = $this->input->post("userInvitation");

		$success = $this->validateRegisterUserFields();
		if($success){
			$this->registerUser($user, $group, $invitation);
		}
		else{

			if(is_null($invitation)){
				$this->register();
			}else{
				$this->load->module("secretary/userInvitation");
				$this->userinvitation->register($invitation);
			}
		}

	}

	private function validateRegisterUserFields(){

		$this->load->library("form_validation");
		$this->form_validation->set_rules("name", "Nome", "required|trim|valid_name");
		$this->form_validation->set_rules("cpf", "CPF", "required|valid_cpf|verify_if_cpf_no_exists");
		$this->form_validation->set_rules("email", "E-mail", "required|valid_email|verify_if_email_no_exists");
		$this->form_validation->set_rules("login", "Login", "required|alpha_dash|verify_if_login_no_exists");
		$this->form_validation->set_rules("password", "Senha", "required");
		$this->form_validation->set_error_delimiters("<p class='alert-danger'>", "</p>");
		$success = $this->form_validation->run();

		return $success;
	}

	private function registerUser($user, $group, $invitation=NULL){

		$this->load->module("auth/useractivation");
		$this->load->module("notification/notification");
		$this->load->model("secretary/userInvitation_model", "invitation_model");

		// Starting transaction
		$this->db->trans_start();

		$this->usuarios_model->save($user);
		$this->usuarios_model->saveGroup($user, $group);
		$savedUser = $this->usuarios_model->getUserDataByLogin($user['login']);

		$activation = $this->useractivation->generateActivation($savedUser);

		if(!is_null($invitation)){
			$this->invitation_model->disable($invitation);
			$this->notification->newRegisterByInvitationNotification($invitation, $user['name']);
		}

		// Finishing transaction
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$status = "danger";
			$message = "Não foi possível realizar o cadastro solicitado. Tente novamente.";
		}else{

			$this->load->module("notification/emailSender");
			$message = $this->emailsender->sendConfirmationEmail($savedUser, $activation);

			$status = $message['status'];
			$message = $message['message'];
		}

		$session = getSession();
		$session->showFlashMessage($status, $message);
		redirect("/");
	}

	public function updateProfile(){

		$user = $this->getAccountForm();

		if(!is_null($user)){

			$updated = $this->usuarios_model->update($user);

			$session = getSession();
			if ($updated) {
				$session->login($user);
				$session->showFlashMessage("success", "Os dados foram alterados");
			}
			else if (!$updated){
				$session->showFlashMessage("danger", "Os dados não foram alterados");
			}
			redirect('profile');
		}
		else{

			$this->profile();
		}
	}

	private function validateEmailField($oldEmail, $newEmail){
		$this->load->library("form_validation");

		$this->form_validation->set_rules("name", "Nome", "trim|valid_name");

		if($oldEmail != $newEmail){
			$this->form_validation->set_rules("email", "E-mail", "valid_email|verify_if_email_no_exists");
		}
		$this->form_validation->set_error_delimiters("<p class='alert-danger'>", "</p>");
		$success = $this->form_validation->run();
		return $success;
	}

	public function remove() {
		$session = getSession();
		$user = $session->getUserData();

		if ($this->usuarios_model->remove($user)) {
			$session->unsetUserData();
			$login = $user->getLogin();
			$session->showFlashMessage("success", "Usuário \"{$login}\" removido");
			redirect("login");
		}
		else {
			$dados = array('user' => $user);
			$this->load->template("auth/user/conta", $dados);
		}

	}

	public function getUserByName($userName){

		$foundUser = $this->usuarios_model->getUserByName($userName);

		return $foundUser;
	}

	public function getUsersOfGroup($idGroup, $name = FALSE){

		$groups = $this->usuarios_model->getUsersOfGroup($idGroup, $name);

		return $groups;
	}

	public function getUserById($userId){

		$foundUser = $this->usuarios_model->getUserById($userId);

		return $foundUser;
	}

	public function getUserGroupNameByIdGroup($groupId){

		$groupName = $this->usuarios_model->getUserGroupNameByIdGroup($groupId);

		return $groupName;
	}

	/**
	 * Get all the user types from database into an array.
	 * @return An array with all user types on database as id => type_name
	 */
	public function getUserGroups(){

		$user_groups = $this->usuarios_model->getAllUserGroups();

		$user_groups_to_array = $this->turnUserGroupsToArray($user_groups);

		return $user_groups_to_array;
	}

	public function getUserNameById($idUser){
		$userName = $this->usuarios_model->getNameByUserId($idUser);

		return $userName;
	}

	/**
	  * Join the id's and names of user types into an array as key => value.
	  * Used to the user type form
	  * @param $user_groups - The array that contains the tuples of user_groups
	  * @return An array with the id's and user types names as id => user_group_name
	  */
	private function turnUserGroupsToArray($user_groups){
		// Quantity of user types registered
		$quantity_of_user_groups = sizeof($user_groups);

		for($cont = 0; $cont < $quantity_of_user_groups; $cont++){
			$keys[$cont] = $user_groups[$cont]['id_group'];
			$values[$cont] = $user_groups[$cont]['group_name'];
		}

		$form_user_groups = array_combine($keys, $values);

		return $form_user_groups;
	}

	private function getAccountForm() {

		$id = $this->input->post("id");
		$name = $this->input->post("name");
		$oldEmail = $this->input->post("oldEmail");
		$email = $this->input->post("email");
		$homePhone = $this->input->post("home_phone");
		$cellPhone = $this->input->post("cell_phone");
		$oldPassword = $this->input->post("password");
		$newPassword = $this->input->post("new_password");

		$success = $this->validateEmailField($oldEmail, $email);


		if($success){

			$session = getSession();
			$user = $session->getUserData();
			$userId = $user->getId();

			$user = $this->usuarios_model->getObjectUser($userId);
			$login = $user->getLogin();

			$currentPassword = $user->getPassword();
			$newPasswordBlank = empty($newPassword);
			$oldPassword = md5($oldPassword);
			if (!$newPasswordBlank && !password_verify($oldPassword, $currentPassword)) {
				$session->showFlashMessage("danger", "Senha atual incorreta");
				redirect("profile");
			}
			else if ($newPasswordBlank) {
				$newPassword = $user->getPassword();
			}
			else{
				$newPassword = $this->usuarios_model->encryptPassword($newPassword);
			}

			if (empty($name)) {
				$name = $user->getName();
			}

			if (empty($email)) {
				$email = $user->getEmail();
			}

			if (empty($homePhone)) {
				$homePhone = $user->getHomePhone();
			}

			if (empty($cellPhone)) {
				$cellPhone = $user->getCellPhone();
			}

			try{
				$user = new User($id, $name, FALSE, $email, $login, $newPassword, FALSE, $homePhone, $cellPhone);

				return $user;

			}
			catch(UserException $e){
				$session->showFlashMessage("danger", $e->getMessage());
				redirect("profile");
			}

		}
		else{
			$user = NULL;
		}

		return $user;
	}

	function alpha_dash_space($str) {
		return ( ! preg_match("/^([-a-z_ ])+$/i", $str)) ? FALSE : TRUE;
	}


}
