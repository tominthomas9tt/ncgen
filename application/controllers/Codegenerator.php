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
	}

	public function index($parameter = false)
	{
		echo "Welcome to Node code generator.";
		assurePaths($this->buildPath);
		$params = array(
			new Param("id", "number", "id"),
			new Param("name", "string", "name"),

			new Param("productId", "string", "mproduct_id"),

			// new Param("remarks", "string", "remarks"),
			// new Param("createdAt", "string", "created_at"),
			// new Param("createdBy", "number", "created_by"),
			// new Param("updatedAt", "string", "updated_at"),
			// new Param("updatedBy", "number", "updated_by"),
			// new Param("deletedAt", "string", "deleted_at"),
			// new Param("deletedBy", "number", "deleted_by"),
			new Param("status", "number", "status"),
			new Param("astatus", "number", "astatus"),
		);
		$this->prepareResourse("equipment", "mequipments", $params);
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

	private function customPraams()
	{
		$array = [
			new Param("id", "number", "id"),

			new Param("name", "string", "name"),

			new Param("date", "string", "date"),

			new Param("applicationNo", "string", "application_no"),

			new Param("dateOfBirth", "string", "date_birth"),
			new Param("maritalStatus", "string", "marital_status"),
			new Param("mobile", "string", "mobile"),
			new Param("email", "string", "email"),


			new Param("referenceNo", "string", "reference_no"),
			new Param("remarks", "string", "remarks"),
			new Param("createdAt", "string", "created_at"),
			new Param("createdBy", "number", "created_by"),
			new Param("updatedAt", "string", "updated_at"),
			new Param("updatedBy", "number", "updated_by"),
			new Param("deletedAt", "string", "deleted_at"),
			new Param("deletedBy", "number", "deleted_by"),
			new Param("status", "number", "status"),
			new Param("astatus", "number", "astatus"),
		];
	}
}
