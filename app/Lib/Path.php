<?php

/**
 * @access public
 * @author Kaan
 */
App::uses("Node", "Lib");
App::uses("Relationships", "Lib");

class Path {

    /**
     * @AttributeType array
     */
    protected $Nodes;

    /**
     * @AttributeType array
     */
    protected $Relationships;

    /**
     * @access public
     * @return array_1
     * @ReturnType array
     */
    public function GetFullPath() {
        $Path = array();
        foreach ($this->Nodes as $key => $node) {
            $Rel = array();
            if ($key != count($this->Relationships)) {
                $Relation = $this->Relationships[$key];
                $Rel = array(
                    "RouteID" => $Relation->GetProperty("RouteID"),
                    "RouteType" => "BUS",
                    "EndNode" => $Relation->GetEnd(),
                );
            }
            $Path[] = array(
                "Stop" => 1313,
                "StopID" => $node->GetProperty("StopID"),
                "Relationship" => $Rel,
            );
        }
        return $Path;
    }

    /**
     * @access public
     * @return Node
     * @ReturnType Node
     */
    public function GetNodes() {
        
    }

    /**
     * @access public
     * @param Node aNode
     * @return Node
     * @ParamType aNode Node
     * @ReturnType Node
     */
    public function SetNodes(Node $Node) {
        $this->Nodes[] = $Node;
    }

    /**
     * @access public
     * @return Relationships
     * @ReturnType Relationships
     */
    public function GetRelationship() {
        
    }

    /**
     * @access public
     * @param Relationships aRelationship
     * @return Relationships
     * @ParamType aRelationship Relationships
     * @ReturnType Relationships
     */
    public function SetRelationship(Relationships $Relationship) {
        $this->Relationships[] = $Relationship;
    }

}

?>