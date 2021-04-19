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

	private $pathFor;
	private $importFor;
	private $nameFor;

	public function __construct()
	{
		parent::__construct();

		$this->load->helper("My_node_code_helper");
	}

	public function index($parameter = false)
	{
		echo "Welcome to Node code generator.";
		assurePaths($this->buildPath);
		$this->prepareResourse("availability1");
	}

	private function prepareResourse($resourseName, $params = [])
	{
		$this->pathFor = generatePathsFor($resourseName);
		$this->nameFor = generateNameFor($resourseName);
		$this->importFor = generateImportsFor($resourseName, $this->pathFor, $this->nameFor);

		$params = array(
			new Param("name", "string", "name"),
			new Param("propertyId", "number", "property_id")
		);
		$this->generateRouteFor($resourseName);
		$this->generateControllerFor($resourseName, $params);
		$this->generateModel($resourseName, $params);
		$this->generateDto($resourseName, $params);
		// $this->generateInterface($resourseName, $params);
		$this->generateSql($resourseName, $params);
	}

	private function generateRouteFor($resourseName)
	{
		$data = getRouteData($resourseName, $this->nameFor, $this->importFor);
		$routeFile = $this->buildPath . $this->pathFor['route'] . $this->fileExtension;
		$fileSaved = saveFile($routeFile, $data);
		return;
	}

	private function generateControllerFor($resourseName, $params)
	{
		$data = getControllerData($resourseName, $params, $this->importFor, $this->nameFor);
		$ControllerFile = $this->buildPath . $this->pathFor['controller'] . $this->fileExtension;
		$fileSaved = saveFile($ControllerFile, $data);
		return;
	}

	private function generateModel($resourseName, $params, $options = false)
	{
		$data = getModelData($resourseName, $params, $this->importFor, $this->nameFor, $options);
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

	private function generateSql($resourseName, $params)
	{
		$data = getSqlData($resourseName, $params, $this->importFor, $this->nameFor, $options = false);
		$sqlFile = $this->buildPath . $this->pathFor['sql'] . $this->fileExtension;
		$filesaved = saveFile($sqlFile, $data);
		return;
	}
}
