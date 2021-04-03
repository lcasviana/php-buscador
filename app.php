<?php

require 'vendor/autoload.php';

use App\Result;
use App\ResultItem;

use App\Engine\Wikipedia\WikipediaEngine;
use App\Engine\Wikipedia\WikipediaParser;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\Table;

class SearchCommand extends Command
{
  protected function configure()
  {
    $this->setName('search')->addArgument('term', InputArgument::REQUIRED);
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $term = $input->getArgument('term');
    $wikipedia = new WikipediaEngine(new WikipediaParser(), HttpClient::create());
    $result = $wikipedia->search($term);

    $header = ''.$result->count().' result(s) was found for term "'.$term.'" on Wikipedia.';
    $output->writeln('<fg=yellow>'.str_pad('',strlen($header),'=').'</>');
    $output->writeln('<fg=yellow>'.$header.'</>');
    $output->writeln('<fg=yellow>'.str_pad('',strlen($header),'=').'</>');
    $output->writeln('Showing first '.$result->countItemsOnPage().' result(s):');

    foreach ($result as $resultItem) {
      $rows[] = [$resultItem->getTitle(), $resultItem->getPreview()];
    }
    $table = new Table($output);
    $table->setHeaders(['Title','Preview'])->setRows($rows);
    $table->render();

    return 0;
  }
}

$app = new Application();
$app->add(new SearchCommand());
$app->run();