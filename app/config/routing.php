<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

$collection->add('index', new Route(
    '/',
    array('_controller' => 'AppBundle:Summary:index'),
    array(),
    array(),
    '',
    array(),
    array('GET')
));

$collection->add('transaction_index', new Route(
    '/transactions/',
    array('_controller' => 'AppBundle:Transaction:index'),
    array(),
    array(),
    '',
    array(),
    array('GET')
));

$collection->add('transaction_show', new Route(
    '/transactions/{id}/show',
    array('_controller' => 'AppBundle:Transaction:show'),
    array(),
    array(),
    '',
    array(),
    array('GET')
));

$collection->add('transaction_new', new Route(
    '/transactions/new',
    array('_controller' => 'AppBundle:Transaction:new'),
    array(),
    array(),
    '',
    array(),
    array('GET', 'POST')
));

$collection->add('transaction_edit', new Route(
    '/transactions/{id}/edit',
    array('_controller' => 'AppBundle:Transaction:edit'),
    array(),
    array(),
    '',
    array(),
    array('GET', 'POST')
));

$collection->add('transaction_delete', new Route(
    '/transactions/{id}/delete',
    array('_controller' => 'AppBundle:Transaction:delete'),
    array(),
    array(),
    '',
    array(),
    array('GET')
));

return $collection;
