<?php
/**
 * Class to test the name generator
 */

require_once 'knife/base/generator.php';

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
			array('base9name'),
			array('base_name'),
			array('9ba#$se_2name')
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
		$this->assertRegExp('/^[a-zA-Z]{1,}$/', $this->base->buildName($name));
	}

	/**
     * @dataProvider provider
     */
	public function testFileName($name)
	{
		// @todo possible to combine 2 data providers?
		$this->assertRegExp("/^[a-z_]{1,}(.)[a-z]{1,}$/", $this->base->buildFileName($name));
		$this->assertRegExp("/^[a-z_]{1,}(.)[a-z]{1,}$/", $this->base->buildFileName($name, 'tpl'));
		$this->assertRegExp("/^[a-z_]{1,}(.)[a-z]{1,}$/", $this->base->buildFileName($name, 'tpl#@$'));
		$this->assertRegExp("/^[a-z_]{1,}(.)[a-z]{1,}$/", $this->base->buildFileName($name, 'tpl9'));
		$this->assertRegExp("/^[a-z_]{1,}(.)[a-z]{1,}$/", $this->base->buildFileName($name, 't@#$pl'));
		$this->assertRegExp("/^[a-z_]{1,}(.)[a-z]{1,}$/", $this->base->buildFileName($name, '99tpl'));
	}

	/**
	 * @dataProvider provider
	 */
	public function testDirName($name)
	{
		$this->assertRegExp('/^[a-z]{1,}$/', $this->base->buildDirName($name));
	}
}
