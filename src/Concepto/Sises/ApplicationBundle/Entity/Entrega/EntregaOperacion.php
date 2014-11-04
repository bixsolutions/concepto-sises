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

namespace Concepto\Sises\ApplicationBundle\Entity\Entrega;


use Concepto\Sises\ApplicationBundle\Entity\ServicioOperativo;
use Doctrine\ORM\Mapping as  ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class EntregaOperacion
 * @package Concepto\Sises\ApplicationBundle\Entity\Entrega
 * @ORM\Entity(repositoryClass="Concepto\Sises\ApplicationBundle\Entity\Entrega\EntregaOperacionRepository")
 * @ORM\Table(name="entrega_operacion")
 */
class EntregaOperacion {

    /**
     * @var string
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(name="id", length=36)
     * @Groups({"list", "details"})
     */
    protected $id;

    /**
     * @var ServicioOperativo
     * @ORM\ManyToOne(targetEntity="Concepto\Sises\ApplicationBundle\Entity\ServicioOperativo")
     * @Assert\NotNull()
     */
    protected $servicio;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $cantidad = 0;

    /**
     * @var \DateTime
     * @ORM\Column(name="fecha_entrega", type="datetime")
     * @Assert\NotNull()
     * @Groups({"details", "list"})
     * @SerializedName("fechaEntrega")
     */
    protected $fechaEntrega;

    /**
     * @return ServicioOperativo
     */
    public function getServicio()
    {
        return $this->servicio;
    }

    /**
     * @param ServicioOperativo $servicio
     */
    public function setServicio($servicio)
    {
        $this->servicio = $servicio;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * @param int $cantidad
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;
    }

    /**
     * @return \DateTime
     */
    public function getFechaEntrega()
    {
        return $this->fechaEntrega;
    }

    /**
     * @param \DateTime $fechaEntrega
     */
    public function setFechaEntrega($fechaEntrega)
    {
        $this->fechaEntrega = $fechaEntrega;
    }
} 