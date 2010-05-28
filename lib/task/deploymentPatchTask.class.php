<?php

class deploymentPatchTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'propel'),
      new sfCommandOption('dir', null, sfCommandOption::PARAMETER_OPTIONAL, 'The patch directory', sfConfig::get('sf_data_dir').DIRECTORY_SEPARATOR.'patch'),
    ));

    $this->namespace        = 'deployment';
    $this->name             = 'patch';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [deployment:patch|INFO] task patches the database with the sql files in the selected directory:

  [./symfony deployment:patch|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $database = $databaseManager->getDatabase($options['connection'] ? $options['connection'] : null);
    $connection = $database->getConnection();

    $username = '-u'.$database->getParameterHolder()->get('username');
    $password = $database->getParameterHolder()->get('password')?' -p'.$database->getParameterHolder()->get('password'):'';

    $dsn = $database->getParameterHolder()->get('dsn');
    $dbname = substr($dsn, strpos($dsn, 'dbname=') + strlen('dbname='));
    $dbname = substr($dbname, 0, strpos($dbname, ';'));

    $finder = sfFinder::type('file')->name('*.sql')->in($options['dir']);

    $this->logSection('patch', 'patching database');
    foreach ($finder as $sql)
    {
      system("mysql $username$password $dbname < $sql");
      $this->logSection('patch+', "database patched with $sql");
    }
  }
}
