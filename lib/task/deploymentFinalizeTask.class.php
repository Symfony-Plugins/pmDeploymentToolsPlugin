<?php

class deploymentFinalizeTask extends sfBaseTask
{
  protected function configure()
  {
    $this->namespace        = 'deployment';
    $this->name             = 'finalize';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [deployment:finalize|INFO] task finalizes the project upgrade:

  [./symfony deployment:finalize|INFO]

The task is equivalent to:

  [./symfony propel:build-model|INFO]
  [./symfony propel:build-forms|INFO]
  [./symfony propel:build-filters|INFO]
  [./symfony project:permissions|INFO]
  [./symfony cache:clear|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // ./symfony propel:build-model
    $task = new sfPropelBuildModelTask($this->dispatcher, $this->formatter);
    $task->setCommandApplication($this->commandApplication);
    $task->run();

    // ./symfony propel:build-forms
    $task = new sfPropelBuildFormsTask($this->dispatcher, $this->formatter);
    $task->setCommandApplication($this->commandApplication);
    $task->run();

    // ./symfony propel:build-filters
    $task = new sfPropelBuildFiltersTask($this->dispatcher, $this->formatter);
    $task->setCommandApplication($this->commandApplication);
    $task->run();

    // ./symfony project:permissions
    $task = new sfProjectPermissionsTask($this->dispatcher, $this->formatter);
    $task->setCommandApplication($this->commandApplication);
    $task->run();

    // ./symfony cache:clear
    $task = new sfCacheClearTask($this->dispatcher, $this->formatter);
    $task->setCommandApplication($this->commandApplication);
    $task->run();
  }
}
