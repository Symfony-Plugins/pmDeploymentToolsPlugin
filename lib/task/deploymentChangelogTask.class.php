<?php

class deploymentChangelogTask extends sfBaseTask
{
  protected function configure()
  {
    // add your own arguments here
    $this->addArguments(array(
      new sfCommandArgument('version', sfCommandArgument::REQUIRED, 'Project version'),
    ));

    // add your own options here
    $this->addOptions(array(
      // from is head and to is 1, so revisions are ordered descending by revision
      new sfCommandOption('from', null, sfCommandOption::PARAMETER_REQUIRED, '', 'HEAD'),
      new sfCommandOption('to', null, sfCommandOption::PARAMETER_REQUIRED, '', '1'),
      new sfCommandOption('format', null, sfCommandOption::PARAMETER_REQUIRED, '', ' * [%%revision%%]: (%%author%%) %%msg%%'),
      new sfCommandOption('message', null, sfCommandOption::PARAMETER_REQUIRED, 'message'),
    ));

    $this->namespace        = 'deployment';
    $this->name             = 'changelog';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [deployment:changelog|INFO] task generates the Changelog, based on the subversion log:

  [./symfony deployment:changelog|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    $version = $arguments['version'];
    $from = $options['from'];
    $to = $options['to'];
    $format = $options['format'];
    $message = $options['message'];

    system("svn log --xml -r $from:$to > /tmp/svn_changelog");

    $xml = simplexml_load_file('/tmp/svn_changelog');

    $data = file_exists('CHANGELOG') ? file_get_contents('CHANGELOG') : '';

    $handle = fopen('CHANGELOG', 'w');

    $title = date('d/m/Y').": Versión $version\n";

    fwrite($handle, $title);
    for ($i = 1; $i <= strlen($title) - 2; $i++) fwrite($handle, "-");
    fwrite($handle, "\n\n");

    if (!empty($message))
    {
      fwrite($handle, $message."\n\n");
    }

    foreach ($xml->logentry as $logentry)
    {
      $text = $format;
      $text = preg_replace('/%%revision%%/', $logentry['revision'], $text);
      $text = preg_replace('/%%author%%/', $logentry->{'author'}, $text);
      $text = preg_replace('/%%msg%%/', $logentry->{'msg'}, $text);
      fwrite($handle, $text."\n");
    }

    fwrite($handle, "\n\n".$data);

    fclose($handle);
  }
}
