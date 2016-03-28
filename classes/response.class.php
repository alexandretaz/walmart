<?php

/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 27/03/16
 * Time: 20:17
 */
class response
{
    /**
     * @var string
     */
    public $route;
    
    public $cost;

    public $totalDistance;

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param string $route
     * @return response
     */
    public function setRoute($route)
    {
        $this->route = $route;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * @param mixed $cost
     * @return response
     */
    public function setCost($cost)
    {
        $this->cost = (float) number_format($cost, 2);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalDistance()
    {
        return $this->totalDistance;
    }

    /**
     * @param mixed $totalDistance
     */
    public function setTotalDistance($totalDistance)
    {
        if( (float) $this->totalDistance !== 0 ) {
            $this->totalDistance += $totalDistance;
            return $this;
        }
        $this->totalDistance = $totalDistance;
        return $this;
    }


    
    
    
}