<?php namespace Designplug\Repository\RepositoryManagerBundle\Command;

require_once dirname(dirname(__FILE__)).'/vendor/autoload.php';

use Designplug\Repository\CLI\Command\GenerateCommand as BaseCommand;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends BaseCommand implements ContainerAwareInterface{

	protected $container, 
			  $configOptions,
			  $bundleName,
			  $repoName;

	public function setContainer(ContainerInterface $container = null){

		$this->container = $container;

	}

	public function setConfigurationOptions($templatePath, $repositoryNamespace, $modelNamespace, $repositoryInitializerNamespace){

		$options = array('templatePath' 	   			  => $templatePath,
						 'repositoryNamespace' 			  => $repositoryNamespace,
						 'modelNamespace'	   			  => $modelNamespace,
						 'repositoryInitializerNamespace' => $repositoryInitializerNamespace);

		$this->configOptions = $options;

	}

	protected function getConfigurationOptions($repositoryName, InputInterface $input, OutputInterface $output){

	    $options 		 			= $this->configOptions;
	    $options['name'] 			= $this->repoName;
	    $options['generationPath']	= $this->container->get( 'kernel' )->getRootDir();

	    return $options;

	}

	protected function setRepositoryName($name){

		$name = explode(":", $name);
		$this->bundleName = $name[0];
		$this->repoName   = $name[1];

	}

	protected function execute(InputInterface $input, OutputInterface $output){

		$name = $input->getArgument('name');

		$this->setRepositoryName( $name );

		//throws error on failure
		$bundle = $this->container->get( 'kernel' )->getBundle( $this->bundleName );

		if($bundle) parent::execute($input, $output);

	}	


}