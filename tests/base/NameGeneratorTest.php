<?php
/**
 * Class to test the name generator
 */

require_once '../../knife/base/generator.php';

class BaseGeneratorTest extends PHPUnit_Framework_TestCase
{
	/**
	 * The base object
	 *
	 * @var KnifeBaseGenerator
	 */
	protected $base;

	/**
	 * The data provider
	 *
	 * @return	array
	 */
	public function provider()
	{
		return array(
			array('basename'),
			array('BaSeNaMe'),
			array('base_name'),
			array('9base_2name')
		);
	}

	public function setUp()
	{
		// get the base generator
		$this->base = new KnifeBaseGenerator();
	}

	/**
     * @dataProvider provider
     */
	public function testName($name)
	{
		$this->assertEquals($nameGen->buildName($name), 'basename');
	}
}
