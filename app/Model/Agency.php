<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Agency
 *
 * @author kaan
 */
class Agency extends AppModel{
    //put your code here
    public $useTable = "agency";
    public $primaryKey = "Agency_id";
    public $hasBelongsToMany = array(
        "route" => array(
            "className" => "Route",
        )
    );
}
