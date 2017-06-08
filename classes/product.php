<?php
class product {
    public $name;
    public $dateIn, $dateOut, $brand, $price, $type;
    
    function __construct( $name, $brand, $dateIn, $dateOut, $price, $type) {
      $this->name      = $name;
      $this->brand     = $brand;
      $this->dateIn    = $dateIn;
      $this->dateOut   = $dateOut;
      $this->price     = $price;
      $this->type      = $type;
    }
    
    function guaranteePeriod () {
      echo "Gwarancja kończy się za ";
    }
    
  }

  ?>