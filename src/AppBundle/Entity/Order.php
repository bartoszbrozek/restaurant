<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use UserBundle\Entity\User;

/**
 * Order
 * @Entity
 */
class Order
{

    /**
     * @ManyToMany(targetEntity="UserBundle:User")
     * @JoinTable(name="user_product",
     *     joinColumns={@JoinColumn(name="product_id", referencedColumnName="id")},
     *     inverseJoinColumns={@JoinColumn(name="order_id"), referencedColumnName="id"}}
     *     )
     */
    protected $purchasers;

    public function __construct()
    {
        $this->purchasers = new ArrayCollection();
    }

    public function getPurchasers()
    {
        return $this->purchasers;
    }
}
