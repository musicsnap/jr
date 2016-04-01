<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-9-21
 * Time: 上午9:57
 */


class BaseModel extends Zend_Db_Table {

    private $_table;
    protected $_name = '';
    protected $_primary = 'id';
    protected $_isbug = false;


    /**
     * 插入数据
     * @param $data
     * @return int
     */
    public function add($data) {
        $this->_table = new self ();
        $db = $this->_table->getAdapter ();

        $db->insert( $this->_name, $data );
        return $productId = $this->_db->lastInsertId($this->_name);
    }

    /**
     * 更新单条数据
     * @param $id
     * @param $field
     * @return int
     */
    public function updateById($id, $field) {
        $this->_table = new self ();
        $db = $this->_table->getAdapter ();
        return $db->update ( $this->_name, $field, "id= {$id}" );
    }

    /**
     * 删除一条数据
     * @param $where
     * @return int
     */
    public function del($where) {
        $this->_table = new self ();
        $db = $this->_table->getAdapter ();
        return $db->delete($this->_name,$where);
    }


    /**
     * 更新单条数据根据条件
     * @param $where
     * @param $field
     * @return int
     */
    public function updateByWhere($where, $field){
        $this->_table = new self ();
        $db = $this->_table->getAdapter ();
        return $db->update ( $this->_name, $field, $where);
    }


    /**
     * 查询一条记录
     * @param $row
     * @param $content
     * @return array
     */
    public function getlineByRow($row, $content) {
        $this->_table = new self ();
        $db = $this->_table->getAdapter ();
        $sql = $db->select ()->from ( $this->_name )->where ( "`{$row}`='{$content}'"  )->__toString ();
        if ($this->_isbug == true) {
            echo  __METHOD__.": ".$sql . "<hr/>";
        }
        return $db->fetchRow ( $sql );
    }


    public function getListById($id) {
        $this->_table = new self ();
        $db = $this->_table->getAdapter ();
        $sql = $db->select ()->from ( $this->_name )->where ( "id='{$id}'" )->__toString ();
        if ($this->_isbug == true) {
            echo  __METHOD__.": ".$sql . "<hr/>";
        }
        return $db->fetchRow ( $sql );
    }


    /**
     * function：根据Id和数据的列名字取得一个特定字段的数据
     * argument：$id 行数据的Id $name列名称
     * return：取得数据
     * User:
     * Date: 2015/4/2
     * Time: 17:35
     */
    public function getOneByIdAndName($id, $name) {
        if(!$id){
            return null;
        }
        $this->_table = new self ();
        $db = $this->_table->getAdapter ();

        $sql = $db->select ()->from ( $this->_name, $name )->where ( "id={$id}" )->__toString ();
        if ($this->_isbug == true) {
            echo  __METHOD__.": ".$sql . "<hr/>";
        }
        return $db->fetchOne ( $sql );
    }

    /**
     * 获取列表
     * @return array
     */
    public function getList() {
        $this->_table = new self ();
        $db = $this->_table->getAdapter ();
        $sql = $db->select ()->from ( $this->_name )->__toString ();
        if ($this->_isbug == true) {
            echo  __METHOD__.": ".$sql . "<hr/>";
        }
        return $db->fetchAll ( $sql );
    }

    /**
     * 跟进条件查询列表,条件名row，条件值centent
     * @param $row
     * @param $content
     * @param $orderby
     * @return array
     */
    public function getListByRow($row, $content,$orderby="id desc") {
        $this->_table = new self ();
        $db = $this->_table->getAdapter ();
        $sql = $db->select ()->from ( $this->_name )->where ( "`{$row}`='{$content}'" )->order($orderby)->__toString ();
        if ($this->_isbug == true) {
            echo  __METHOD__.": ".$sql . "<hr/>";
        }
        return $db->fetchAll ( $sql );
    }

    /**
     * 查询是否某个值是否存在
     * @param $row
     * @param $content
     * @return string
     */
    public function isExistByRow($row, $content) {
        $this->_table = new self ();
        $db = $this->_table->getAdapter ();
        $sql = $db->select ()->from ( $this->_name, $row )->where ( "`{$row}`='{$content}'" )->__toString ();
        if ($this->_isbug == true) {
            echo  __METHOD__.": ".$sql . "<hr/>";
        }
        return $db->fetchOne ( $sql );
    }

    /**
     * 多种条件查询是否存在
     * @param $row
     * @param $content
     * @param $id
     * @return string
     */
    public function isExistByRowExcludeRowById($row, $content, $id) {
        $this->_table = new self ();
        $db = $this->_table->getAdapter ();
        $sql = $db->select ()->from ( $this->_name, $row )->where ( "`{$row}`='{$content}' AND `id` != '{$id}'" )->__toString ();
        if ($this->_isbug == true) {
            echo  __METHOD__.": ".$sql . "<hr/>";
        }
        return $db->fetchOne ( $sql );
    }


    /**
     * 取得总数
     * @return string
     */
    public function getCount() {
        $this->_table = new self ();
        $db = $this->_table->getAdapter ();
        $sql = $db->select ()->from ( $this->_name, "count(*)" )->__toString ();
        if ($this->_isbug == true) {
            echo  __METHOD__.": ".$sql . "<hr/>";
        }
        return $db->fetchOne ( $sql );
    }

    /**
     * 取得当前数据表共有多少行数据
     * @param $page
     * @param $rowCount
     * @return array
     */
    public function getPageList($page,$rowCount) {
        $this->_table = new self ();
        $db = $this->_table->getAdapter ();
        $sql = $db->select ()->from ( $this->_name )->__toString ();
        $sql .= " limit ".(($page-1))*$rowCount.",".$rowCount;
        if ($this->_isbug == true) {
            echo  __METHOD__.": ".$sql . "<hr/>";
        }
        return $db->fetchAll($sql);
    }


    /**
     * 取得当前数据表共有多少行数据
     * @param $where
     * @return string
     */
    public function getCountByWhere($where) {
        $this->_table = new self ();
        $db = $this->_table->getAdapter ();
        if(!$where){
            $where=" 1=1 ";
        }
        $sql = $db->select ()->from ( $this->_name, "count(*)" )->where($where)->__toString ();
        if ($this->_isbug == true) {
            echo  __METHOD__.": ".$sql . "<hr/>";
        }
        return $db->fetchOne ( $sql );
    }


    /**
     * function：取得当前数据表共有多少行数据
     * argument：$page 取得当前页  $rowCount 需要取得的行数
     * return：返回取得的数据
     * User:
     * Date: 2015/4/9
     * Time: 11:35
     */
    public function getPageListByWhere($start=0,$count=0,$where ="1=1",$order ="id DESC") {
        $this->_table = new self ();
        $db = $this->_table->getAdapter ();
        if(!$where){
            $where='1=1';
        }
        $sql = $db->select ()->from ( $this->_name )->where($where)->__toString ();
        if($order){
            $sql.= " order  by ".$order;
        }
        if($count){
            $sql .= " limit {$start} , {$count} ";
        }

        if ($this->_isbug == true) {
            echo  __METHOD__.": ".$sql . "<hr/>";
        }

        return $db->fetchAll($sql);
    }

    /**
     * 获取产品
     * @param $where
     */
    public function getRowByWhere($where){
        $this->_table = new self ();
        $db = $this->_table->getAdapter ();
        $sql = $db->select ()->from ( $this->_name )->where($where)->__toString ();
        if ($this->_isbug == true) {
            echo  __METHOD__.": ".$sql . "<hr/>";
        }
        return $db->fetchRow($sql);
    }

    /**
     * @param $id
     * @param $fields 这个是数组，是要查询的字段信息
     * @return null|string
     */
    public function getFieldById($id, $fields) {
        $this->_table = new self ();
        $db = $this->_table->getAdapter ();

        $sql = $db->select ()->from ( $this->_name, $fields )->where ( "id={$id}" )->__toString ();
        if ($this->_isbug == true) {
            echo  __METHOD__.": ".$sql . "<hr/>";
        }
        return $db->fetchOne ( $sql );
    }

    /**
     * @param $where
     * @param $field
     * @param string $type
     * @return array|string
     */
    public function getListByWhere($where,$field,$type='all'){
        $this->_table = new self ();
        $db = $this->_table->getAdapter ();
        $sql = $db->select ()->from ( $this->_name ,$field)->where($where)->__toString ();

        if ($this->_isbug == true) {
            echo  __METHOD__.": ".$sql . "<hr/>";
        }
        if($type == 'one'){
            $data = $db->fetchOne($sql);;
        }else if($type == 'row'){
            $data = $db->fetchRow($sql);;
        }else{
            $data = $db->fetchAll($sql);;
        }
        return $data;
    }


    /*
     * function  取得数据库连接
     * @return 数据库连接
     * */
    public function getDB(){
        $this->_table = new self ();
        return $db = $this->_table->getAdapter ();
    }



}