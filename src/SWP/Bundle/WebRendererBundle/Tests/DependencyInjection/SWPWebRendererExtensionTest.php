<?php

/**
 * This file is part of the Superdesk Web Publisher Web Renderer Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\WebRendererBundle\Tests\DependencyInjection;

use SWP\Bundle\WebRendererBundle\DependencyInjection\SWPWebRendererExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SWPWebRendererExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $extension;
    private $container;

    public function setUp()
    {
        $this->extension = new SWPWebRendererExtension();

        $this->container = new ContainerBuilder();
        $this->container->registerExtension($this->extension);
    }

    public function testloadConfiguration()
    {
        $this->extension->load([], $this->container);
        $this->assertTrue($this->container->hasExtension('swp_web_renderer'));
    }
}
