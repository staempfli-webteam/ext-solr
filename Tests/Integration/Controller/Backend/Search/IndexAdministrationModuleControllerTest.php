<?php

namespace ApacheSolrForTypo3\Solr\Tests\Integration\Controller\Backend\Search;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2019 Timo Hund <timo.hund@dkd.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use ApacheSolrForTypo3\Solr\ConnectionManager;
use ApacheSolrForTypo3\Solr\Controller\Backend\Search\IndexAdministrationModuleController;
use ApacheSolrForTypo3\Solr\Domain\Site\SiteRepository;
use ApacheSolrForTypo3\Solr\IndexQueue\Queue;
use ApacheSolrForTypo3\Solr\System\Mvc\Backend\Service\ModuleDataStorageService;
use ApacheSolrForTypo3\Solr\Tests\Integration\IntegrationTest;
use Doctrine\DBAL\Exception as DBALException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\TestingFramework\Core\Exception as TestingFrameworkCoreException;

/**
 * Class IndexAdministrationModuleControllerTest
 */
class IndexAdministrationModuleControllerTest extends IntegrationTest
{
    /**
     * @var IndexAdministrationModuleController
     */
    protected $controller;

    /**
     * @throws NotFoundExceptionInterface
     * @throws DBALException
     * @throws ContainerExceptionInterface
     * @throws NoSuchCacheException
     * @throws TestingFrameworkCoreException
     */
    protected function setUp(): void
    {
        parent::setUp();
        $GLOBALS['LANG'] = $this->createMock(LanguageService::class);

        $this->writeDefaultSolrTestSiteConfiguration();

        $this->controller = $this->getMockBuilder(IndexAdministrationModuleController::class)
            ->setConstructorArgs(
                [
                    'moduleTemplateFactory' => $this->getContainer()->get(ModuleTemplateFactory::class),
                    'moduleDataStorageService' => GeneralUtility::makeInstance(ModuleDataStorageService::class),
                    'siteRepository' => GeneralUtility::makeInstance(SiteRepository::class),
                    'siteFinder' => GeneralUtility::makeInstance(SiteFinder::class),
                    'solrConnectionManager' => GeneralUtility::makeInstance(ConnectionManager::class),
                    'indexQueue' => GeneralUtility::makeInstance(Queue::class),
                ]
            )
            ->onlyMethods(['addFlashMessage'])
            ->getMock();
        $uriBuilderMock = $this->getMockBuilder(UriBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['uriFor'])->getMock();
        $uriBuilderMock->expects(self::any())->method('uriFor')->willReturn('index');
        $this->controller->injectUriBuilder($uriBuilderMock);
    }

    /**
     * @test
     */
    public function testReloadIndexConfigurationAction()
    {
        /** @var SiteRepository $siteRepository */
        $siteRepository = GeneralUtility::makeInstance(SiteRepository::class);
        $selectedSite = $siteRepository->getFirstAvailableSite();
        $this->controller->setSelectedSite($selectedSite);
        $this->controller->expects(self::exactly(1))
            ->method('addFlashMessage')
            ->with('Core configuration reloaded (core_en, core_de, core_da).', '', FlashMessage::OK);
        $this->controller->reloadIndexConfigurationAction();
    }

    /**
     * @test
     */
    public function testEmptyIndexAction()
    {
        /** @var SiteRepository $siteRepository */
        $siteRepository = GeneralUtility::makeInstance(SiteRepository::class);
        $selectedSite = $siteRepository->getFirstAvailableSite();
        $this->controller->setSelectedSite($selectedSite);
        $this->controller->expects(self::atLeastOnce())
            ->method('addFlashMessage')
            ->with('Index emptied for Site "Root of Testpage testone.site aka integration_tree_one, Root Page ID: 1" (core_en, core_de, core_da).', '', FlashMessage::OK);

        $this->controller->emptyIndexAction();
    }
}
