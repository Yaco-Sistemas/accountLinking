<?php

// AccountLinking configuration file example

$config = array(

    // Boolean. Enables or not the fact of remove auth sources that 
    // does not matchs the expected LoA.
    'disable-non-compliance' => false,

    // Default LoA value to be used for idps and sps that are not
    // defined in the LoAs var.
    'default-LoAs' => array (
        'sp' => 2,
        'idp' => 1,
    ),

    // Array that contains a list of 'idps' and 'sps', each
    // entityId is associated with the required LoA  
    'LoAs' => array (
        'idps' => array (
            'http://idp1.example.com' => 3,
            'http://sp1.example.com' => 2
        ),
        'sps' => array (
            'http://sp1.example.com' => 2,
            'http://sp2.example.com' => 1,
        ),
    ),
);


