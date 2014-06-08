<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Shape
 *
 * @author kaan
 */
class Shape extends AppModel{
    //put your code here
    public $useTable = "shape";
    public $primaryKey = "Shape_id";
    public $belongTo = array(
        "trip" => array(
            "className" => "Trip",
            "foreignKey" => "Trip_id"
        ),
    );
}
