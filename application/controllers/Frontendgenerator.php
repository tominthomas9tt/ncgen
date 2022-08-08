<?php
// defined('BASEPATH') or exit('No direct script access allowed');

// class FeParam
// {
// 	public $name;
// 	public $fieldName;
// 	public $type;

// 	public function __construct($popertyName, $dataType, $name = false)
// 	{
// 		$this->name = $name ? $name : $popertyName;
// 		$this->propertyName = $popertyName;
// 		$this->type = $dataType;
// 	}
// }

// class Frontendgenerator extends CI_Controller
// {

// 	public $buildPath = "fe_build/";
// 	public $fileExtension = ".ts";

// 	private $resourseName;
// 	private $pathFor;
// 	private $importFor;
// 	private $nameFor;
// 	private $doAuthentication = true;
// 	private $tableName;

// 	public function __construct()
// 	{
// 		parent::__construct();

// 		$this->load->helper("My_fe_code_helper");
// 	}

// 	public function index($parameter = false)
// 	{
// 		echo "Welcome to Node code generator.";
// 		assurePaths($this->buildPath);
// 		$params = array(
// 			new FeParam("id", "number"),



// 			// new Param("remarks", "string", "remarks"),
// 			// new Param("isActive", "number", "is_active"),
// 			// new Param("referenceNo", "string", "reference_no"),
// 			// new Param("description", "string", "description"),
// 			new Param("createdAt", "string"),
// 			new Param("createdBy", "number"),
// 			new Param("updatedAt", "string"),
// 			new Param("updatedBy", "number"),
// 			new Param("deletedAt", "string"),
// 			new Param("deletedBy", "number"),
// 			new Param("status", "number"),
// 			new Param("astatus", "number"),
// 		);
// 		$this->prepareResourse("userdepartment", "tuserdepartments", $params);
// 	}

// 	private function prepareResourse($resourseName, $tableName, $params = [])
// 	{
// 		$this->pathFor = generatePathsFor($resourseName);
// 		$this->nameFor = generateNameFor($resourseName);
// 		$this->importFor = generateImportsFor($resourseName, $this->pathFor, $this->nameFor);
// 		$this->doAuthentication = true;
// 		$options = array(
// 			'tableName' => $tableName
// 		);
// 		$this->generateRouteFor($resourseName);
// 		$this->generateControllerFor($resourseName, $params);
// 		$this->generateModel($resourseName, $params, $options);
// 		$this->generateDto($resourseName, $params);
// 		$this->generateInterface($resourseName, $params);
// 		$this->generateSql($resourseName, $params, $options);
// 	}

// 	private function generateRouteFor($resourseName)
// 	{
// 		$data = getRouteData($resourseName, $this->nameFor, $this->importFor, $this->doAuthentication);
// 		$routeFile = $this->buildPath . $this->pathFor['route'] . $this->fileExtension;
// 		$fileSaved = saveFile($routeFile, $data);
// 		return;
// 	}


// 	private function generateModule()
// 	{
// 	}

// 	public function generateBaseComponent($componentName)
// 	{
// 		$pathForBaseComponents=
// 		$data = getBaseComponentHtml($resourseName, $params, $this->importFor, $this->nameFor, $this->pathFor);
// 		$file = $this->buildPath . $this->pathFor['controller'] . $this->fileExtension;
// 		$fileSaved = saveFile($ControllerFile, $data);
// 		return;
// 	}

// 	public function generateDetailsComponent($componentName)
// 	{
// 	}

// 	public function generateModifyComponent($componentName)
// 	{
// 	}

// 	public function generateFilterComponent($componentName)
// 	{
// 	}


// 	private function generateControllerFor($resourseName, $params)
// 	{
// 		$data = getControllerData($resourseName, $params, $this->importFor, $this->nameFor, $this->pathFor);
// 		$ControllerFile = $this->buildPath . $this->pathFor['controller'] . $this->fileExtension;
// 		$fileSaved = saveFile($ControllerFile, $data);
// 		return;
// 	}

// 	private function generateModel($resourseName, $params, $options = false)
// 	{
// 		$data = getModelData($resourseName, $params, $this->importFor, $this->nameFor, $this->pathFor, $options);
// 		$ModelFile = $this->buildPath . $this->pathFor['model'] . $this->fileExtension;
// 		$fileSaved = saveFile($ModelFile, $data);
// 		return;
// 	}

// 	private function generateDto($resourseName, $params)
// 	{
// 		$data = getDtoData($resourseName, $params, $this->importFor, $this->nameFor);
// 		$dtoFile = $this->buildPath . $this->pathFor['dto'] . $this->fileExtension;
// 		$filesaved = saveFile($dtoFile, $data);
// 		return;
// 	}

// 	private function generateInterface($resourseName, $params)
// 	{
// 		$data = getInterfaceData($resourseName, $params, $this->importFor, $this->nameFor);
// 		$interfaceFile = $this->buildPath . $this->pathFor['interface'] . $this->fileExtension;
// 		$filesaved = saveFile($interfaceFile, $data);
// 		return;
// 	}

// 	private function generateSql($resourseName, $params, $options = false)
// 	{
// 		$data = getSqlData($resourseName, $params, $this->importFor, $this->nameFor, $options);
// 		$sqlFile = $this->buildPath . $this->pathFor['sql'] . $this->fileExtension;
// 		$filesaved = saveFile($sqlFile, $data);
// 		return;
// 	}
// }
