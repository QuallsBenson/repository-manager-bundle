<?php namespace Designplug\Repository\RepositoryManagerBundle\Database;

use Designplug\Illuminate\Database\DatabaseManager as Manager;

class DatabaseManager extends manager{

  public function setConnectionParameters(array $param){
/*
  	$param['database']  = $param['database_name'];
  	$param['driver']    = $param['database_driver'];
  	$param['host']	    = $param['database_host'];
  	$param['username']  = $param['database_user'];
  	$param['password']  = $param['database_password'];
 */ 	
  	$param['charset']   = 'utf8';
  	$param['collation'] = 'utf8_unicode_ci';
  	$param['prefix']	= @$param['database_prefix'] ?: "";


    $this->connectionParameters = array_merge($this->connectionParameters, $param);

  }

  public function setConnectionParam($name, $driver, $host, $user, $password){

  	$this->setConnectionParameters( array('name'     => $name,
  										  'driver'   => $driver,
  										  'host'     => $host,
  										  'username' => $user,
  										  'password' => $password ) );

  }

}