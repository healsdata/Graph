<?php
/**
 * File containing the ezcGraphToolsNotRenderedException class
 *
 * @package Graph
 * @version //autogen//
 * @copyright Copyright (C) 2005-2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Exception thrown when a chart was not rendered yet, but the graph tool 
 * requires information only available in rendered charts.
 *
 * @package Graph
 * @version //autogen//
 */
class ezcGraphToolsNotRenderedException extends ezcGraphException
{
    public function __construct( $chart )
    {
        parent::__construct( "Chart is not yet rendered." );
    }
}

?>