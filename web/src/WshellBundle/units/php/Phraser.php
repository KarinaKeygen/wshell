<?php

class Phraser extends Unit {

    // single view
    public function uiOutput($data)
    {
        return "Hello from __CLASS__";
    }

	// in chain
    public function output($data) {
        
    }
    
    // embed
    public function uiEmbed($data) {
		
    }
}