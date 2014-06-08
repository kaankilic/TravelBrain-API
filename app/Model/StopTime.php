<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of StopTime
 *
 * @author kaan
 */
class StopTime extends AppModel{
    //put your code here
    public $useTable = "stopTime";
    public $primaryKey = "Stop_count";
    public $hasOne = array(
        "Trip" => array(
            "className" => "Trip",
            "foreignKey" => "Trip_id"
        )
    );
    public $belongsTo = array(
        "Stop" => array(
            "className" => "Stop",
            "foreignKey" => "Stop_id"
        )
    );
}
