<?php

// AccountLinking configuration file example

$config = array(

    // Boolean. Enables or not the fact of remove auth sources that 
    // does not matchs the expected LoA.
    'disable-non-compliance' => false,

    // Default LoA value to be used for idps and sps that are not
    // defined in the LoAs var or in the auth source.
    'default-LoAs' => array (
        'sp' => 2,
        'idp' => 1,
        'auth' => 1,
    ),

    // Array that contains a list of 'idps' and 'sps', each
    // entityId is associated with the required LoA. And also
    // define the LoAs of the auth sources that may be configured
    // at the config/authsources.php
    'LoAs' => array (
        'idps' => array (
            'http://idp1.example.com' => 3,
            'http://sp1.example.com' => 2
        ),
        'sps' => array (
            'http://sp1.example.com' => 2,
            'http://sp2.example.com' => 1
        ),
        'auths' => array (
            'example-admin' => 3,
            'google' => 1,
            'facebook' => 1
        )
    ),
);


