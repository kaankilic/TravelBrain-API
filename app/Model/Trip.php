<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Trip
 *
 * @author kaan
 */
class Trip extends AppModel {

    //put your code here
    public $useTable = "trip";
    public $primaryKey = "Trip_id";
    public $hasOne = array(
        "Route" => array(
            "className" => "Route",
            "foreignKey" => "Route_id"
        ),
        "StopTime" => array(
            "className" => "StopTime",
            "foreignKey" => "Trip_id"
        )
    );
}
