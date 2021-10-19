<?php

declare(strict_types=1);

namespace Application\Controller\Factory;

use Application\Controller\IndexController;
use Application\Form\SearchForm;
use Application\Helper\DataHelper;
use Application\Model\SearchMovie;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\Model\JsonModel;

class IndexControllerFactory implements FactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ): IndexController {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $searchMovie = new SearchMovie();
        $searchForm = new SearchForm();
        $jsonModel = new JsonModel();
        $dataHelper = new DataHelper();

        return new IndexController(
            $entityManager,
            $searchMovie,
            $searchForm,
            $jsonModel,
            $dataHelper
        );
    }
}
