<?php namespace Quallsbenson\Repository\RepositoryManagerBundle\Symfony;

use Symfony\Component\Yaml\Yaml;
use Quallsbenson\Repository\RepositoryManagerBundle\Database\DatabaseManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Quallsbenson\Repository\RepositoryManager as BaseRepoManager;

class RepositoryManager extends BaseRepoManager implements ContainerAwareInterface{


	protected $Container;

	/**
	*
	*  set the container 
	*  @return null
	*
	**/

	public function setContainer( ContainerInterface $container = null )
	{

		return $this->Container = $container;

	}


	/**
	*
	*  Get the container 
	*  @return Symfony\Component\DependencyInjection\ContainerInterface
	*
	**/

	public function getContainer()
	{

		return $this->Container;

	}


	public function getInitializationServices( $name )
	{

		return $this->getContainer()->getParameter( $name.".services" ) ?: [];

	}


}