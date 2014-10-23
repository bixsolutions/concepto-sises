<?php
 /**
 * Copyright © 2014 Julian Reyes Escrigas <julian.reyes.escrigas@gmail.com>
 *
 * This file is part of concepto-sises.
 *
 * concepto-sises
 * can not be copied and/or distributed without the express
 * permission of Julian Reyes Escrigas <julian.reyes.escrigas@gmail.com>
 */

namespace Concepto\Sises\ApplicationBundle\Handler;

use Concepto\Sises\ApplicationBundle\Entity\EntityRepository;
use Concepto\Sises\ApplicationBundle\Entity\OrmPersistible;
use Doctrine\Common\Inflector\Inflector;
use Doctrine\DBAL\DBALException;
use Doctrine\Instantiator\Instantiator;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\InjectParams;
use JMS\DiExtraBundle\Annotation\Service;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Router;

/**
 * Class RestHandler
 * @package Concepto\Sises\ApplicationBundle\Handler
 * @Service(id="conceptos_sises_abstract_rest.handler", abstract=true)
 */
abstract class RestHandler implements RestHandlerInterface {

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var FormFactory
     */
    private $formfactory;

    /**
     *
     * @InjectParams({
     *   "em" = @Inject("doctrine.orm.default_entity_manager"),
     *   "formfactory" = @Inject("form.factory"),
     *   "router" = @Inject("router")
     * })
     */
    function __construct($em, $formfactory, $router)
    {
        $this->em = $em;
        $this->formfactory = $formfactory;
        $this->router = $router;
    }

    abstract protected function getTypeClassString();
    abstract protected function getOrmClassString();


    public function post($parameters)
    {
        $class = $this->getOrmClassString();
        $object = new $class();

        return $this->process($parameters, $object, 'POST');
    }

    public function put($id, $parameters)
    {
        /** @var OrmPersistible $object */
        $object = $this->getEm()->find($this->getOrmClassString(), $id);

        // fix: los objetos de angular envian el id no es necesario
        if (isset($parameters['id'])) {
            unset($parameters['id']);
        }

        return $this->process($parameters, $object, 'PUT');
    }

    public function patch($id, $parameters)
    {
        /** @var OrmPersistible $object */
        $object = $this->getEm()->find($this->getOrmClassString(), $id);

        return $this->process($parameters, $object, 'PATCH');
    }

    public function delete($id)
    {
        try {
            $object = $this->getEm()->find($this->getOrmClassString(), $id);

            if ($object) {
                $this->getEm()->remove($object);
                $this->getEm()->flush();
                return View::create(null, Codes::HTTP_NO_CONTENT);
            }
        } catch (DBALException $e) {
            return View::create(null, Codes::HTTP_CONFLICT);
        }

        throw new NotFoundHttpException("Object {$id} not found");
    }

    public function get($id)
    {
        return $this->getEm()->find($this->getOrmClassString(), $id);
    }

    public function cget($pagerParams, $extraParams = array())
    {
        /** @var EntityRepository $repository */
        $repository = $this->getEm()->getRepository($this->getOrmClassString());
        $results = $repository->findAll($extraParams);

        $pager = new Pagerfanta(new ArrayAdapter($results));

        $pager->setMaxPerPage($pagerParams['limit']);
        $pager->setCurrentPage($pagerParams['page']);

        return $pager;
    }

    protected function getRouteName()
    {
        $name = explode('\\', $this->getOrmClassString());

        return 'get_' . strtolower(end($name));
    }


    /**
     * @param array  $parameters
     * @param OrmPersistible|null   $object
     * @param string $method
     *
     * @return View
     */
    protected function process(array $parameters, $object, $method = 'PUT')
    {
        $instantiator = new Instantiator();

        $type = class_exists($this->getTypeClassString()) ?
            $instantiator->instantiate($this->getTypeClassString()): $this->getTypeClassString();

        $bag = array();

        list($object, $bag, $parameters) = $this->preSubmit($object, $bag, $parameters);

        $form = $this->formfactory->create($type, $object);
        $form->submit($this->camelizeParamers($parameters), 'PATCH' !== $method);

        $url = $this->getRouteName();

        if ($form->isValid()) {
            $code = $object->getId() ? Codes::HTTP_NO_CONTENT : Codes::HTTP_CREATED;
            list($object, ) = $this->preFlush($object, $bag);
            $this->getEm()->persist($object);
            $this->getEm()->flush();

            $view = View::createRedirect(
                $this->router->generate($url, array('id' => $object->getId())),
                $code
            );

            return $view;
        }

        return View::create($form, Codes::HTTP_BAD_REQUEST);
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEm()
    {
        return $this->em;
    }

    /**
     * @return \Symfony\Component\Form\FormFactory
     */
    public function getFormfactory()
    {
        return $this->formfactory;
    }

    /**
     * @return \Symfony\Component\Routing\Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    protected function camelizeParamers($parameters)
    {
        if (!is_array($parameters)) {
            return $parameters;
        }

        $camelizedParams = [];
        foreach (array_keys($parameters) as $key) {
            $camelizedParams[Inflector::camelize($key)] = $this->camelizeParamers($parameters[$key]);
        }

        return $camelizedParams;
    }

    protected  function preSubmit($object, $bag = array(), $parameters = array())
    {
        return array($object, $bag, $parameters);
    }

    protected  function preFlush($object, $bag = array())
    {
        return array($object, $bag);
    }
}