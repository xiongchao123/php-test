<?php

namespace App\Server;

use App\Lib\Utility\IO\ConfigHandler;
use App\Lib\Utility\IO\FileHandler;

class Connection
{
    private static $_connectorTable;
    private static $_checkTable;
    
    /**
     * 创建连接池缓存table
     */
    public function createConnectorTable()
    {
        //创建表格 行数参数大小得为2的指数
        self::$_connectorTable = new \swoole_table(131072); 
    
        // 表字段
        self::$_connectorTable->column('userId', \swoole_table::TYPE_INT, 8); // 1,2,4,8
        self::$_connectorTable->column('connectionType', \swoole_table::TYPE_INT, 2);
        self::$_connectorTable->column('createTime', \swoole_table::TYPE_STRING, 20);
    
        self::$_connectorTable->create();
    }
    
    /**
     * 创建待检池缓存table
     */
    public function createCheckTable()
    {
        //创建表格 行数参数大小得为2的指数
        self::$_checkTable = new \swoole_table(65536);
    
        // 表字段
        self::$_checkTable->column('createTime', \swoole_table::TYPE_STRING, 20);
    
        self::$_checkTable->create();
    }
    
    /**
     * 获取连接信息
     * @param int $fd
     * 连接id
     * @return array
     */
    public function getConnector($fd)
    {
        $result = self::$_connectorTable->get("$fd");
        if(!$result)
        {
        	$result = array();
        }
        return $result;
    }
    
    /**
     * 根据连接id获取用户id
     * @param int $fd
     * @return int
     */
    public function getConnectorUserId($fd)
    {
        $connector = $this->getConnector($fd);
        if(! empty($connector))
        {
            return intval($connector["userId"]);
        }
        return 0;
    }
    
    /**
     * 根据连接id获取连接类型
     * @param int $fd
     * @return int
     */
    public function getConnectionType($fd)
    {
        $connector = $this->getConnector($fd);
        if(! empty($connector))
        {
            return intval($connector["connectionType"]);
        }
        return CommonDefine::CONNECTION_TYPE_SOCKET;
    }
    
    /**
     * 根据连接id检查该连接是否合法
     * @param int $fd
     * @return boolean
     */
    public function isValidConnector($fd)
    {
        $userId = $this->getConnectorUserId($fd);
        return $userId > 0;
    }
    
    /**
     * 保存当前连接到连接池
     * @param int $fd
     * @param int $connectionType
     * @param int $userId
     */
    public function saveConnector($fd, $connectionType = 0, $userId = 0)
    {
        $arr = $this->getConnector($fd);
        
        if($connectionType > 0)
        {
            $arr["connectionType"] = $connectionType;
        }
        if(! array_key_exists("createTime", $arr))
        {
            $arr["createTime"] = time();
        }
        
        $userId = intval($userId);
        if($userId > 0)
        {
            // 保存用户信息
            $flag = (new User())->saveUser($fd, $userId, $connectionType);
            if($flag)
            {
                $arr["userId"] = $userId;
            }
        }
        
        // 保存连接
        self::$_connectorTable->set("$fd",$arr);
    }
    
    /**
     * 将连接从连接池中移除，并移除用户信息
     * @param int $fd
     */
    public function removeConnector($fd)
    {
        // 移除用户信息
        $userId = $this->getConnectorUserId($fd);
        (new User())->removeUser($userId);
        
        // 移除连接池
        self::$_connectorTable->del("$fd");
    }
    
    /**
     * 验证用户 包括加密解密验证、token验证、时效性验证
     * @param string $token
     * 登录凭证
     * @return boolean
     */
    public function validateConnector($token)
    {
        // TODO 根据实际情况对token进行验证，这里直接通过
        return true;
    }

    /**
     * 清理非法连接
     *
     * @param swoole_server $serv            
     */
    public function clearInvalidConnection($serv)
    {
        echo "check count0: ".count(self::$_checkTable)."\n";
        foreach (self::$_checkTable as $key=>$value)
        {
            $connector = $this->getConnector($key);
            if(empty($connector))
            {
            	//连接不在连接池，从待检池移除并关闭连接
                self::$_checkTable->del("$key");
                $serv->close($key);
                continue;
            }
            else if(intval($connector["userId"]) > 0 || !$serv->exist($key))
            {
                //已进行身份验证或者连接已不存在从待检池移除
                self::$_checkTable->del("$key");
                continue;
            }
            $createTime = $connector["createTime"];
            if($createTime < strtotime("-10 seconds"))
            {
                //过期，从待检池移除并关闭连接
                self::$_checkTable->del("$key");
                $serv->close($key);
                continue;
            }
            
        }
        echo "check count1: ".count(self::$_checkTable)."\n";
    }
    
    /**
     * 保存连接到待检池
     * @param int $fd
     */
    public function saveCheckConnector($fd)
    {
    	$arr=array(
    		"createTime"=>time()
    	);
    	self::$_checkTable->set("$fd", $arr);
    }

    /**
     * 清理数据
     */
    public function clearData()
    {
        $connectorDir=ConfigHandler::getLocalConfigs("connectorDir");
        FileHandler::deleteFiles($connectorDir);
    }
    
    
}