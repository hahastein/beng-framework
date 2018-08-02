<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-08-02
 * Time: 14:25
 */

namespace bengbeng\framework\base\data;


class ActiveOperate
{

    private $where;
    private $params;

    public function __construct()
    {
        $this->where = '';
        $this->params = null;
    }

    /**
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @return string|array
     */
    public function getWhere()
    {
        return $this->where;
    }

    /**
     * @param array $params
     */
    public function params($params)
    {
        $this->params = $params;
    }

    /**
     * @param mixed $where
     */
    public function where($where)
    {
        $this->where = $where;
    }
}