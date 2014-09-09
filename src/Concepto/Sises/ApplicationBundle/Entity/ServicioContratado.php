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

namespace Concepto\Sises\ApplicationBundle\Entity;


use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use JMS\Serializer\Annotation\Exclude;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class ServicioContratado
 * @package Concepto\Sises\ApplicationBundle\Entity
 * @Entity()
 * @Table("servicio_contratado")
 */
class ServicioContratado {
    /**
     * @var string
     * @Id()
     * @Column(length=36, name="id")
     * @GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @var string
     * @Column(name="nombre", length=250)
     * @NotBlank()
     */
    protected $nombre;

    /**
     * @var int
     * @Column(name="dias_contratados", type="integer")
     * @NotBlank()
     */
    protected $diasContratados;

    /**
     * @var int
     * @Column(name="unidades_diarias", type="integer")
     * @NotBlank()
     */
    protected $unidadesDiarias;

    /**
     * @var double
     * @Column(name="valor_unitario", type="decimal", precision=64, scale=2)
     * @NotBlank()
     */
    protected $valorUnitario;

    /**
     * @var double
     * @Column(name="costo_unitario", type="decimal", precision=64, scale=2)
     * @NotBlank()
     */
    protected $costoUnitario;

    /**
     * @var Contrato
     * @ManyToOne(targetEntity="Concepto\Sises\ApplicationBundle\Entity\Contrato", fetch="LAZY", inversedBy="servicios")
     * @NotBlank()
     * @JoinColumn(nullable=false)
     * @Exclude()
     */
    protected $contrato;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * @param string $nombre
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    /**
     * @param float $costoUnitario
     */
    public function setCostoUnitario($costoUnitario)
    {
        $this->costoUnitario = $costoUnitario;
    }

    /**
     * @return float
     */
    public function getCostoUnitario()
    {
        return $this->costoUnitario;
    }

    /**
     * @param int $diasContratados
     */
    public function setDiasContratados($diasContratados)
    {
        $this->diasContratados = $diasContratados;
    }

    /**
     * @return int
     */
    public function getDiasContratados()
    {
        return $this->diasContratados;
    }

    /**
     * @param int $unidadesDiarias
     */
    public function setUnidadesDiarias($unidadesDiarias)
    {
        $this->unidadesDiarias = $unidadesDiarias;
    }

    /**
     * @return int
     */
    public function getUnidadesDiarias()
    {
        return $this->unidadesDiarias;
    }

    /**
     * @param mixed $valorUnitario
     */
    public function setValorUnitario($valorUnitario)
    {
        $this->valorUnitario = $valorUnitario;
    }

    /**
     * @return mixed
     */
    public function getValorUnitario()
    {
        return $this->valorUnitario;
    }

    /**
     * @return mixed
     */
    public function getContrato()
    {
        return $this->contrato;
    }

    /**
     * @param mixed $contrato
     */
    public function setContrato($contrato)
    {
        $this->contrato = $contrato;
    }


}