<?php

// Autoload ORM annotations
Doctrine\Common\Annotations\AnnotationRegistry::registerFile(
    realpath(__DIR__.'/../') . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php'
);

Doctrine\Common\Annotations\AnnotationRegistry::registerAutoloadNamespace(
    'Gedmo\Mapping\Annotation',
    realpath(__DIR__.'/../') . '/vendor/gedmo/doctrine-extensions/lib'
);

require_once realpath(__DIR__.'/../').'/vendor/swiftmailer/swiftmailer/lib/classes/Swift.php';

\Swift::registerAutoload(realpath(__DIR__.'/../').
    '/vendor/swiftmailer/swiftmailer/lib/swift_init.php'
);
/*
$autoloadSwift = function($rootDir) use($loader) {
    
    \Swift::registerAutoload($rootDir.
        '/vendor/swiftmailer/swiftmailer/lib/swift_init.php'
    );
};*/