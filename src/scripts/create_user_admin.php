<?php   // src/scripts_aux/create_user.php

require_once __DIR__ . '/../../bootstrap.php';

use MiW16\Results\Entity\User;

$em = getEntityManager();

$user = new User();
$user->setUsername('miw16_admin' . rand());
$user->setEmail($user->getUsername() . '@example.com');
$user->setPassword('*miw16_admin*');
$user->setEnabled(true);

$em->persist($user);
$em->flush();
