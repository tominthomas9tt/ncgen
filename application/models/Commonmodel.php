<?php

class Commonmodel extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function putData($table, $data)
    {
        $status = $this->db->insert($table, $data);
        if ($status) {
            $result['insert_id'] = $this->db->insert_id();
        }
        $result['status'] = $status;
        return $result;
    }

    public function setData($table, $where, $data)
    {
        $this->db->where($where);
        $result = $this->db->update($table, $data);
        return $result;
    }

    public function deleteData($table, $where)
    {
        $this->db->where($where);
        $result = $this->db->delete($table);
        return $result;
    }

    public function getRawQueryData($sql)
    {
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return $result;
    }

    public function getOneRawQueryData($sql)
    {
        $query = $this->db->query($sql);
        $result = $query->row_array();
        return $result;
    }

    public function getDatas($params)
    {
        if (!empty($params['distinct'])) {
            $this->db->distinct($params['distinct']);
        }
        if (!empty($params['select'])) {
            $this->db->select($params['select']);
        }
        if (!empty($params['where'])) {
            $this->db->where($params['where']);
        }
        if (!empty($params["where_in"])) {
            $where_in = $params["where_in"];
            $this->db->where_in($where_in["column"], $where_in["datas"]);
        }
        if (!empty($params['likes'])) {
            $likes = $params['likes'];
            foreach ($likes as $like) {
                $wildcard = !empty($like['wildcards']) ? $like['wildcards'] : "both";
                $this->db->like($like['column'], $like['value'], $wildcard);
            }
        }
        if (!empty($params['from_table'])) {
            $this->db->from($params['from_table']);
        }
        if (!empty($params['joins'])) {
            $joins = $params['joins'];
            foreach ($joins as $join) {
                $this->db->join($join['table'], $join['condition'], $join['type']);
            }
        }
        if (!empty($params['limit'])) {
            $limit = $params['limit'];
            $offset = !empty($limit['offset']) ? $limit['offset'] : 0;
            $this->db->limit($limit['limit'], $offset);
        }
        if (!empty($params['order_by'])) {
            $order_by = $params['order_by'];
            $this->db->order_by($order_by['column'], $order_by['order']);
        }
        if (!empty($params['order_bys'])) {
            $order_bys = $params["order_bys"];
            foreach ($order_bys as $order_by) {
                $this->db->order_by($order_by['column'], $order_by['order']);
            }
        }
        if (!empty($params['group_by'])) {
            $group_bys = $params['group_by'];
            foreach ($group_bys as $group_by) {
                $this->db->group_by($group_by);
            }
        }
        $query = $this->db->get();
        if (!empty($params['id'])) {
            $result = $query->row_array();
        } else {
            $result = $query->result();
        }
        return $result;
    }

    public function transBegin()
    {
        $this->db->trans_begin();
    }

    public function transStatus()
    {
        return $this->db->trans_status();
    }

    public function transRollback()
    {
        $this->db->trans_rollback();
    }

    public function transCommit()
    {
        $this->db->trans_commit();
    }

    public function getAllTables()
    {
        $tabResults = $this->db->select('TABLE_NAME')
            ->from('INFORMATION_SCHEMA.TABLES')
            ->where('TABLE_SCHEMA',$this->db->database)
            ->get()->result_array();
        $tables = array_column($tabResults, 'TABLE_NAME');
        return $tables;
    }

    public function getTableStructure($tableName)
    {
        $fields = $this->db->field_data($tableName);
        return $fields;
    }
}
