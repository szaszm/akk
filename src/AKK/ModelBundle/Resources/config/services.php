<?php

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Parameter;

/*

$container->setDefinition(
    'akk_model.example',
    new Definition(
        'AKK\ModelBundle\Example',
        array(
            new Reference('service_id'),
            "plain_value",
            new Parameter('parameter_name'),
        )
    )
);

*/

$container->setDefinition(
    'akk_model.repository.detail.repository_impl',
    new Definition(
        \AKK\ModelBundle\Repository\Detail\RepositoryImpl::class,
        [
            new Reference('database_connection')
        ]
    )
);

$container->setDefinition(
    'akk_model.repository.pass_type',
    new Definition(
        \AKK\ModelBundle\Repository\PassTypeRepository::class,
        [
            new Reference('akk_model.repository.detail.repository_impl'),
        ]
    )
);

$container->setDefinition(
    'akk_model.repository.user',
    new Definition(
        \AKK\ModelBundle\Repository\UserRepository::class,
        [
            new Reference('akk_model.repository.detail.repository_impl'),
        ]
    )
);

$container->setDefinition(
    'akk_model.repository.pass',
    new Definition(
        \AKK\ModelBundle\Repository\PassRepository::class,
        [
            new Reference('akk_model.repository.detail.repository_impl'),
            new Reference('akk_model.repository.pass_type'),
            new Reference('akk_model.repository.user')
        ]
    )
);
