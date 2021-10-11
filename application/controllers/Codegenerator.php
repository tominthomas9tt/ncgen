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

			// new Param("code", "string", "code"),
			// new Param("name", "string", "name"),
			new Param("datetime", "string", "datetime"),
			// new Param("title", "string", "title"),
			// new Param("description", "string", "description"),

			// new Param("worthAmount", "number", "worth_amount"),
			// new Param("percentageOff", "number", "percentage_off"),
			// new Param("totalProvided", "number", "total_provided"),
			// new Param("assignedToUsers", "number", "assigned_to_users"),
			// new Param("used", "number", "used"),


			// new Param("companyName", "string", "company_name"),
			// new Param("companyAddress", "string", "company_address"),
			// new Param("terms", "string", "terms"),
			new Param("userId", "number", "muser_id"),

			new Param("pointsEarned", "number", "points_earned"),
			new Param("pointsSpent", "number", "points_spent"),
			new Param("amountReceived", "number", "amount_received"),
			new Param("amountSpent", "number", "amount_spent"),
			new Param("couponId", "number", "mcoupon_id"),
			new Param("couponCode", "string", "coupon_code"),
			new Param("couponWorth", "number", "coupon_worth"),

			// new Param("subscriptionplanId", "number", "msubscriptionplan_id"),
			new Param("validFromDate", "string", "valid_from_date"),
			new Param("validToDate", "string", "valid_to_date"),
			new Param("referedById", "number", "refered_by_id"),
			new Param("referedForId", "number", "refered_for_id"),
			new Param("wallettransactiontypeId", "number", "mwallettransactiontype_id"),

			// new Param("validityInDays", "number", "validity_in_days"),
			
			// new Param("points", "number", "points"),
			// new Param("amount", "number", "amount"),
			// new Param("maxRedeemPercentage", "number", "max_redeem_percentage"),

			// new Param("freeAmount", "number", "free_amount"),
			// new Param("amountToPointsGain", "number", "amount_to_points_gain"),
			// new Param("amountToPointsSpent", "number", "amount_to_points_spent"),
			// new Param("redemptionStartMinPoints", "number", "redemption_start_min_points"),
			// new Param("insuranceCoverage", "number", "insurance_coverage"),
			// new Param("discountCouponWorth", "number", "discount_coupon_worth"),

			// new Param("transactionId", "string", "ttransaction_id"),
			// new Param("voucherNo", "string", "voucher_number"),
			// new Param("cashDebit", "number", "cash_debit"),
			// new Param("cashCredit", "number", "cash_credit"),
			// new Param("bankAccountId", "number", "mbankaccount_id"),
			// new Param("bankCredit", "number", "bank_credit"),
			// new Param("bankDebit", "number", "bank_debit"),
			// new Param("fixedDeposit", "number", "fixed_deposit"),
			// new Param("date", "string", "date"),
			// new Param("financialYearId", "number", "mfinancialyear_id"),
			// new Param("tenderId", "number", "mtender_id"),
			// new Param("workorderId", "number", "tworkorder_id"),
			// new Param("employeeId", "number", "temployee_id"),
			// new Param("familyId", "number", "mfamily_id"),
			// new Param("memberId", "number", "mmmeber_id"),
			// new Param("userId", "number", "muser_id"),
			// new Param("municipalityId", "number", "mmunicipality_id"),
			// new Param("date", "string", "date"),

			// new Param("remarks", "string", "remarks"),
			// new Param("isActive", "number", "is_active"),
			new Param("referenceNo", "string", "reference_no"),

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
		$this->prepareResourse("wallettransaction", "twallettransactions", $params);
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
