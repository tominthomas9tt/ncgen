
<?php

defined('BASEPATH') or exit('No direct script access allowed');

/***********************************************
 *
 *    if (!function_exists("functionName")) {
 *        function functionName($params)
 *       {
 *       }
 *    }
 *
 ************************************************/

if (!function_exists("resourceNameDefiner")) {
    function resourceNameDefiner($name, $replaceArray)
    {
        $returnName = $name;
        if (count($replaceArray) > 0) {
            foreach ($replaceArray as $replaceString) {
                $returnName = str_replace($replaceString, "", $returnName);
            }
        }
        return $returnName;
    }
}

if (!function_exists("typeDeterminer")) {
    function typeDeterminer($type)
    {
        $numberTypes = ["int", "tinyint", "decimal", "float", "double"];
        $returnType = 'string';
        if (in_array($type, $numberTypes)) {
            $returnType = "number";
        }
        return $returnType;
    }
}

if (!function_exists("camelize")) {
    function camelize($input)
    {
        return strtolower($input[0]) . substr(str_replace(' ', '', ucwords(preg_replace('/[\s_]+/', ' ', $input))), 1);
    }
}

if (!function_exists("saveFile")) {
    function saveFile($path, $data)
    {
        $isWritten = file_put_contents($path, $data);
        return $isWritten;
    }
}

if (!function_exists("checkOrCreateDirectory")) {
    function checkOrCreateDirectory($path = false)
    {
        $rootPath = FCPATH;
        $buildPath = $rootPath .  (!empty($path) ? $path : "");
        if (!file_exists($buildPath)) {
            mkdir($path, 0777, true);
        }
        return $buildPath;
    }
}

if (!function_exists("assurePaths")) {
    function assurePaths($buildPath)
    {
        checkOrCreateDirectory($buildPath . "routes");
        checkOrCreateDirectory($buildPath . "controllers");
        checkOrCreateDirectory($buildPath . "models");
        checkOrCreateDirectory($buildPath . "dtos");
        checkOrCreateDirectory($buildPath . "sqls");
        checkOrCreateDirectory($buildPath . "interfaces");
    }
}

if (!function_exists("generatePathsFor")) {
    function generatePathsFor($entity)
    {
        $result['route'] = "routes" . "/" . $entity . ".route";
        $result['controller'] = "controllers" . "/" . $entity . ".controller";
        $result['model'] = "models" . "/" . $entity . ".model";
        $result['dto'] = "dtos" . "/" . $entity . ".dto";
        $result['interface'] = "interfaces" . "/" . $entity . ".interface";
        $result['sql'] = "sqls" . "/" . $entity . ".sql";
        return $result;
    }
}

if (!function_exists("generateNameFor")) {
    function generateNameFor($entity)
    {
        $cappedName = ucfirst($entity);
        $result['mRoute'] = $cappedName . 'Route';
        $result['mController'] = $cappedName . 'Controller';
        $result['tController'] = $entity . 'Controller';
        $result['mModel'] =  $cappedName . 'Model';
        $result['tModel'] =  $entity . 'Model';
        $result['mDto'] = $cappedName . 'Dto';
        $result['mInterface'] = $cappedName;
        $result['filterInterface'] = $cappedName . 'Filter';
        $result['mSqls'] = $cappedName . 'Sqls';
        $result['tSqls'] = $entity . 'Sqls';
        return $result;
    }
}

if (!function_exists("generateImportsFor")) {
    function generateImportsFor($entity, $pathTo, $nameFor)
    {
        $result['controller'] = 'import ' . $nameFor['mController'] . ' from \'@' . $pathTo['controller'] . '\';';
        $result['model'] = 'import ' . $nameFor['mModel'] . ' from \'@' . $pathTo['model'] . '\';';
        $result['dto'] = 'import { ' . $nameFor['mDto'] . ' } from \'@' . $pathTo['dto'] . '\';';
        $result['interface'] = 'import ' . $nameFor['mInterface'] . ' from \'@' . $pathTo['interface'] . '\';';
        $result['sql'] = 'import { ' . $nameFor['mSqls'] . ' } from \'@' . $pathTo['sql'] . '\';';
        return $result;
    }
}

if (!function_exists("getRouteData")) {
    function getRouteData($resourseName, $nameFor, $imports, $doAuthentication)
    {
        $cappedName = ucfirst($resourseName);
        $authMiddleware = '';
        if ($doAuthentication) {
            $authMiddleware = 'import authMiddleware from \'../middlewares/auth.middleware\';';
        }

        $data = ''
            . 'import { Router } from \'express\';
import Routes from \'@mis/interfaces/routes.interface\';

' . $authMiddleware . '
import validationMiddleware from \'../middlewares/validation.middleware\';
' . $imports['controller'] . '
' . $imports['dto'] . '
		
class ' . $nameFor['mRoute'] . ' implements Routes {
	public path = \'/' . $resourseName . '\';
	public router = Router();
	public ' . $nameFor['tController'] . ' = new ' . $nameFor['mController'] . '();
		
	constructor(version="") {
		this.initializeRoutes(version);
	}
		
	private initializeRoutes(version) {
		this.router.get(`${version}${this.path}`,' . (($doAuthentication) ? ' authMiddleware,' : '') . ' this.' . $nameFor['tController'] . '.get' . $cappedName . ');
		this.router.get(`${version}${this.path}/:id(\\\d+)`,' . (($doAuthentication) ? ' authMiddleware,' : '') . ' this.' . $nameFor['tController'] . '.get' . $cappedName . 'ById);
		this.router.post(`${version}${this.path}`,' . (($doAuthentication) ? ' authMiddleware,' : '') . ' validationMiddleware(' . $nameFor['mDto'] . ', \'body\'), this.' . $nameFor['tController'] . '.create' . $cappedName . ');
		this.router.put(`${version}${this.path}/:id(\\\d+)`,' . (($doAuthentication) ? ' authMiddleware,' : '') . ' validationMiddleware(' . $nameFor['mDto'] . ', \'body\', true), this.' . $nameFor['tController'] . '.update' . $cappedName . ');
		this.router.put(`${version}${this.path}/where`,' . (($doAuthentication) ? ' authMiddleware,' : '') . ' validationMiddleware(' . $nameFor['mDto'] . ', \'body\', true), this.' . $nameFor['tController'] . '.update' . $cappedName . 'Where);
        this.router.delete(`${version}${this.path}/:id(\\\d+)`,' . (($doAuthentication) ? ' authMiddleware,' : '') . ' this.' . $nameFor['tController'] . '.delete' . $cappedName . ');
        this.router.delete(`${version}${this.path}/where`,' . (($doAuthentication) ? ' authMiddleware,' : '') . ' this.' . $nameFor['tController'] . '.delete' . $cappedName . 'Where);
	}
}
		
export default ' . $nameFor['mRoute'] . ';
';
        return $data;
    }
}

if (!function_exists("getModelData")) {
    function getModelData($resourseName, $params, $imports, $nameFor, $pathTo, $options = false)
    {
        $cappedResourseName = ucfirst($resourseName);
        $tableName = $resourseName;
        if (!empty($options)) {
            if (!empty($options['tableName'])) {
                $tableName = $options['tableName'];
            }
        }
        $parameterDeclarations = '';
        $parameterInitializations = '';
        $whereConditions = '';

        if (!empty($params)) {
            foreach ($params as $param) {

                $parameterDeclarations .= ('  
private ' . ($param->fieldName ? $param->fieldName : $param->name) . ': ' . $param->type . ';');
                $parameterInitializations .= ('
' . '       ' . $resourseName . 'Data.' . $param->name . ' ? this.' . ($param->fieldName ? $param->fieldName : $param->name) . ' = ' . $resourseName . 'Data.' . $param->name . ' : "";');
                $whereConditions .= ('
        if (filterQuery.' . $param->name . ') {
            whereCondition.push(`' . $tableName . '.' . ($param->fieldName ? $param->fieldName : $param->name) . ' = ' . (($param->type == "number") ? '' : '\'') . '${filterQuery.' . $param->name . '}' . (($param->type == "number") ? '' : '\'') . '`);
        }
                ');
            }
        }

        $data = ''
            . 'import { MysqlResponse, MysqlService } from \'@mis/services/mysql.service\';
import { isEmpty } from \'@mis/utils\';
import { ' . $nameFor['mInterface'] . ', ' . $nameFor['filterInterface'] . ' } from \'@' . $pathTo['interface'] . '\';
' . $imports['sql'] . '

const tableName = "' . $tableName . '";
const mysqlService = new MysqlService();
const ' . $nameFor['tSqls'] . ' = new ' . $nameFor['mSqls'] . '();

class ' . $nameFor['mModel'] . ' {
'
            .
            $parameterDeclarations . '

constructor(' . $resourseName . 'Data?: ' . $nameFor['mInterface'] . ') {
    if (' . $resourseName . 'Data) {
'.
        $parameterInitializations
            . '    }
    }

public async create' . $cappedResourseName . '(' . $resourseName . 'Data: ' . $nameFor['mModel'] . '): Promise<MysqlResponse> {
    const create' . $cappedResourseName . 'Query = `INSERT INTO ${tableName} SET ?`;
    const ' . $resourseName . 'Inserted: MysqlResponse = await mysqlService.query(create' . $cappedResourseName . 'Query, ' . $resourseName . 'Data);
    return ' . $resourseName . 'Inserted;
}

private getWhereConditionsFor(filterQuery: ' . $nameFor['filterInterface'] . ') {
    let whereSqls = \'\';
    let whereCondition: string[] = [];
    if (!isEmpty(filterQuery)) {

        ' . $whereConditions . '
        whereSqls = whereCondition.join(" AND ");
    }

    return whereSqls;
}

public async get' . $cappedResourseName . 'ById(' . $resourseName . 'Id: number): Promise<MysqlResponse> {
    const select' . $cappedResourseName . 'Query = ' . $nameFor['tSqls'] . '.detailSelect;
    const modifiedSelect' . $cappedResourseName . 'Query = select' . $cappedResourseName . 'Query + `WHERE ' . $tableName . '.id = ${' . $resourseName . 'Id}`;
    const ' . $resourseName . 'Selected: MysqlResponse = await mysqlService.query(modifiedSelect' . $cappedResourseName . 'Query);
    return ' . $resourseName . 'Selected;
}

public async getAll' . $cappedResourseName . '(filterQuery: ' . $nameFor['filterInterface'] . '): Promise<MysqlResponse[]> {
    let limitSql = \'\';
    let offset = filterQuery?.offset ?? 0;
    let limit = filterQuery?.limit ?? 0;
    let orderBy = ``;

    let whereSqls = this.getWhereConditionsFor(filterQuery);
    whereSqls = whereSqls ? ` WHERE ` + whereSqls : \'\';

    if(limit){
        limitSql = ` limit ${offset}, ${limit}`;
    }

    let ' . $resourseName . 'Selected: MysqlResponse[]=[];
    const countSelect' . $cappedResourseName . 'Query = ' . $nameFor['tSqls'] . '.countselect;
    const modifiedCountSelect' . $cappedResourseName . 'Query = countSelect' . $cappedResourseName . 'Query + `${whereSqls} `;
    ' . $resourseName . 'Selected[0] = await mysqlService.query(modifiedCountSelect' . $cappedResourseName . 'Query);
    const select' . $cappedResourseName . 'Query = ' . $nameFor['tSqls'] . '.generalSelect;
    const modifiedSelect' . $cappedResourseName . 'Query = select' . $cappedResourseName . 'Query + `${whereSqls} ${orderBy} ${limitSql}`;
    ' . $resourseName . 'Selected[1] = await mysqlService.query(modifiedSelect' . $cappedResourseName . 'Query);
    return ' . $resourseName . 'Selected;
}

public async update' . $cappedResourseName . 'ById(' . $resourseName . 'Id: number, ' . $resourseName . 'Data: ' . $nameFor['mModel'] . '): Promise<MysqlResponse> {
    const update' . $cappedResourseName . 'Query = `UPDATE ${tableName} SET ? WHERE ' . $tableName . '.id = ${' . $resourseName . 'Id}`;
    const ' . $resourseName . 'Updated: MysqlResponse = await mysqlService.query(update' . $cappedResourseName . 'Query, ' . $resourseName . 'Data);
    return ' . $resourseName . 'Updated;
}

public async update' . $cappedResourseName . 'Where(filterQuery: ' . $nameFor['filterInterface'] . ', ' . $resourseName . 'Data: ' . $nameFor['mModel'] . '): Promise<MysqlResponse> {
    let whereSqls = this.getWhereConditionsFor(filterQuery);
    whereSqls = ` WHERE ` + (whereSqls ? whereSqls : \'false\');

    const update' . $cappedResourseName . 'Query = `DELETE FROM ${tableName} ${whereSqls}`;
    const ' . $resourseName . 'Updated: MysqlResponse = await mysqlService.query(update' . $cappedResourseName . 'Query, ' . $resourseName . 'Data);
    return ' . $resourseName . 'Updated;
}

public async delete' . $cappedResourseName . '(' . $resourseName . 'Id: number): Promise<MysqlResponse> {
    const delete' . $cappedResourseName . 'Query = `DELETE FROM ${tableName} WHERE ' . $tableName . '.id = ${' . $resourseName . 'Id}`;
    const ' . $resourseName . 'Deleted: MysqlResponse = await mysqlService.query(delete' . $cappedResourseName . 'Query);
    return ' . $resourseName . 'Deleted;
}

public async delete' . $cappedResourseName . 'Where(filterQuery: ' . $nameFor['filterInterface'] . '): Promise<MysqlResponse> {
    let whereSqls = this.getWhereConditionsFor(filterQuery);
    whereSqls = ` WHERE ` + (whereSqls ? whereSqls : \'false\');

    const delete' . $cappedResourseName . 'Query = `DELETE FROM ${tableName} ${whereSqls}`;
    const ' . $resourseName . 'Deleted: MysqlResponse = await mysqlService.query(delete' . $cappedResourseName . 'Query);
    return ' . $resourseName . 'Deleted;
}

public async deleteAll' . $cappedResourseName . '(): Promise<MysqlResponse> {
    const delete' . $cappedResourseName . 'sQuery = `DELETE * FROM ${tableName}`;
    const ' . $resourseName . 'sDeleted: MysqlResponse = await mysqlService.query(delete' . $cappedResourseName . 'sQuery);
    return ' . $resourseName . 'sDeleted;
}

}
export default ' . $nameFor['mModel'] . ';';
        return $data;
    }
}

if (!function_exists("getDtoData")) {
    function getDtoData($resourseName, $params, $imports, $nameFor)
    {
        $parameterDeclarations = '';

        if (!empty($params)) {
            foreach ($params as $param) {
                $parameterDeclarations .= ('
	public ' . $param->name . '?: ' . $param->type . ';
			');
            }
        }

        $data = ''
            . 'export class ' . $nameFor['mDto'] . ' {
' . $parameterDeclarations . '

}
';
        return $data;
    }
}

if (!function_exists("getInterfaceData")) {
    function getInterfaceData($resourseName, $params, $imports, $nameFor)
    {
        $parameterDeclarations = '';
        if (!empty($params)) {
            foreach ($params as $param) {
                $parameterDeclarations .= ('	' . $param->name . '?: ' . $param->type . ';
');
            }
        }

        $data = ''
            . 'export interface ' . $nameFor['mInterface'] . ' {
' . $parameterDeclarations . '}
  ';

        $data .= '
  '
            . 'export interface ' . $nameFor['filterInterface'] . ' {
' . $parameterDeclarations . '	offset?: number;
	limit?: number;
}
';
        return $data;
    }
}

if (!function_exists("getSqlData")) {
    function getSqlData($resourseName, $params, $imports, $nameFor, $options)
    {
        $tableName = $resourseName;
        if (!empty($options)) {
            if (!empty($options['tableName'])) {
                $tableName = $options['tableName'];
            }
        }

        $selectDeclarations = '';

        if (!empty($params)) {
            foreach ($params as $param) {

                $selectDeclarations .= ('
                ' . $tableName . '.' . $param->fieldName . ' AS ' . $param->name . ',');
            }
        }

        $selectDeclarations = substr_replace($selectDeclarations, "", -1);

        $data = ''
            . 'export class ' . $nameFor['mSqls'] . ' {
    public countselect: string = `SELECT 
    count(' . $tableName . '.id) AS totalResults  
    FROM 
    ' . $tableName . ' `;
    public generalSelect: string = `SELECT ' . $selectDeclarations . ' 
    FROM 
    ' . $tableName . ' `;
    public detailSelect: string = `SELECT ' . $selectDeclarations . '
     FROM 
     ' . $tableName . ' `;
}
  ';
        return $data;
    }
}

if (!function_exists("getControllerData")) {
    function getControllerData($resourseName, $params, $imports, $nameFor, $pathTo)
    {
        $cappedResourseName = ucfirst($resourseName);

        $data = ''
            . 'import { NextFunction, Request, Response } from \'express\';
import { InternalServerError, ResultsNotFoundError } from \'@/mis/dtos/customerrors.dto\';
import { ERROR_MSGS_GENERAL } from \'@/mis/constants/errors.enum\';
import {
    mysqlManyToResultMany,
    mysqlOneToResultOne,
    resolveMultipleMysqlSelect,
    resolveMysqlCreate,
    resolveMysqlModifications,
    resolveSingleMysqlSelect,
  } from \'@/mis/helpers/mysql.helper\';
import { FunctionResult } from \'@/mis/dtos/functionresult.dto\';

import { ' . $nameFor['mInterface'] . ', ' . $nameFor['filterInterface'] . ' } from \'@' . $pathTo['interface'] . '\';
' . $imports['model'] . '

class ' . $nameFor['mController'] . ' {
private ' . $nameFor['tModel'] . ' = new ' . $nameFor['mModel'] . '();

public create' . $cappedResourseName . 'Func = async (' . $resourseName . ': ' . $nameFor['mInterface'] . '): Promise<FunctionResult> => {
  try {
    let result = new FunctionResult();
    result.status = false;
    const ' . $resourseName . 'Data = new ' . $nameFor['mModel'] . '(' . $resourseName . ');
    const insert' . $cappedResourseName . 'Data = await this.' . $nameFor['tModel'] . '.create' . $cappedResourseName . '(' . $resourseName . 'Data);
    const insert' . $cappedResourseName . 'DataResolved = resolveMysqlCreate(insert' . $cappedResourseName . 'Data);
    if (insert' . $cappedResourseName . 'DataResolved?.status) {
      result.status = true;
      if (insert' . $cappedResourseName . 'DataResolved?.insertId) {
        return this.get' . $cappedResourseName . 'ByIdFunc(insert' . $cappedResourseName . 'DataResolved?.insertId);
      }
    }
    return result;
  } catch (error) {
    throw error;
  }
};

public get' . $cappedResourseName . 'Func = async (' . $resourseName . 'Filter: ' . $nameFor['filterInterface'] . '): Promise<FunctionResult> => {
  try {
    let result = new FunctionResult();
    result.status = false;
    const findAll' . $cappedResourseName . 'Data = await this.' . $nameFor['tModel'] . '.getAll' . $cappedResourseName . '(' . $resourseName . 'Filter);
    const findAll' . $cappedResourseName . 'DataResolved = resolveMultipleMysqlSelect(findAll' . $cappedResourseName . 'Data);
    if (findAll' . $cappedResourseName . 'DataResolved?.status) {
      result.status = true;
      result.data = findAll' . $cappedResourseName . 'DataResolved;
    }
    return result;
  } catch (error) {
    throw error;
  }
};

public get' . $cappedResourseName . 'ByIdFunc = async (' . $resourseName . 'Id: string | number): Promise<FunctionResult> => {
  try {
    let result = new FunctionResult();
    result.status = false;
    ' . $resourseName . 'Id = Number(' . $resourseName . 'Id);
    const findOne' . $cappedResourseName . 'Data = await this.' . $nameFor['tModel'] . '.get' . $cappedResourseName . 'ById(' . $resourseName . 'Id);
    const findOne' . $cappedResourseName . 'DataResolved = resolveSingleMysqlSelect(findOne' . $cappedResourseName . 'Data);
    if (findOne' . $cappedResourseName . 'DataResolved?.status) {
      result.status = true;
      result.data = findOne' . $cappedResourseName . 'DataResolved;
    }
    return result;
  } catch (error) {
    throw error;
  }
};

public update' . $cappedResourseName . 'Func = async (' . $resourseName . 'Id: string | number, ' . $resourseName . 'Data: ' . $nameFor['mInterface'] . '): Promise<FunctionResult> => {
  try {
    let result = new FunctionResult();
    result.status = false;
    ' . $resourseName . 'Id = Number(' . $resourseName . 'Id);
    const ' . $resourseName . 'Object = new ' . $nameFor['mModel'] . '(' . $resourseName . 'Data);
    const update' . $cappedResourseName . 'Data = await this.' . $nameFor['tModel'] . '.update' . $cappedResourseName . 'ById(' . $resourseName . 'Id, ' . $resourseName . 'Object);
    const update' . $cappedResourseName . 'DataResolved = resolveMysqlModifications(update' . $cappedResourseName . 'Data);
    if (update' . $cappedResourseName . 'DataResolved?.status) {
      result.status = true;
      if (update' . $cappedResourseName . 'DataResolved?.result?.affectedRows > 0) {
        return this.get' . $cappedResourseName . 'ByIdFunc(' . $resourseName . 'Id);
      }
    }
    return result;
  } catch (error) {
    throw error;
  }
};
  
public update' . $cappedResourseName . 'WhereFunc = async (' . $resourseName . 'Filter: ' . $nameFor['filterInterface'] . ', ' . $resourseName . 'Data: ' . $nameFor['mInterface'] . '): Promise<FunctionResult> => {
  try {
    let result = new FunctionResult();
    result.status = false;
    const ' . $resourseName . 'Object = new ' . $nameFor['mModel'] . '(' . $resourseName . 'Data);
    const update' . $cappedResourseName . 'Data = await this.' . $nameFor['tModel'] . '.update' . $cappedResourseName . 'Where(' . $resourseName . 'Filter, ' . $resourseName . 'Object);
    const update' . $cappedResourseName . 'DataResolved = resolveMysqlModifications(update' . $cappedResourseName . 'Data);
    if (update' . $cappedResourseName . 'DataResolved?.status) {
      result.status = true;
      result.data = update' . $cappedResourseName . 'DataResolved.result;
    }
    return result;
  } catch (error) {
    throw error;
  }
};
 
public delete' . $cappedResourseName . 'Func = async (' . $resourseName . 'Id: string | number): Promise<FunctionResult> => {
  try {
    let result = new FunctionResult();
    result.status = false;
    ' . $resourseName . 'Id = Number(' . $resourseName . 'Id);
    const delete' . $cappedResourseName . 'Data = await this.' . $nameFor['tModel'] . '.delete' . $cappedResourseName . '(' . $resourseName . 'Id);
    const delete' . $cappedResourseName . 'DataResolved = resolveMysqlModifications(delete' . $cappedResourseName . 'Data);
    if (delete' . $cappedResourseName . 'DataResolved?.status) {
      result.status = true;
      result.data = delete' . $cappedResourseName . 'DataResolved.result;
    }
    return result;
  } catch (error) {
    throw error;
  }
};
    
public delete' . $cappedResourseName . 'WhereFunc = async (' . $resourseName . 'Filter: ' . $nameFor['filterInterface'] . '): Promise<FunctionResult> => {
  try {
    let result = new FunctionResult();
    result.status = false;
    const delete' . $cappedResourseName . 'Data = await this.' . $nameFor['tModel'] . '.delete' . $cappedResourseName . 'Where(' . $resourseName . 'Filter);
    const delete' . $cappedResourseName . 'DataResolved = resolveMysqlModifications(delete' . $cappedResourseName . 'Data);
    if (delete' . $cappedResourseName . 'DataResolved?.status) {
      result.status = true;
      result.data = delete' . $cappedResourseName . 'DataResolved.result;
    }
    return result;
  } catch (error) {
    throw error;
  }
};

public create' . $cappedResourseName . ' = async (req: Request, res: Response, next: NextFunction): Promise<void> => {
  try {
    let ' . $resourseName . ' = req.body as ' . $nameFor['mInterface'] . ';
    const insert' . $cappedResourseName . 'Data = await this.create' . $cappedResourseName . 'Func(' . $resourseName . ');
    if (insert' . $cappedResourseName . 'Data?.status) {
      next({ data: mysqlOneToResultOne(insert' . $cappedResourseName . 'Data.data) });
    } else {
      throw new InternalServerError({ message: ERROR_MSGS_GENERAL?.CREATION_FAIL });
    }
  } catch (error) {
   next({ error: error });
  }
};

public get' . $cappedResourseName . ' = async (req: Request, res: Response, next: NextFunction): Promise<void> => {
  try {
    const ' . $resourseName . 'Filter: ' . $nameFor['filterInterface'] . ' = req.query as ' . $nameFor['filterInterface'] . ';
    const findAll' . $cappedResourseName . 'Data = await this.get' . $cappedResourseName . 'Func(' . $resourseName . 'Filter);
    if (findAll' . $cappedResourseName . 'Data?.status) {
      next({ data: mysqlManyToResultMany(findAll' . $cappedResourseName . 'Data?.data) });
    } else {
      throw new ResultsNotFoundError({});
    }
  } catch (error) {
    next({ error: error });
  }
};

public get' . $cappedResourseName . 'ById = async (req: Request, res: Response, next: NextFunction): Promise<void> => {
  try {
    const ' . $resourseName . 'Id = req.params.id;
    const findOne' . $cappedResourseName . 'Data = await this.get' . $cappedResourseName . 'ByIdFunc(' . $resourseName . 'Id);
    if (findOne' . $cappedResourseName . 'Data?.status) {
      next({ data: mysqlOneToResultOne(findOne' . $cappedResourseName . 'Data?.data) });
    } else {
      throw new ResultsNotFoundError({});
    }
  } catch (error) {
   next({ error: error });
  }
};

public update' . $cappedResourseName . ' = async (req: Request, res: Response, next: NextFunction): Promise<void> => {
  try {
    const ' . $resourseName . 'Id = req.params.id;
    const ' . $resourseName . 'Data = req.body as ' . $nameFor['mInterface'] . ';
    const update' . $cappedResourseName . 'Data = await this.update' . $cappedResourseName . 'Func(' . $resourseName . 'Id, ' . $resourseName . 'Data);
    if (update' . $cappedResourseName . 'Data?.status) {
      next({ data: mysqlOneToResultOne(update' . $cappedResourseName . 'Data.data) });
    } else {
      throw new InternalServerError({ message: ERROR_MSGS_GENERAL?.UPDATION_FAIL });
    }
  } catch (error) {
    next({ error: error });
  }
};

public update' . $cappedResourseName . 'Where = async (req: Request, res: Response, next: NextFunction): Promise<void> => {
  try {
    const ' . $resourseName . 'Data = req.body as ' . $nameFor['mInterface'] . ';
    const ' . $resourseName . 'Filter: ' . $nameFor['filterInterface'] . ' = req.query as ' . $nameFor['filterInterface'] . ';
    const update' . $cappedResourseName . 'Data = await this.update' . $cappedResourseName . 'WhereFunc(' . $resourseName . 'Filter, ' . $resourseName . 'Data);
    if (update' . $cappedResourseName . 'Data?.status) {
      next({ data: update' . $cappedResourseName . 'Data.data });
    } else {
      throw new InternalServerError({ message: ERROR_MSGS_GENERAL?.UPDATION_FAIL });
    }
  } catch (error) {
    next({ error: error });
  }
};

public delete' . $cappedResourseName . ' = async (req: Request, res: Response, next: NextFunction): Promise<void> => {
  try {
    const ' . $resourseName . 'Id = req.params.id;
    const delete' . $cappedResourseName . 'Data = await this.delete' . $cappedResourseName . 'Func(' . $resourseName . 'Id);
    if (delete' . $cappedResourseName . 'Data?.status) {
      next({ data: delete' . $cappedResourseName . 'Data?.data });
    } else {
      throw new InternalServerError({ message: ERROR_MSGS_GENERAL?.DELETION_FAIL });
    }
  } catch (error) {
    next({ error: error });
  }
};

public delete' . $cappedResourseName . 'Where = async (req: Request, res: Response, next: NextFunction): Promise<void> => {
  try {
    const ' . $resourseName . 'Filter: ' . $nameFor['filterInterface'] . ' = req.query as ' . $nameFor['filterInterface'] . ';
    const delete' . $cappedResourseName . 'Data = await this.delete' . $cappedResourseName . 'WhereFunc(' . $resourseName . 'Filter);
    if (delete' . $cappedResourseName . 'Data?.status) {
      next({ data: delete' . $cappedResourseName . 'Data?.data });
    } else {
      throw new InternalServerError({ message: ERROR_MSGS_GENERAL?.DELETION_FAIL });
    }
  } catch (error) {
    next({ error: error });
  }
};
}

export default ' . $nameFor['mController'] . ';
';
        return $data;
    }
}
