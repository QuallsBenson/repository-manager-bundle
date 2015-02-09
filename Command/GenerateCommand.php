<?php namespace Designplug\Repository\RepositoryManagerBundle\Command;

use Designplug\Repository\CLI\Command\GenerateCommand as BaseCommand;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;


class GenerateCommand extends BaseCommand implements ContainerAwareInterface{

	protected $container,
			  $configOptions,
			  $bundleName,
			  $repositoryName;

	public function setContainer(ContainerInterface $container = null){

		$this->container = $container;

	}

	public function setConfigurationOptions(array $options){

		$this->configOptions = $options;

	}

	public function getBundleName(){

		return $this->bundleName;

	}

	public function getRepositoryName(){

		return $this->repositoryName;

	}

	protected function formatParameters(array $parameters){

		$formattedParam = array();

		foreach($parameters as $key => $value) {

			//camel case each parameter key
			$key = preg_replace_callback('/_([a-z])/', function($c){

				  return strtoupper($c[1]);

			}, $key );

			//save the new key value pair in formatted array
			$formattedParam[$key] =  $value;

		}

		return $formattedParam;
	}

	protected function getConfigurationFileContents(){

		//get bundle parameters
		$path   = $this->container->get( 'kernel' )->locateResource("@{$this->bundleName}/Resources/config/services.yml");
		$config = Yaml::parse( file_get_contents($path) );

		$param  = @$config['parameters'] ?: array();

		if(empty($param) || !isset($param['dp_repo.generator'])){
			throw new \Exception("Repository Generation Parameters not found in @{$this->bundleName}/Resources/config/services.yml ");
		}

		//build options array with parameters
		$options = $this->formatParameters( $param['dp_repo.generator'] );

		return $options;

	}

	protected function getConfigurationOptions(){

			//override default options
		  $this->configOptions 				 = array_merge( $this->configOptions, $this->getConfigurationFileContents() );
 			$this->configOptions['name'] = $this->getRepositoryName();

      //set src directory to default if generation path not set
			if(!isset( $this->configOptions['generationPath'] ))
					$this->configOptions['generationPath'] = $this->container->get( 'kernel' )->getRootDir() .'/../src';

			return $this->configOptions;

	}

	protected function setRepositoryName($name){

		$name = explode(":", $name);
		$this->bundleName = trim($name[0]);
		$this->repoName   = trim($name[1]);

	}

	protected function execute(InputInterface $input, OutputInterface $output){

		$name = $input->getArgument('name');

		$this->setRepositoryName( $name );

		//give an error if user attempts to build repo in this dir
		if($this->getBundleName() === 'DPRepoBundle') throw new \Exception('Cannot create Repository in DPRepoBundle');

		//throws error on failure
		$bundle = $this->container->get( 'kernel' )->getBundle( $this->getBundleName() );

		if($bundle) parent::execute($input, $output);

	}


}
