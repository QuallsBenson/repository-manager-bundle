<?php namespace Quallsbenson\Repository\RepositoryManagerBundle\Symfony;

use Symfony\Component\Yaml\Yaml;
use Quallsbenson\Repository\RepositoryManagerBundle\Database\DatabaseManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Quallsbenson\Repository\RepositoryManagerBundle\Symfony\RepositoryManager as RepoManager;

class RepositoryManagerConfigurator implements ContainerAwareInterface{


	protected $RepositoryManagers = [],
			  $databaseManager,
			  $Container;


	public function __construct( DatabaseManager $databaseManager )
	{

		$this->setDatabaseManager( $databaseManager );

	}	


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

	/**
	*
	*  Gets an instance of Repository Object with bundle configuration
	*  @return Quallsbenson\Repository\Repository
	*
	**/

	public function get( $bundleRepository )
	{

		$parts  = explode( ":", $bundleRepository );

		$bundle = $parts[0];
		$repo   = $parts[1];


		//get manager for bundle
		$manager = $this->getManager( $bundle );

		//return the repository from the new bundle
		return $manager->get( $repo );

	}

	/**
	*
	*  Gets the Repository Manager for the bundle, attempts to create one if not set 
	*  @return Quallsbenson\Repository\RepositoryManager
	*
	**/

	public function getManager( $bundle )
	{

		//return repository manager if bundle configuration already set
		if(isset( $this->RepositoryManagers[$bundle] ))
			return $this->RepositoryManagers[$bundle];

		//get configuration for bundle
		$config  = $this->getConfig( $bundle ); 

		//create new manager for the bundle
		$manager = $this->makeRepositoryManager($bundle, $config);

		//set the database manager for the repository manager
		$manager->setDatabaseManager( $this->getDatabaseManager() );

		return $manager;

	}


	/**
	*
	*	gets the contents of services.yml confugration file for the bundle,
	*   @return array 
	*
	*/


	public function getConfig( $bundle )
	{

		$file   = $this->Container->get( 'kernel' )->locateResource("@{$bundle}/Resources/config/services.yml");
		$config = Yaml::parse( file_get_contents($file) );

		return  @$config['parameters'] ?: array();

	}

	/**
	*
	*	sets the database manager to be used by repositories
	*   @return null
	*
	*/

	public function setDatabaseManager( DatabaseManager $manager )
	{

		$this->DatabaseManager = $manager;

	}

	/**
	*
	*	gets the database manager to be used by repositories
	*   @return Quallsbenson\Repository\RepositoryManagerBundle\Database\DatabaseManager
	*
	*/

	public function getDatabaseManager()
	{

		return $this->DatabaseManager;

	}

	/**
	*
	*	instantiates repository manager for the bundle then returns it
	*   @return Quallsbenson\Repository\RepositoryManager
	*
	*/

	protected function makeRepositoryManager($bundle, $config)
	{

		$manager  = new RepoManager($config['qb_repo.repository.autoload_namespace'], 
									$config['qb_repo.model.autoload_namespace'], 
								    $config['qb_repo.repository_initializer.autoload_namespace'] );


		$manager->setContainer( $this->getContainer() );

		$manager->setBundleName( $bundle );


		return $this->RepositoryManagers[$bundle] = $manager;


	}



}