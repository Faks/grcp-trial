<?php

declare(strict_types=1);

namespace Application\Helper;

use Application\Entity\Movie;

use function implode;

class DataHelper
{
    /**
     * @param Movie[] $allSearchHistory
     *
     * @return array
     */
    public function createMovieData(array $allSearchHistory): array
    {
        $movies = [];

        foreach ($allSearchHistory as $movie) {
            $movies[] = [
                'name' => $movie->getName(),
                'genre' => count($movie->getGenres()) >= 1
                    ? $this->filter($movie, [
                        'all' => 'getGenres',
                        'first' => 'getGenre'
                    ])
                    : '-',
                'language' => count($movie->getLanguages()) >= 1
                    ? $this->filter($movie, [
                        'all' => 'getLanguages',
                        'first' => 'getLanguage'
                    ])
                    : '-',
            ];
        }

        return $movies;
    }

    /**
     * @param string[][] $allOptions
     *
     * @return array
     */
    public function createOptionsData(array $allOptions): array
    {
        $options = [];

        foreach ($allOptions as $option) {
            $options[] = array_pop($option);
        }

        return $options;
    }

    private function filter(Movie $entity, array $methods): string
    {
        $filter = [];

        foreach ($entity->{$methods['all']}() as $model) {
            $filter[] = $model->{$methods['first']}();
        }

        return implode(', ', $filter);
    }
}
