<?php namespace Quallsbenson\Repository\RepositoryManagerBundle\Symfony;

use Symfony\Component\Yaml\Yaml;
use Quallsbenson\Repository\RepositoryManagerBundle\Database\DatabaseManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Quallsbenson\Repository\RepositoryManager as BaseRepoManager;

class RepositoryManager extends BaseRepoManager implements ContainerAwareInterface{


	protected $Container, $bundle;

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

		return $this->getServicesFromConfig( $name );

	}
	

	protected function getServicesFromConfig( $name )
	{

		$serviceKey = $this->getBundleName().".".$name.".repository_services";
		$app        = $this->getContainer();

		$services   = $app->hasParameter( $serviceKey ) ? $app->getParameter( $serviceKey ) : [];
		
		$configuredServices  = [];

		foreach( $services as $key => $service )
		{

			$configuredServices[ $key ] = $app->get( $service );

		}

		return $configuredServices;

	}

	/**
	* set the name of the bundle
	**/


	public function setBundleName( $name )
	{

		$this->bundle = (string) $name;
		return $this;

	}

	/**
	* get the name of the bundle
	**/


	public function getBundleName()
	{

		return $this->bundle;

	}


}