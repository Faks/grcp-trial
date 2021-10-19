<?php

declare(strict_types=1);

namespace Application\Controller;

use Application\Entity\Genre;
use Application\Entity\Language;
use Application\Entity\Movie;
use Application\Form\SearchForm;
use Application\Helper\DataHelper;
use Application\Model\SearchMovie;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use GuzzleHttp\Client as Guzzle;
use Laminas\Json\Json;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;

class IndexController extends AbstractActionController
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var SearchMovie
     */
    private $searchMovie;

    /**
     * @var SearchForm
     */
    private $searchForm;

    /**
     * @var JsonModel
     */
    private $viewModel;

    /**
     * @var DataHelper
     */
    private $dataHelper;

    /**
     * @param EntityManager $entityManager
     * @param SearchMovie   $searchMovie
     * @param SearchForm    $searchForm
     * @param JsonModel     $jsonModel
     * @param DataHelper    $dataHelper
     */
    public function __construct(
        EntityManager $entityManager,
        SearchMovie $searchMovie,
        SearchForm $searchForm,
        JsonModel $jsonModel,
        DataHelper $dataHelper
    ) {
        $this->entityManager = $entityManager;
        $this->searchMovie = $searchMovie;
        $this->searchForm = $searchForm;
        $this->viewModel = $jsonModel;
        $this->dataHelper = $dataHelper;
        $this->viewModel->setTerminal(true);
    }

    /**
     * @return ORMException|\Exception|JsonModel
     */
    public function searchAction()
    {
        $request = $this->getRequest();

        if (!$request->getContent()) {
            return $this->createSearchView();
        }

        $this->searchForm->setInputFilter($this->searchMovie->getInputFilter())
            ->setData(Json::decode($request->getContent(), 1));

        if (!$this->searchForm->isValid()) {
            return $this->createSearchView();
        }

        $this->searchMovie->exchangeArray((array) $this->searchForm->getData());

        $guzzleClient = new Guzzle();

        $omdbResponse = $guzzleClient->get(
            sprintf(
                'http://www.omdbapi.com/?t=%s&type=movie&r=json&apikey=32f4648',
                $this->searchMovie->getName()
            )
        )->getBody()->getContents();

        if ($omdbResponse) {
            $omdbResponse = Json::decode($omdbResponse, 1);
        }

        if (
            !is_array($omdbResponse) ||
            !isset($omdbResponse['Title']) ||
            !isset($omdbResponse['Genre']) ||
            !isset($omdbResponse['Language'])
        ) {
            return $this->createSearchView();
        }

        /**
         * Convert response csv data values to array
         */
        $omdbResponse['genres'] = str_getcsv($omdbResponse['Genre'], ",");
        $omdbResponse['languages'] = str_getcsv($omdbResponse['Language'], ",");

        $this->saveNewMovie(
            trim($omdbResponse['Title']),
            $omdbResponse['genres'],
            $omdbResponse['languages']
        );

        return $this->createSearchView();
    }

    /**
     * @return JsonModel
     */
    private function createSearchView(): JsonModel
    {
        $searchHistory = $this->getAllSearchHistory();

        $movies = $this->dataHelper->createMovieData($searchHistory);

        $this->viewModel->setVariable('movies', $movies);
        $this->viewModel->setVariable(
            'genres',
            $this->dataHelper->createOptionsData($this->getAllGenres())
        );
        $this->viewModel->setVariable(
            'languages',
            $this->dataHelper->createOptionsData($this->getAllLanguages())
        );

        return $this->viewModel;
    }

    /**
     * @return Movie[]
     */
    private function getAllSearchHistory(): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('m')
            ->from(Movie::class, 'm');

        if (!empty($this->searchMovie->getGenre())) {
            $qb->leftJoin('m.genres', 'g')
                ->andWhere('g.genre = :genre')
                ->setParameter('genre', $this->searchMovie->getGenre());
        }

        if (!empty($this->searchMovie->getLanguage())) {
            $qb->leftJoin('m.languages', 'l')
                ->andWhere('l.language = :lang')
                ->setParameter('lang', $this->searchMovie->getLanguage());
        }

        return $qb->getQuery()
            ->getResult();
    }

    /**
     * @return array{genre: string}[]
     */
    private function getAllGenres(): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('g.genre')
            ->distinct()
            ->from(Genre::class, 'g')
            ->orderBy('g.genre', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array{language: string}[]
     */
    private function getAllLanguages(): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('l.language')
            ->distinct()
            ->from(Language::class, 'l')
            ->orderBy('l.language', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $name
     * @param string[] $genres
     * @param string[] $languages
     * @return bool
     */
    private function saveNewMovie(
        string $name,
        array $genres,
        array $languages
    ): bool {
        $movies = $this->entityManager
            ->getRepository(Movie::class)
            ->findBy(['name' => $name]);

        $dbGenres = [];
        $genreRepository = $this->entityManager->getRepository(Genre::class);

        foreach ($genres as $genre) {
            $dbGenre = $genreRepository->findOneBy(['genre' => $genre]);

            if (null === $dbGenre) {
                $dbGenre = new Genre();
                $dbGenre->setGenre(trim($genre));

                $this->entityManager->persist($dbGenre);
            }

            $dbGenres[] = $dbGenre;
        }

        $dbLanguages = [];
        $languageRepository = $this->entityManager->getRepository(Language::class);

        foreach ($languages as $language) {
            $dbLanguage = $languageRepository->findOneBy(['language' => $language]);

            if (null === $dbLanguage) {
                $dbLanguage = new Language();
                $dbLanguage->setLanguage(trim($language));

                $this->entityManager->persist($dbLanguage);
            }

            $dbLanguages = [$dbLanguage];
        }

        if (count($movies) < 1) {
            $movie = new Movie();

            $movie->setName($name);

            $movies[] = $movie;
        }

        foreach ($movies as $movie) {
            foreach ($dbGenres as $genre) {
                $movie->addGenre($genre);
            }

            foreach ($dbLanguages as $language) {
                $movie->addLanguage($language);
            }

            $this->entityManager->persist($movie);
        }

        $this->entityManager->flush();

        return true;
    }
}
