<?php

declare(strict_types=1);

namespace Application\Helper;

use Application\Entity\Movie;

class DataHelper
{
    /**
     * @param Movie[] $allSearchHistory
     * @return array
     */
    public function createMovieData(array $allSearchHistory): array
    {
        $movies = [];

        foreach ($allSearchHistory as $movie) {
            $movies[] = [
                'name' => $movie->getName(),
                'genre' => count($movie->getGenres()) > 0
                    ? $movie->getGenres()[0]->getGenre()
                    : '-',
                'language' => count($movie->getLanguages()) > 0
                    ? $movie->getLanguages()[0]->getLanguage()
                    : '-',
            ];
        }

        return $movies;
    }

    /**
     * @param string[][] $allOptions
     * @return array
     */
    public function createOptionsData(array $allOptions): array
    {
        $options = array();

        foreach ($allOptions as $option) {
            $options[] = array_pop($option);
        }

        return $options;
    }
}
