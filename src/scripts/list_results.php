<?php   // src/scripts/list_results.php

require_once __DIR__ . '/../../bootstrap.php';

use MiW16\Results\Entity\Result;

$entityManager = getEntityManager();

$resultsRepository = $entityManager->getRepository('MiW16\Results\Entity\Result');
$results = $resultsRepository->findAll();

if ($argc === 1) {
    echo PHP_EOL . sprintf('%3s - %3s - %20s - %s', 'id', 'res', 'username', 'time') . PHP_EOL;
    $items = 0;
    /* @var $result Result */
    foreach ($results as $result) {
        echo $result . PHP_EOL;
        $items++;
    }
    echo PHP_EOL . "Total: $items results.";
} elseif (in_array('--json', $argv, true)) {
    echo json_encode($results, JSON_PRETTY_PRINT);
}
