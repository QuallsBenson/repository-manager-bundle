<?php namespace Designplug\Repository\RepositoryManagerBundle\Command;

require_once dirname(dirname(__FILE__)).'/vendor/autoload.php';

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
			  $repoName;

	public function setContainer(ContainerInterface $container = null){

		$this->container = $container;

	}

	public function setConfigurationOptions($templatePath, $repositoryNamespace, $modelNamespace, $repositoryInitializerNamespace){

		$options = array('templatePath' 	   			  => $templatePath 		  				?: @$this->configOptions['templatePath'],
						 'repositoryNamespace' 			  => $repositoryNamespace 				?: @$this->configOptions['repositoryNamespace'],
						 'modelNamespace'	   			  => $modelNamespace 	  				?: @$this->configOptions['modelNamespace'],
						 'repositoryInitializerNamespace' => $repositoryInitializerNamespace	?: @$this->configOptions['repositoryInitializerNamespace']);

		$this->configOptions = $options;

		return $this->configOptions;

	}

	protected function overrideDefaultConfiguration( $options ){

		//get bundle param
		$path   = $this->container->get( 'kernel' )->locateResource("@{$this->bundleName}/Resources/config/services.yml");
		$config = Yaml::parse( file_get_contents($path) ); 
						
		//if(!isset($config['parameters'])) return $options;

		$p = @$config['parameters'] ?: array();


		if(!isset($p['dp_repo.config.repository_namespace']) ||
		   !isset($p['dp_repo.config.model_namespace'])      ||
		   !isset($p['dp_repo.config.repository_initializer_namespace']))
				throw new \Exception('Cannot create Repository: Bundle configuration not set');

		$override = $this->setConfigurationOptions( @$p['dp_repo.config.template_path'], 
											   		@$p['dp_repo.config.repository_namespace'],
											   		@$p['dp_repo.config.model_namespace'],
											   		@$p['dp_repo.config.repository_initializer_namespace'] );

		return array_merge( $options, $override );



	}

	protected function getConfigurationOptions($repositoryName, InputInterface $input, OutputInterface $output){

	    $this->configOptions['name'] 			= $this->repoName;
	    $this->configOptions['generationPath']	= $this->container->get( 'kernel' )->getRootDir() .'/../src';

		//load specified bundle config to override default options if it exists

	    return $this->overrideDefaultConfiguration( $this->configOptions );

	}

	protected function setRepositoryName($name){

		$name = explode(":", $name);
		$this->bundleName = $name[0];
		$this->repoName   = $name[1];

	}

	protected function execute(InputInterface $input, OutputInterface $output){

		$name = $input->getArgument('name');

		$this->setRepositoryName( $name );

		//give an error if user attempts to build repo in this dir
		if($this->bundleName === 'DPRepoBundle') throw new \Exception('Cannot create Repository in DPRepoBundle');

		//throws error on failure
		$bundle = $this->container->get( 'kernel' )->getBundle( $this->bundleName );

		if($bundle) parent::execute($input, $output);

	}	


}