<?php

// src/AppBundle/Entity/Order.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="Orders")
 */
class Order
{
    
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="ShopItem", inversedBy="orders")
     * @ORM\JoinColumn(name="item_id", referencedColumnName="id")
     */
    protected $Item;    

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Assert\GreaterThan(
     *     value = 0
     * )
     */
    protected $amount;

    /**
     * @ORM\Column(type="integer")
     */
    protected $status = 0;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set amount
     *
     * @param integer $amount
     *
     * @return Order
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return integer
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Order
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set item
     *
     * @param \AppBundle\Entity\ShopItem $item
     *
     * @return Order
     */
    public function setItem(\AppBundle\Entity\ShopItem $item = null)
    {
        $this->Item = $item;

        return $this;
    }

    /**
     * Get item
     *
     * @return \AppBundle\Entity\ShopItem
     */
    public function getItem()
    {
        return $this->Item;
    }
}
