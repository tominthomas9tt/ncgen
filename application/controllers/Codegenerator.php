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

	public function index()
	{
		echo "Welcome to Node code generator.";
		assurePaths($this->buildPath);
		// $tables = $this->cm->getAllTables();
		$tables = ['ma_code_types1'];
		foreach ($tables as $table) {
			$tableName = $table;
			$resourseName = resourceNameDefiner($tableName, ["ma_"]);
			$fieldDatas = $this->cm->getTableStructure($tableName);
			$params = [];
			foreach ($fieldDatas as $fieldData) {
				$name = $fieldData->name;
				$type = typeDeterminer($fieldData->type);
				$propertyName = camelize($name);
				$newParam = new Param($propertyName, $type, $name);
				array_push($params, $newParam);
			}
			$this->prepareResourse($resourseName, $tableName, $params);
		}
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
		$this->generateServiceFor($resourseName, $params);
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

	private function generateServiceFor($resourseName, $params)
	{
		$data = getServiceData($resourseName, $params, $this->importFor, $this->nameFor, $this->pathFor);
		$serviceFile = $this->buildPath . $this->pathFor['service'] . $this->fileExtension;
		$fileSaved = saveFile($serviceFile, $data);
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
