<?php

/**
 * Description of PlannerController
 * Planner controller, sistemin algoritmalarının çalıştırıldığı en temel
 * controller dosyasıdır.Bu dosya yardımı ile client'a otobüs tarifeleri hakkında
 * bilgiler ve hesaplama sonuçları iletilir
 * 
 * @author Kaan KILIÇ kaan@kaankilic.com
 * @version v1.0A
 */
App::uses('Transactor', 'Lib');

class PlannerController extends AppController {
    /* RquestHandler component'ini import ederek REST altyapısını oluşturuyor. */

    public $components = array('RequestHandler');
    /* RealTime dataların API-Key isteği varsa aşağıdaki değişken */
    public $realtimeApiKey = "9C1E7770-ED89-4689-BA1E-B7DBB48F8D4A";

    public function index() {
        $this->loadModel("Stop");
        /*
         * Start Points
         */
        $StartLatitude = "21.331304";
        $StopLongitude = "-157.924238";
        $this->Stop->virtualFields['Distance'] = "CALCULATE_DISTANCEE(Stop.Stop_lat,Stop.Stop_lon,$StartLatitude,$StopLongitude)";
        $StartStation = $this->Stop->find("first", array("conditions" => array("Distance <" => "250.00"), "recursive" => 1, "limit" => 3));

        /*
         * End Points
         */
        $MyLatitude = "21.260340";
        $MyLongtitude = "-157.818079";
        $this->Stop->virtualFields['Distance'] = "CALCULATE_DISTANCEE(Stop.Stop_lat,Stop.Stop_lon,$MyLatitude,$MyLongtitude)";
        $MyLastStation = $this->Stop->find('first', array("conditions" => array("Distance <" => "150.00"), "recursive" => 1, "limit" => 1));
        $Trip_ID = array();
        foreach ($MyLastStation["StopTime"] as $Row) {
            $Trip_ID[] = $Row["Trip_id"];
        }
        $this->loadModel("Trip");
        $Routes = $this->Trip->find("all", array("fields" => "DISTINCT Route_id", "conditions" => array("Trip.Trip_id" => $Trip_ID), "recursive" => -1));
        debug($Routes);
        exit();
        $this->set(array(
            'recipes' => $recipes,
            '_serialize' => array('recipes')
        ));
    }

    public function search($Latitude, $Longitude, $EndLatitude, $EndLongitude, $Limit = 3) {
        if (is_null($Latitude) || is_null($Longitude) || is_null($EndLatitude) || is_null($EndLongitude)) {
            @exit();
        }
        $this->loadModel("Stop");
        $this->Stop->virtualFields['Distance'] = "CALCULATE_DISTANCEE(Stop.Stop_lat,Stop.Stop_lon,$Latitude,$Longitude)";
        $NearestStations = $this->Stop->find('all', array("conditions" => array("Distance <" => "250.00"), "recursive" => -1, "limit" => $Limit));

        $this->Stop->virtualFields['Distance'] = "CALCULATE_DISTANCEE(Stop.Stop_lat,Stop.Stop_lon,$EndLatitude,$EndLongitude)";
        $EndStations = $this->Stop->find('all', array("conditions" => array("Distance <" => "250.00"), "recursive" => -1, "limit" => $Limit));

        $Response = array();

        $dbfs = new Transactor();
        /*
         * Aşağaıdaki sorgu transfer noktalarınada bakarak hareket eder ve kullanıcıyı olabildiğince
         * durak sayısını baz alarak transfer ettirir.
         */
        $dbfs->ExecuteChyper('START n=node:Stop(StopID="' . $NearestStations[0]["Stop"]["Stop_id"] . '"), m=node:Stop(StopID="' . $EndStations[0]["Stop"]["Stop_id"] . '") MATCH p=shortestPath(n-[:ROUTE*..1000]->m) RETURN p,length(p);');
        $Response["WithTransferPoints"] = $dbfs->GetResponse();
        /*
         * Aşağıdaki sorgu transit direkt olarak giden otobüs varmı yokmu şeklinde bir kontrole girer.
         */
        foreach ($NearestStations as $NStop) {
            foreach ($EndStations as $EStop) {
                $dbfs->ExecuteChyper('START a=node:Stop(StopID="' . $NStop["Stop"]["Stop_id"] . '"), d=node:Stop(StopID="' . $EStop["Stop"]["Stop_id"] . '")
MATCH p=shortestPath(a-[r:ROUTE*]-d)
WITH head(relationships(p))as r1,last(relationships(p))as r2,p
WHERE r2.RouteID = r1.RouteID
return p;');
                $Response["DirectTransport"] = $dbfs->GetResponse();
                if (!empty($Response)) {
                    break;
                }
            }
        }
        $this->set(array(
            'Arrival' => $Response,
            '_serialize' => array('Arrival')
        ));
    }

    /*
     *  Nearest Stations are retreiving nearest station informations by the
     *  coordinates of client.Then you can retreive station informations.
     *  Number of Nearest Stations can be parametrized with Limit parameter.
     */

    public function NearestStations($Latitude, $Longitude, $Limit = 3) {
        $this->loadModel("Stop");
        $this->Stop->virtualFields['Distance'] = "CALCULATE_DISTANCEE(Stop.Stop_lat,Stop.Stop_lon,$Latitude,$Longitude)";
        $NearestStations = $this->Stop->find('all', array("conditions" => array("Distance <" => "150.00"), "recursive" => -1, "limit" => $Limit));
        $this->set(array(
            'Station' => $NearestStations,
            '_serialize' => array('Station')
        ));
    }

    /*
     *  Bus Path funciton retrieves full path of bus.Client is sending path
     *  And function gets full path of bus.
     *  This function depends on real-time api link.
     *  For example: 
     */

    public function BusPath($RealTimeTripID) {
        $this->loadModel("Trip");
        $Vehicle = $this->Trip->find("first", array("fields" => "Shape_id", "recursive" => -1, "conditions" => array("Trip_id" => $RealTimeTripID)));
        $this->loadModel("Shape");
        $Shapes = $this->Shape->find("all", array("conditions" => array("Shape_id" => $Vehicle["Trip"]["Shape_id"]), "recursive" => -1));

        $this->loadModel("StopTime");
        $StopTime = $this->StopTime->find("all", array("fields" => "Stop.*", "conditions" => array("StopTime.Trip_id" => $RealTimeTripID), "recursive" => 0));

        $BusPaths = array(
            "BusCordinaates" => array(
                "TripID" => $RealTimeTripID
            ), "PathShapes" => $Shapes,
            "Stations" => $StopTime
        );
        $this->set(array(
            'BusPath' => $BusPaths,
            '_serialize' => array('BusPath')
        ));
    }

    public function TripList() {
        $this->loadModel("Trip");
        $BusPaths = $this->Trip->find("all", array("group" => array("Route_id"), "fields" => array("DISTINCT Trip_id", "Route_id", "Shape_id", "Trip_headsign"), "recursive" => -1));
        $this->set(array(
            'Trip' => $BusPaths,
            '_serialize' => array('Trip')
        ));
    }

    public function StopBusses($StopID) {
        $APILink = "http://api.thebus.org/arrivals/?key=" . $this->realtimeApiKey . "&stop=" . $StopID;
        $StopTimeXML = Xml::toArray(Xml::build($APILink));
        $this->set(array(
            'Stop' => $StopTimeXML,
            '_serialize' => array('Stop')
        ));
    }

}
