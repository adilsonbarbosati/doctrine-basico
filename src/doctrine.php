<?php

//serve par criar as configurações das anotações
use Doctrine\ORM\Tools\Setup;

//vai gerenciar todas as entidades da nossa aplicação
use Doctrine\ORM\EntityManager;

//configurar o caminho de onde vai estar nossas entidades
$paths = [
    __DIR__.'/Entity'
];

//setar modo de desenvolvimento ou não
$isDevMode = true;

//configurações de conexão com o banco de dados
$dbParams = [
    'driver' => 'pdo_mysql',
    'user' => 'root',
    'password' => 'mysql123',
    'dbname' => 'doctrine_basico'
];

$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
$entityManager = EntityManager::create($dbParams, $config);

function getEntityManager(){
    global $entityManager;
    return $entityManager;
}