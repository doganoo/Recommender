<?php
declare(strict_types=1);
/**
 * Recommender
 *
 * Copyright (C) <2020> <Dogan Ucar>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace doganoo\Recommender\Service\CollaborativeFiltering;

use doganoo\DI\Object\Float\IFloatService;
use doganoo\PHPAlgorithms\Common\Exception\InvalidKeyTypeException;
use doganoo\PHPAlgorithms\Common\Exception\UnsupportedKeyTypeException;
use doganoo\PHPAlgorithms\Datastructure\Table\HashTable;
use doganoo\Recommender\Recommendation\Feature\IFeature;
use doganoo\Recommender\Recommendation\Rater\IRater;

/**
 * Class CosineComputer
 *
 * @package doganoo\Recommender\Service\CollaborativeFiltering
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class CosineComputer {

    public const SIMILARITY_EQUAL     = 1;
    public const SIMILARITY_NOT_EQUAL = 0;

    /** @var IFloatService */
    private $floatService;

    public function __construct(IFloatService $floatService) {
        $this->floatService = $floatService;
    }

    /**
     * @param IFeature $first
     * @param IFeature $second
     *
     * @return float
     * @throws InvalidKeyTypeException
     * @throws UnsupportedKeyTypeException
     */
    public function compute(IFeature $first, IFeature $second): float {

        $firstDenominator = $secondDenominator = $denominator = $numerator = $similarity = 0;

        // base case 1: the similarity is equal to one if you pass the same object
        if ($first->getId() === $second->getId()) return CosineComputer::SIMILARITY_EQUAL;

        // step 1: we need to know the common raters of both features. Only by
        // comparing the common raters we can build a similarity. All other raters
        // are excluded here
        $commonRaters = $this->getIntersection($first->getRaters(), $second->getRaters());

        // base case 2: do not do anything if there are no common raters
        if (0 === $commonRaters->size()) return (float) CosineComputer::SIMILARITY_NOT_EQUAL;

        // step 2: apply cosine based similarity function
        // since we determined the common raters and can be sure
        // the raters are present in both features, we can simply
        // iterate over the raters and pick the rating
        /** @var IRater $rater */
        foreach ($commonRaters->keySet() as $raterId) {
            $rater = $commonRaters->get($raterId);
            /** @var IRater $firstRater */
            $firstRater  = $first->getRaters()->get($rater->getId());
            $firstRating = $firstRater->getRating();

            /** @var IRater $secondRater */
            $secondRater  = $second->getRaters()->get($rater->getId());
            $secondRating = $secondRater->getRating();

            $numerator         = $numerator + ($firstRating * $secondRating);
            $firstDenominator  = $firstDenominator + (pow($firstRating, 2));
            $secondDenominator = $secondDenominator + (pow($secondRating, 2));
        }

        // step 3: work with the denominators to build a final one
        $denominator = sqrt($firstDenominator) * sqrt($secondDenominator);

        // we want to prevent a division by zero here!
        // if this condition gets true, the similarity will be 0
        if (false === $this->floatService->equals(0, $denominator)) {
            $similarity = $numerator / $denominator;
        }

        return $similarity;
    }

    private function getIntersection(HashTable $first, HashTable $second): HashTable {
        $result = new HashTable();
        foreach ($first->keySet() as $key) {

            if (true === $second->containsKey($key)) {
                $result->put($key, $first->get($key));
            }
        }

        return $result;
    }


}
