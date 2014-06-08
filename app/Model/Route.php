<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Route
 *
 * @author kaan
 */
class Route extends AppModel{
    //put your code here
    public $useTable = "route";
    public $primaryKey = "Route_id";
    public $hasMany = array(
        "agency" => array(
            "className" => "Agency",
            "foreignKey" => "Agency_id"
        )
    );
}
