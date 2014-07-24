<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;
use CssCrush\CssCrush;
/**
 * Loads CssCrush files.
 *
 * @link   http://the-echoplex.net/csscrush/
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 * @author Tristan Lins <tristan.lins@bit3.de>
 *
 * @todo   import directives do not work
 */
class CssCrushFilter implements FilterInterface
{
	private $debug = false;

	private $boilerplate;

	private $versioning = false;

	private $plugins = array();

	public function setDebug($debug)
	{
		$this->debug = $debug;
	}

	public function setBoilerplate($boilerplate)
	{
		$this->boilerplate = $boilerplate;
	}

	public function setVersioning($versioning)
	{
		$this->versioning = $versioning;
	}

	public function setPlugins($plugins)
	{
		$this->plugins = (array) $plugins;
		return $this;
	}

	public function getPlugins()
	{
		return $this->plugins;
	}

	public function filterLoad(AssetInterface $asset)
	{
		$options = array();

		if (null !== $this->debug) {
			$options['debug'] = (bool) $this->debug;
		}

		if (null !== $this->boilerplate) {
			$options['boilerplate'] = (bool) $this->boilerplate;
		}

		if (null !== $this->versioning) {
			$options['versioning'] = (bool) $this->versioning;
		}

		if (!empty($this->plugins)) {
			$options['enable'] = $this->plugins;
		}

		// remember the previous document root
		$snapshot = CssCrush::$config->docRoot;

		// process the asset
		CssCrush::$config->docRoot = $asset->getSourceRoot();
		$output                     = (string) CssCrush::string($asset->getContent(), $options);
		$asset->setContent($output);

		// cleanup
		CssCrush::$config->docRoot = $snapshot;
	}

	public function filterDump(AssetInterface $asset)
	{
		$this->filterLoad($asset);
	} 
}
