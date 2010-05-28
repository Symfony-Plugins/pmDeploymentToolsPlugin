<?php

class deploymentAllTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'propel'),
      new sfCommandOption('output-dir', null, sfCommandOption::PARAMETER_OPTIONAL, 'The prepare task\'s output directory', '/tmp'),
      new sfCommandOption('dir', null, sfCommandOption::PARAMETER_OPTIONAL, 'The patch task\'s patch directory', sfConfig::get('sf_data_dir').DIRECTORY_SEPARATOR.'patch'),
    ));

    $this->namespace        = 'deployment';
    $this->name             = 'all';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [deployment:all|INFO] task executes all deployment tasks:

  [./symfony deployment:all|INFO]

The task is equivalent to:

  [./symfony deployment:prepare|INFO]
  [./symfony deployment:patch|INFO]
  [./symfony deployment:finalize|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    $dpt_options = array('--connection='.$options['connection'], '--output-dir='.$options['output-dir']);
    $task = new deploymentPrepareTask($this->dispatcher, $this->formatter);
    $task->setCommandApplication($this->commandApplication);
    $task->run(array(), $dpt_options);

    $dpt_options = array('--connection='.$options['connection'], '--dir='.$options['dir']);
    $task = new deploymentPatchTask($this->dispatcher, $this->formatter);
    $task->setCommandApplication($this->commandApplication);
    $task->run(array(), $dpt_options);

    $task = new deploymentFinalizeTask($this->dispatcher, $this->formatter);
    $task->setCommandApplication($this->commandApplication);
    $task->run();
  }
}
