<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Param
{
	public $name;
	public $fieldName;
	public $type;

	public function __construct($name, $dataType, $fieldName = false)
	{
		$this->name = $name;
		$this->fieldName = $fieldName ? $fieldName : $name;
		$this->type = $dataType;
	}
}

class Codegenerator extends CI_Controller
{

	public $buildPath = "build/";
	public $fileExtension = ".ts";

	private $resourseName;
	private $pathFor;
	private $importFor;
	private $nameFor;
	private $doAuthentication = true;
	private $tableName;

	public function __construct()
	{
		parent::__construct();

		$this->load->helper("My_node_code_helper");

		$this->load->model("Commonmodel", "cm");
	}

	public function index($parameter = false)
	{
		echo "Welcome to Node code generator.";
		assurePaths($this->buildPath);
		$da = $this->cm->getTables();
		var_dump($da);
		$params = array(
			new Param("id", "number", "id"),
			new Param("name", "string", "name"),
			new Param("description", "string", "description"),

			new Param("userId", "number", "muser_id"),
			// new Param("accountuserId", "number", "maccountuser_id"),


			// new Param("credId", "number", "tcred_id"),
			// new Param("credtagId", "number", "mcredtag_id"),
			// new Param("credmasterId", "number", "mcredmaster_id"),
			// new Param("parentId", "number", "tparent_id"),

			// new Param("notificationId", "number", "tnotification_id"),
			// new Param("batchNo", "number", "batch_no"),

			// new Param("data", "string", "data"),
			// new Param("value", "string", "value"),
			// new Param("sentAt", "string", "senT_at"),

			// new Param("username", "string", "username"),
			// new Param("password", "string", "password"),
			// new Param("host", "string", "host"),
			// new Param("service", "string", "service"),
			// new Param("port", "string", "port"),
			// new Param("entityId", "string", "entity_id"),
			// new Param("senderId", "string", "sender_id"),
			// new Param("authKey", "string", "auth_key"),
			// new Param("authSecret", "string", "auth_secret"),
			// new Param("route", "number", "route"),


			// new Param("notificationtypeId", "number", "mnotificationtype_id"),
			// new Param("configId", "number", "tconfig_id"),
			// new Param("subject", "string", "subject"),
			// new Param("body", "string", "body"),
			// new Param("template", "string", "template"),
			// new Param("data", "string", "data"),

			// new Param("addressId", "number", "maddress_id"),
			// new Param("title", "string", "title"),
			// new Param("description", "string", "description"),
			// new Param("thumbUrl", "string", "thumb_url"),
			// new Param("coverUrl", "string", "cover_url"),

			// new Param("zeropropertytypeId", "number", "tzeropropertytype_id"),

			// new Param("publishedDate", "string", "published_date"),
			// new Param("toDate", "string", "to_date"),
			// new Param("priority", "number", "priority"),
			// new Param("isFeatures", "number", "is_features"),

			// new Param("info1", "string", "info1"),
			// new Param("info2", "string", "info2"),
			// new Param("info3", "string", "info3"),
			// new Param("info4", "string", "info4"),
			// new Param("info5", "string", "info5"),
			// new Param("viewCount", "number", "view_counts"),
			// new Param("contactCount", "number", "contact_counts"),


			// new Param("name", "string", "name"),
			// new Param("zeroserviceId", "number", "tzeroservice_id"),
			// new Param("parentId", "number", "tparent_id"),

			// new Param("isVisible", "number", "is_visible"),
			// new Param("label", "string", "label"),
			// new Param("showLabel", "number", "show_label"),
			// new Param("value", "string", "value"),
			// new Param("municipalityId", "number", "mmunicipality_id"),
			// new Param("isEnabled", "number", "is_enabled"),

			// new Param("value1", "string", "value_1"),
			// new Param("value2", "string", "value_2"),
			// new Param("value3", "string", "value_3"),
			// new Param("value4", "string", "value_4"),
			// new Param("value5", "string", "value_5"),
			// new Param("description", "string", "description"),
			// new Param("roleId", "number", "mrole_id"),



			// new Param("memberSince", "string", "membersince"),
			// new Param("firstName", "string", "first_name"),
			// new Param("middleName", "string", "middle_name"),
			// new Param("lastName", "string", "last_name"),

			// new Param("code", "string", "code"),

			// new Param("dateOfbirth", "string", "date_of_birth"),
			// new Param("genderId", "number", "mgender_id"),
			// new Param("email1", "string", "email1"),
			// new Param("email2", "string", "email2"),
			// new Param("phone1", "string", "phone1"),
			// new Param("phone2", "string", "phone2"),
			// new Param("username", "string", "username"),
			// new Param("password", "string", "password"),
			// new Param("father", "string", "father"),
			// new Param("mother", "string", "mother"),
			// new Param("qualificationId", "number", "mqualification_id"),
			// new Param("occupationId", "number", "moccupation_id"),
			// new Param("bloodgroupId", "number", "mbloodgroup_id"),
			// new Param("contact1", "string", "contact1"),
			// new Param("contact2", "string", "contact2"),
			// new Param("email1", "string", "email1"),
			// new Param("addressline1", "string", "addressline1"),
			// new Param("addressline2", "string", "addressline2"),
			// new Param("city", "string", "city"),
			// new Param("stateId", "number", "mstate_id"),
			// new Param("pincode", "string", "pincode"),
			// new Param("parishId", "number", "mparish_id"),
			// new Param("sccId", "number", "scc_id"),
			// new Param("familyId", "number", "family_id"),
			// new Param("memberId", "number", "member_id"),

			// new Param("lastLoginDate", "string", "last_login_date"),
			// new Param("lastLoginIp", "string", "last_login_ip"),
			// new Param("lastLoginDevice", "string", "last_login_device"),
			// new Param("lastLoginLocation", "string", "last_login_location"),
			// new Param("externalAuthId", "string", "external_auth_id"),
			// new Param("externalAuthSourceId", "number", "external_auth_source_id"),
			// new Param("isUsernameVerified", "number", "is_username_verified"),
			// new Param("usernameVerificationCode", "string", "username_verification_code"),
			// new Param("resetPasswordCode", "string", "reset_password_code"),
			// new Param("refreshToken", "string", "refresh_token"),

			// new Param("substationId", "number", "substation_id"),
			// new Param("institutionId", "number", "institution_id"),
			// new Param("conventId", "number", "convent_id"),
			// new Param("associationId", "number", "association_id"),
			// new Param("sisterId", "number", "sister_id"),
			// new Param("catechistId", "number", "catechist_id"),


			// new Param("isBaptised", "number", "baptism_flag"),
			// new Param("isCommunion", "number", "communion_flag"),
			// new Param("isConfirmation", "number", "confirmation_flag"),
			// new Param("isMarried", "number", "marriage_flag"),
			// new Param("isVocation", "number", "vocation_flag"),
			// new Param("isDead", "number", "death_flag"),

			// new Param("mledgerId", "number", "mledger_id"),
			// new Param("tledgerId", "number", "tledger_id"),
			// new Param("parishId", "number", "mparish_id"),
			// new Param("familyId", "number", "mfamily_id"),
			// new Param("memberId", "number", "mmmeber_id"),


			// new Param("remarks", "string", "remarks"),
			// new Param("isActive", "number", "active"),
			// new Param("referenceNo", "string", "reference_no"),

			// new Param("description", "string", "description"),
			new Param("createdAt", "string", "created_at"),
			new Param("createdBy", "number", "created_by"),
			new Param("updatedAt", "string", "updated_at"),
			new Param("updatedBy", "number", "updated_by"),
			new Param("deletedAt", "string", "deleted_at"),
			new Param("deletedBy", "number", "deleted_by"),
			new Param("status", "number", "status"),
			new Param("astatus", "number", "astatus"),
		);
		
		// $this->prepareResourse("accountuser", "taccountusers", $params);
	}

	private function prepareResourse($resourseName, $tableName, $params = [])
	{
		$this->pathFor = generatePathsFor($resourseName);
		$this->nameFor = generateNameFor($resourseName);
		$this->importFor = generateImportsFor($resourseName, $this->pathFor, $this->nameFor);
		$this->doAuthentication = true;
		$options = array(
			'tableName' => $tableName
		);
		$this->generateRouteFor($resourseName);
		$this->generateControllerFor($resourseName, $params);
		$this->generateModel($resourseName, $params, $options);
		$this->generateDto($resourseName, $params);
		$this->generateInterface($resourseName, $params);
		$this->generateSql($resourseName, $params, $options);
	}

	private function generateRouteFor($resourseName)
	{
		$data = getRouteData($resourseName, $this->nameFor, $this->importFor, $this->doAuthentication);
		$routeFile = $this->buildPath . $this->pathFor['route'] . $this->fileExtension;
		$fileSaved = saveFile($routeFile, $data);
		return;
	}

	private function generateControllerFor($resourseName, $params)
	{
		$data = getControllerData($resourseName, $params, $this->importFor, $this->nameFor, $this->pathFor);
		$ControllerFile = $this->buildPath . $this->pathFor['controller'] . $this->fileExtension;
		$fileSaved = saveFile($ControllerFile, $data);
		return;
	}

	private function generateModel($resourseName, $params, $options = false)
	{
		$data = getModelData($resourseName, $params, $this->importFor, $this->nameFor, $this->pathFor, $options);
		$ModelFile = $this->buildPath . $this->pathFor['model'] . $this->fileExtension;
		$fileSaved = saveFile($ModelFile, $data);
		return;
	}

	private function generateDto($resourseName, $params)
	{
		$data = getDtoData($resourseName, $params, $this->importFor, $this->nameFor);
		$dtoFile = $this->buildPath . $this->pathFor['dto'] . $this->fileExtension;
		$filesaved = saveFile($dtoFile, $data);
		return;
	}

	private function generateInterface($resourseName, $params)
	{
		$data = getInterfaceData($resourseName, $params, $this->importFor, $this->nameFor);
		$interfaceFile = $this->buildPath . $this->pathFor['interface'] . $this->fileExtension;
		$filesaved = saveFile($interfaceFile, $data);
		return;
	}

	private function generateSql($resourseName, $params, $options = false)
	{
		$data = getSqlData($resourseName, $params, $this->importFor, $this->nameFor, $options);
		$sqlFile = $this->buildPath . $this->pathFor['sql'] . $this->fileExtension;
		$filesaved = saveFile($sqlFile, $data);
		return;
	}
}
