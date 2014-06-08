<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Stop
 *
 * @author kaan
 */
class Stop extends AppModel{
    //put your code here
    public $useTable = "stop";
    public $primaryKey = "Stop_id";
    public $hasMany = array(
        "StopTime" => array(
            "className" => "StopTime",
            "foreignKey" => "Stop_id"
        )
    );
}
