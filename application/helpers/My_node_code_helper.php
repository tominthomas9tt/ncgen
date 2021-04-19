
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
        // checkOrCreateDirectory($buildPath . "interfaces");
    }
}

if (!function_exists("generatePathsFor")) {
    function generatePathsFor($entity)
    {
        $result['route'] = "routes" . "/" . $entity . "s.route";
        $result['controller'] = "controllers" . "/" . $entity . "s.controller";
        $result['model'] = "models" . "/" . $entity . "s.model";
        $result['dto'] = "dtos" . "/" . $entity . "s.dto";
        $result['interface'] = "interfaces" . "/" . $entity . "s.interface";
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
        $result['mSqls'] = $cappedName . 'Sqls';
        $result['tSqls'] = $entity . 'Sqls';
        return $result;
    }
}

if (!function_exists("generateImportsFor")) {
    function generateImportsFor($entity, $pathTo, $nameFor)
    {
        $result['controller'] = 'import ' . $nameFor['mController'] . ' from \'../' . $pathTo['controller'] . '\';';
        $result['model'] = 'import ' . $nameFor['mModel'] . ' from \'../' . $pathTo['model'] . '\';';
        $result['dto'] = 'import { ' . $nameFor['mDto'] . ' } from \'../' . $pathTo['dto'] . '\';';
        $result['interface'] = 'import ' . $nameFor['mInterface'] . ' from \'../' . $pathTo['interface'] . '\';';
        $result['sql'] = 'import { ' . $nameFor['mSqls'] . ' } from \'../' . $pathTo['sql'] . '\';';
        return $result;
    }
}

if (!function_exists("getRouteData")) {
    function getRouteData($resourseName, $nameFor, $imports)
    {
        $cappedName = ucfirst($resourseName);
        $data = ''
            . 'import { Router } from \'express\';
import Route from \'../interfaces/routes.interface\';
import validationMiddleware from \'../middlewares/validation.middleware\';
' . $imports['controller'] . '
' . $imports['dto'] . '
		
class ' . $nameFor['mRoute'] . ' implements Route {
	public path = \'/' . $resourseName . 's\';
	public router = Router();
	public ' . $nameFor['tController'] . ' = new ' . $nameFor['mController'] . '();
		
	constructor() {
		this.initializeRoutes();
	}
		
	private initializeRoutes() {
		this.router.get(`${this.path}`, this.' . $nameFor['tController'] . '.get' . $cappedName . 's);
		this.router.get(`${this.path}/:id(\\\d+)`, this.' . $nameFor['tController'] . '.get' . $cappedName . 'ById);
		this.router.post(`${this.path}`, validationMiddleware(' . $nameFor['mDto'] . ', \'body\'), this.' . $nameFor['tController'] . '.create' . $cappedName . ');
		this.router.put(`${this.path}/:id(\\\d+)`, validationMiddleware(' . $nameFor['mDto'] . ', \'body\', true), this.' . $nameFor['tController'] . '.update' . $cappedName . ');
		this.router.delete(`${this.path}/:id(\\\d+)`, this.' . $nameFor['tController'] . '.delete' . $cappedName . ');
	}
}
		
export default ' . $nameFor['mRoute'] . ';
';
        return $data;
    }
}

if (!function_exists("getModelData")) {
    function getModelData($resourseName, $params, $imports, $nameFor, $options = false)
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

        if (!empty($params)) {
            foreach ($params as $param) {
                $parameterDeclarations .= ('  
public ' . ($param->fieldName ? $param->fieldName : $param->name) . ': ' . $param->type . ';');
                $parameterInitializations .= ('
' . '       ' . $resourseName . 'Data.' . $param->name . ' ? this.' . ($param->fieldName ? $param->fieldName : $param->name) . ' = ' . $resourseName . 'Data.' . $param->name . ' : "";');
            }
        }

        $data = ''
            . $imports['dto'] . '
' . $imports['sql'] . '
' . 'import { MysqlResponse, MysqlService } from \'../services/mysql.service\';

const tableName = "' . $tableName . 's";
const mysqlService = new MysqlService();
const ' . $nameFor['tSqls'] . ' = new ' . $nameFor['mSqls'] . '();

class ' . $nameFor['mModel'] . ' {
'
            .
            $parameterDeclarations . '

constructor(' . $resourseName . 'Data?: ' . $cappedResourseName . 'Dto) {
    if (' . $resourseName . 'Data) {
' .
            $parameterInitializations
            . '    }
    }

public async create' . $cappedResourseName . '(' . $resourseName . 'Data: ' . $nameFor['mModel'] . '): Promise<MysqlResponse> {
    const create' . $cappedResourseName . 'Query = `INSERT INTO ${tableName} SET ?`;
    const ' . $resourseName . 'Inserted: MysqlResponse = await mysqlService.create(create' . $cappedResourseName . 'Query, ' . $resourseName . 'Data);
    if (' . $resourseName . 'Inserted && ' . $resourseName . 'Inserted.status && ' . $resourseName . 'Inserted.insertId) {
        return this.get' . $cappedResourseName . 'ById(' . $resourseName . 'Inserted.insertId);
    }
    return ' . $resourseName . 'Inserted;
}

public async update' . $cappedResourseName . 'ById(' . $resourseName . 'Id: number, ' . $resourseName . 'Data: ' . $nameFor['mModel'] . '): Promise<MysqlResponse> {
    const update' . $cappedResourseName . 'Query = `UPDATE ${tableName} SET ? WHERE id = ${' . $resourseName . 'Id}`;
    const ' . $resourseName . 'Updated: MysqlResponse = await mysqlService.create(update' . $cappedResourseName . 'Query, ' . $resourseName . 'Data);
    if (' . $resourseName . 'Updated && ' . $resourseName . 'Updated.status && ' . $resourseName . 'Updated.affectedRows && ' . $resourseName . 'Updated.affectedRows > 0) {
        return this.get' . $cappedResourseName . 'ById(' . $resourseName . 'Id);
    }
    return ' . $resourseName . 'Updated;
}

public async getAll' . $cappedResourseName . 's(): Promise<MysqlResponse> {
    let whereSqls = \'\';
    let limitSql = \'\';
    let offset = 0;
    let limit = 0;

    if(limit){
        limitSql = \' limit \' + offset + \',\' + limit;
    }
    const select' . $cappedResourseName . 'Query = ' . $nameFor['tSqls'] . '.generalSelect;
    const modifiedSelect' . $cappedResourseName . 'Query = select' . $cappedResourseName . 'Query + `${whereSqls} ${limitSql}`;
    const ' . $resourseName . 'Selected: MysqlResponse = await mysqlService.create(modifiedSelect' . $cappedResourseName . 'Query);
    return ' . $resourseName . 'Selected;
}

public async get' . $cappedResourseName . 'ById(' . $resourseName . 'Id: number): Promise<MysqlResponse> {
    const select' . $cappedResourseName . 'Query = ' . $nameFor['tSqls'] . '.detailSelect;
    const modifiedSelect' . $cappedResourseName . 'Query = select' . $cappedResourseName . 'Query + `WHERE id = ${' . $resourseName . 'Id}`;
    const ' . $resourseName . 'Selected: MysqlResponse = await mysqlService.create(modifiedSelect' . $cappedResourseName . 'Query);
    return ' . $resourseName . 'Selected;
}

public async delete' . $cappedResourseName . '(' . $resourseName . 'Id: number): Promise<MysqlResponse> {
    const delete' . $cappedResourseName . 'Query = `DELETE FROM ${tableName} WHERE id = ${' . $resourseName . 'Id}`;
    const ' . $resourseName . 'Deleted: MysqlResponse = await mysqlService.create(delete' . $cappedResourseName . 'Query);
    return ' . $resourseName . 'Deleted;
}

public async deleteAll' . $cappedResourseName . 's(): Promise<MysqlResponse> {
    const delete' . $cappedResourseName . 'sQuery = `DELETE * FROM ${tableName}`;
    const ' . $resourseName . 'sDeleted: MysqlResponse = await mysqlService.create(delete' . $cappedResourseName . 'sQuery);
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
	public ' . $param->name . ': ' . $param->type . ';
			');
            }
        }

        $data = ''
            . '
import { IsNotEmpty, IsString } from \'class-validator\';

export class ' . $nameFor['mDto'] . ' {
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
                $parameterDeclarations .= ('	' . $param->name . ': ' . $param->type . ';
');
            }
        }

        $data = ''
            . 'export interface ' . $nameFor['mInterface'] . ' {
' . $parameterDeclarations . '}
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

        $data = ''
            . 'export class ' . $nameFor['mSqls'] . ' {
    public generalSelect: string = "SELECT * FROM ' . $tableName . ' ";
    public detailSelect: string = "SELECT * FROM ' . $tableName . ' ";
}
  ';
        return $data;
    }
}

if (!function_exists("getControllerData")) {
    function getControllerData($resourseName, $params, $imports, $nameFor)
    {
        $cappedResourseName = ucfirst($resourseName);

        $data = ''
            . 'import { NextFunction, Request, Response } from \'express\';
import MisException from \'../exceptions/MisException\';
' . $imports['model'] . '

class ' . $nameFor['mController'] . ' {
public ' . $nameFor['tModel'] . ' = new ' . $nameFor['mModel'] . '();

public get' . $cappedResourseName . 's = async (req: Request, res: Response, next: NextFunction): Promise<void> => {
    try {
        const findAll' . $cappedResourseName . 'sData = await this.' . $nameFor['tModel'] . '.getAll' . $cappedResourseName . 's();
        if (findAll' . $cappedResourseName . 'sData && findAll' . $cappedResourseName . 'sData.status && findAll' . $cappedResourseName . 'sData.result && findAll' . $cappedResourseName . 'sData.result.length > 0) {
            const result: any = findAll' . $cappedResourseName . 'sData.result;
            next({ status: 1, data: result })
        } else {
            next({ status: 0, error: new MisException("", "' . $resourseName . 's not found") });
        }
    } catch (error) {
        next({ status: 0, error: error });
    }
};

public get' . $cappedResourseName . 'ById = async (req: Request, res: Response, next: NextFunction): Promise<void> => {
    try {
        const ' . $resourseName . 'Id = Number(req.params.id);

        const findOne' . $cappedResourseName . 'Data = await this.' . $nameFor['tModel'] . '.get' . $cappedResourseName . 'ById(' . $resourseName . 'Id);
        if (findOne' . $cappedResourseName . 'Data && findOne' . $cappedResourseName . 'Data.status && findOne' . $cappedResourseName . 'Data.result && findOne' . $cappedResourseName . 'Data.result.length > 0) {
            const result: any = findOne' . $cappedResourseName . 'Data.result;
            next({ status: 1, data: result[0] })
        } else {
            next({ status: 0, error: new MisException("", "' . $resourseName . ' not found") });
        }
    } catch (error) {
        next({ status: 0, error: error });
    }
};

public create' . $cappedResourseName . ' = async (req: Request, res: Response, next: NextFunction): Promise<void> => {
    try {
        const ' . $resourseName . 'Data = new ' . $cappedResourseName . 'Model(req.body);
        const insert' . $cappedResourseName . 'Data = await this.' . $nameFor['tModel'] . '.create' . $cappedResourseName . '(' . $resourseName . 'Data);
        if (insert' . $cappedResourseName . 'Data && insert' . $cappedResourseName . 'Data.status && insert' . $cappedResourseName . 'Data.result && insert' . $cappedResourseName . 'Data.result.length > 0) {
            const inserted' . $cappedResourseName . 's: any = insert' . $cappedResourseName . 'Data.result;
            next({ status: 1, data: inserted' . $cappedResourseName . 's[0] })
        } else {
            next({ status: 0, error: new MisException("", "' . $resourseName . ' not created") });
        }
    } catch (error) {
        next({ status: 0, error: error });
    }
};

public update' . $cappedResourseName . ' = async (req: Request, res: Response, next: NextFunction): Promise<void> => {
    try {
        const ' . $resourseName . 'Id = Number(req.params.id);
        const ' . $resourseName . 'Data = new ' . $cappedResourseName . 'Model(req.body);

        const update' . $cappedResourseName . 'Data = await this.' . $nameFor['tModel'] . '.update' . $cappedResourseName . 'ById(' . $resourseName . 'Id, ' . $resourseName . 'Data);
        if (update' . $cappedResourseName . 'Data && update' . $cappedResourseName . 'Data.status && update' . $cappedResourseName . 'Data.result && update' . $cappedResourseName . 'Data.result.length > 0) {
            const updated' . $cappedResourseName . 's: any = update' . $cappedResourseName . 'Data.result;
            next({ status: 1, data: updated' . $cappedResourseName . 's[0] })
        } else {
            next({ status: 0, error: new MisException("", "' . $resourseName . ' not updated") });
        }
    } catch (error) {
        next({ status: 0, error: error });
    }
};

public delete' . $cappedResourseName . ' = async (req: Request, res: Response, next: NextFunction): Promise<void> => {
    try {
        const ' . $resourseName . 'Id = Number(req.params.id);

        const delete' . $cappedResourseName . 'Data = await this.' . $nameFor['tModel'] . '.delete' . $cappedResourseName . '(' . $resourseName . 'Id);
        if (delete' . $cappedResourseName . 'Data && delete' . $cappedResourseName . 'Data.status && delete' . $cappedResourseName . 'Data.affectedRows && delete' . $cappedResourseName . 'Data.affectedRows > 0) {
            next({ status: 1, infoDtls: { message: "' . $resourseName . ' deleted" } })
        } else {
            next({ status: 0, error: new MisException("", "' . $resourseName . ' not deleted") });
        }
    } catch (error) {
        next({ status: 0, error: error });
    }
};
}

export default ' . $nameFor['mController'] . ';
';
        return $data;
    }
}