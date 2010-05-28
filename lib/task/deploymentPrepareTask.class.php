<?php

class deploymentPrepareTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'propel'),
      new sfCommandOption('output-dir', null, sfCommandOption::PARAMETER_OPTIONAL, 'The output directory', '/tmp'),
    ));

    $this->namespace        = 'deployment';
    $this->name             = 'prepare';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [deployment:prepare|INFO] task prepares the application for being upgraded:

  [./symfony deployment:prepare|INFO]
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

    $output = $options['output-dir'].DIRECTORY_SEPARATOR.date('Ymdhis').'_'.$dbname.'.sql';

    $this->logSection('mysqldump', 'dumping database');
    system("mysqldump --opt $username$password $dbname > $output");
    $this->logSection('mysqldump', "database dumped to $output");
  }
}
