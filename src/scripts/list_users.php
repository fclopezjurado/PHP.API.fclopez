<?php   // src/scripts/list_users.php

require_once __DIR__ . '/../../bootstrap.php';

$entityManager = getEntityManager();

$userRepository = $entityManager->getRepository('MiW16\Results\Entity\User');
$users = $userRepository->findAll();

if (in_array('--json', $argv)) {
    echo json_encode($users, JSON_PRETTY_PRINT);
} else {
    $items = 0;
    echo PHP_EOL . sprintf("  %2d: %20s %30s %7s\n", 'Id', 'Username:', 'Email:', 'Enabled:');
    /** @var \MiW16\Results\Entity\User $user */
    foreach ($users as $user) {
        echo sprintf(
            '- %2d: %20s %30s %7s',
            $user->getId(),
            $user->getUsername(),
            $user->getEmail(),
            $user->isEnabled()
        ),
        PHP_EOL;
        $items++;
    }

    echo "\nTotal: $items users.\n\n";
}

