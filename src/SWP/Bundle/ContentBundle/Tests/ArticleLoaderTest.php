<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\ContentBundle\Tests;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use SWP\Bundle\ContentBundle\Loader\ArticleLoader;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;

class ArticleLoaderTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();

        $this->runCommand('doctrine:schema:drop', ['--force' => true, '--env' => 'test'], true);
        $this->runCommand('doctrine:doctrine:schema:update', ['--force' => true, '--env' => 'test'], true);
        $this->loadFixtures([
            'SWP\Bundle\FixturesBundle\DataFixtures\ORM\LoadTenantsData',
        ]);
        $this->runCommand('doctrine:phpcr:repository:init', ['--env' => 'test'], true);
    }

    public function testFindNewArticle()
    {
        $this->loadFixtures([
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadArticlesData',
        ], null, 'doctrine_phpcr');

        $articleLoader = new ArticleLoader(
            $this->getContainer()->get('swp.publish_workflow.checker'),
            $this->getContainer()->get('doctrine_phpcr.odm.document_manager'),
            $this->getContainer()->getParameter('kernel.root_dir'),
            $this->getContainer()->get('doctrine_cache.providers.main_cache'),
            $this->getContainer()->get('swp_multi_tenancy.path_builder'),
            $this->getContainer()->getParameter('swp_multi_tenancy.persistence.phpcr.route_basepaths')
        );

        $this->assertTrue($articleLoader->isSupported('article'));
        $this->assertTrue($articleLoader->isSupported('articles'));
        $this->assertFalse($articleLoader->isSupported('items'));

        $article = $articleLoader->load('article', ['contentPath' => '/swp/default/content/test-article']);
        $this->assertInstanceOf('SWP\Component\TemplatesSystem\Gimme\Meta\Meta', $article);

        $this->assertFalse($articleLoader->load('article', ['contentPath' => '/swp/default/content/test-articles']));
        $this->assertFalse($articleLoader->load('article', ['contentPath' => '/swp/default/content/test-article'], LoaderInterface::COLLECTION));

        $this->assertTrue(count($articleLoader->load('article', ['route' => '/news'], LoaderInterface::COLLECTION)) == 3);
        $this->assertFalse($articleLoader->load('article', ['route' => '/news1'], LoaderInterface::COLLECTION));

        $this->assertFalse($articleLoader->load('article', null, LoaderInterface::COLLECTION));
    }
}
