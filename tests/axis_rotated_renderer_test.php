<?php
/**
 * ezcGraphAxisRotatedRendererTest 
 * 
 * @package Graph
 * @version //autogen//
 * @subpackage Tests
 * @copyright Copyright (C) 2005-2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

require_once dirname( __FILE__ ) . '/test_case.php';

/**
 * Tests for ezcGraph class.
 * 
 * @package ImageAnalysis
 * @subpackage Tests
 */
class ezcGraphAxisRotatedRendererTest extends ezcGraphTestCase
{
    protected $basePath;

    protected $tempDir;

    protected $renderer;

    protected $driver;

	public static function suite()
	{
		return new PHPUnit_Framework_TestSuite( "ezcGraphAxisRotatedRendererTest" );
	}

    protected function setUp()
    {
        static $i = 0;

        if ( version_compare( phpversion(), '5.1.3', '<' ) )
        {
            $this->markTestSkipped( "This test requires PHP 5.1.3 or later." );
        }

        $this->tempDir = $this->createTempDir( __CLASS__ . sprintf( '_%03d_', ++$i ) ) . '/';
        $this->basePath = dirname( __FILE__ ) . '/data/';
    }
    
    protected function tearDown()
    {
        if ( !$this->hasFailed() )
        {
            $this->removeTempDir();
        }
    }

    public function testRenderTextBoxes()
    {
        $chart = new ezcGraphLineChart();
        $chart->palette = new ezcGraphPaletteBlack();
        $chart->xAxis->axisLabelRenderer = new ezcGraphAxisRotatedLabelRenderer();
        $chart->yAxis->axisLabelRenderer = new ezcGraphAxisNoLabelRenderer();
        $chart->data['sampleData'] = new ezcGraphArrayDataSet( array( 'sample 1' => 234, 'sample 2' => 21, 'sample 3' => 324, 'sample 4' => 120, 'sample 5' => 1) );
        
        $mockedRenderer = $this->getMock( 'ezcGraphRenderer2d', array(
            'drawText',
        ) );

        $mockedRenderer
            ->expects( $this->at( 0 ) )
            ->method( 'drawText' )
            ->with(
                $this->equalTo( new ezcGraphBoundings( 132.5, 154., 160., 206. ), 1. ),
                $this->equalTo( 'sample 1 ' ),
                $this->equalTo( ezcGraph::MIDDLE | ezcGraph::RIGHT ),
                $this->equalTo( new ezcGraphRotation( -45, new ezcGraphCoordinate( 160, 180 ) ) )
            );
        $mockedRenderer
            ->expects( $this->at( 1 ) )
            ->method( 'drawText' )
            ->with(
                $this->equalTo( new ezcGraphBoundings( 207.5, 154., 235., 206. ), 1. ),
                $this->equalTo( 'sample 2 ' ),
                $this->equalTo( ezcGraph::MIDDLE | ezcGraph::RIGHT ),
                $this->equalTo( new ezcGraphRotation( -45, new ezcGraphCoordinate( 235, 180 ) ) )
            );
        $mockedRenderer
            ->expects( $this->at( 4 ) )
            ->method( 'drawText' )
            ->with(
                $this->equalTo( new ezcGraphBoundings( 432.5, 154., 460., 206. ), 1. ),
                $this->equalTo( 'sample 5 ' ),
                $this->equalTo( ezcGraph::MIDDLE | ezcGraph::RIGHT ),
                $this->equalTo( new ezcGraphRotation( -45, new ezcGraphCoordinate( 460, 180 ) ) )
            );

        $chart->renderer = $mockedRenderer;

        $chart->render( 500, 200 );
    }

    public function testAxisRotatedLabelRendererPropertyAngle()
    {
        $options = new ezcGraphAxisRotatedLabelRenderer();

        $this->assertSame(
            45.,
            $options->angle,
            'Wrong default value for property angle in class ezcGraphAxisRotatedLabelRenderer'
        );

        $options->angle = 89.5;
        $this->assertSame(
            89.5,
            $options->angle,
            'Setting property value did not work for property angle in class ezcGraphAxisRotatedLabelRenderer'
        );

        $options->angle = 410.5;
        $this->assertSame(
            50.5,
            $options->angle,
            'Setting property value did not work for property angle in class ezcGraphAxisRotatedLabelRenderer'
        );

        try
        {
            $options->angle = false;
        }
        catch ( ezcBaseValueException $e )
        {
            return true;
        }

        $this->fail( 'Expected ezcBaseValueException.' );
    }

    public function testRenderCompleteLineChart()
    {
        $filename = $this->tempDir . __FUNCTION__ . '.svg';

        $chart = new ezcGraphLineChart();

        $chart->data['Line 1'] = new ezcGraphArrayDataSet( array( 'sample 1' => 234, 'sample 2' => 21, 'sample 3' => 324, 'sample 4' => 120, 'sample 5' => 1) );
        $chart->data['Line 2'] = new ezcGraphArrayDataSet( array( 'sample 1' => 543, 'sample 2' => 234, 'sample 3' => 298, 'sample 4' => 5, 'sample 5' => 613) );

        $chart->xAxis->axisLabelRenderer = new ezcGraphAxisRotatedLabelRenderer();
        $chart->xAxis->axisSpace = .25;
        $chart->yAxis->axisLabelRenderer = new ezcGraphAxisRotatedLabelRenderer();

        $chart->render( 500, 200, $filename );

        $this->compare(
            $filename,
            $this->basePath . 'compare/' . __CLASS__ . '_' . __FUNCTION__ . '.svg'
        );
    }

    public function testRenderCompleteLineChartReverseRotated()
    {
        $filename = $this->tempDir . __FUNCTION__ . '.svg';

        $chart = new ezcGraphLineChart();

        $chart->data['Line 1'] = new ezcGraphArrayDataSet( array( 'sample 1' => 234, 'sample 2' => 21, 'sample 3' => 324, 'sample 4' => 120, 'sample 5' => 1) );
        $chart->data['Line 2'] = new ezcGraphArrayDataSet( array( 'sample 1' => 543, 'sample 2' => 234, 'sample 3' => 298, 'sample 4' => 5, 'sample 5' => 613) );

        $chart->xAxis->axisLabelRenderer = new ezcGraphAxisRotatedLabelRenderer();
        $chart->xAxis->axisSpace = .25;
        $chart->xAxis->axisLabelRenderer->angle = -45;

        $chart->yAxis->axisLabelRenderer = new ezcGraphAxisRotatedLabelRenderer();
        $chart->yAxis->axisLabelRenderer->angle = -45;

        $chart->render( 500, 200, $filename );

        $this->compare(
            $filename,
            $this->basePath . 'compare/' . __CLASS__ . '_' . __FUNCTION__ . '.svg'
        );
    }

    public function testRenderRotatedAxisWithLotsOfLabels()
    {
        $filename = $this->tempDir . __FUNCTION__ . '.svg';

        $labelCount = 30;

        // Make this reproducible
        mt_srand( 23 );

        for ( $i = 0; $i < $labelCount; ++$i )
        {
            $data[(string) ( 2000 + $i )] = mt_rand( 500, 2000 );
        }

        $chart = new ezcGraphLineChart();
        $chart->data['sample'] = new ezcGraphArrayDataSet( $data );

        // Set manual label count
        $chart->xAxis->labelCount = 31;

        $chart->xAxis->axisLabelRenderer = new ezcGraphAxisRotatedLabelRenderer();

        $chart->render( 500, 200, $filename );

        $this->compare(
            $filename,
            $this->basePath . 'compare/' . __CLASS__ . '_' . __FUNCTION__ . '.svg'
        );
    }
}

?>