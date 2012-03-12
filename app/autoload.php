<?php

// Autoload ORM annotations
Doctrine\Common\Annotations\AnnotationRegistry::registerFile(
    realpath(__DIR__.'/../') . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php'
);