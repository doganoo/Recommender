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
use doganoo\Recommender\Exception\InvalidRatingException;
use doganoo\Recommender\Recommendation\Feature\IFeature;
use doganoo\Recommender\Recommendation\Rater\IRater;
use doganoo\Recommender\Service\CollaborativeFiltering\Rating\Range\IRange;
use Psr\Log\LoggerInterface;

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

    /** @var IRange */
    private $range;

    /** @var array */
    private $cache = [];

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        IFloatService $floatService
        , IRange $range
        , LoggerInterface $logger
    ) {
        $this->floatService = $floatService;
        $this->range        = $range;
        $this->logger       = $logger;
    }

    /**
     * @param IFeature $first
     * @param IFeature $second
     *
     * @return float
     * @throws InvalidKeyTypeException
     * @throws UnsupportedKeyTypeException
     * @throws InvalidRatingException
     */
    public function compute(IFeature $first, IFeature $second): float {

        $firstDenominator = $secondDenominator = $denominator = $numerator = $similarity = 0;

        // base case 1: the similarity is equal to one if you pass the same object
        if ($first->getId() === $second->getId()) {
            $this->logger->info("{$first->getId()} and {$second->getId()} are equal and similarity: " . CosineComputer::SIMILARITY_EQUAL);
            return CosineComputer::SIMILARITY_EQUAL;
        }

        // base case 2: the values could be cached. Check first!
        $cachedValue = $this->getCachedValue($first, $second);

        if (null !== $cachedValue && true === is_float($cachedValue)) {
            $this->logger->info("found a cached value for {$first->getId()} -> {$second->getId()}: $cachedValue");
            return $cachedValue;
        }

        // the values could also be cached in reverse order
        $cachedValue = $this->getCachedValue($second, $first);

        if (null !== $cachedValue && true === is_float($cachedValue)) {
            $this->logger->info("found a cached value for {$second->getId()} -> {$first->getId()}: $cachedValue");
            return $cachedValue;
        }

        // step 2: apply cosine based similarity function
        // since we determined the common raters and can be sure
        // the raters are present in both features, we can simply
        // iterate over the raters and pick the rating
        foreach ($first->getRaters()->keySet() as $firstRaterId) {
            /** @var IRater $firstRater */
            $firstRater  = $first->getRaters()->get($firstRaterId);
            $firstRating = $firstRater->getRating();

            if (false === $second->getRaters()->containsKey($firstRaterId)) continue;

            /** @var IRater $secondRater */
            $secondRater  = $second->getRaters()->get($firstRaterId);
            $secondRating = $secondRater->getRating();

            if (false === $this->inRange($firstRating)) {
                throw new InvalidRatingException();
            }
            if (false === $this->inRange($secondRating)) {
                throw new InvalidRatingException();
            }

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

        $this->cache($first, $second, $similarity);
        return $similarity;
    }

    public function getCachedValue(IFeature $first, IFeature $second): ?float {
        $f = $this->cache[$first->getId()] ?? null;
        if (null === $f) return null;

        $s = $f[$second->getId()] ?? null;
        if (null === $s) return null;

        return $s;
    }

    private function inRange(float $value): bool {
        return $this->floatService->isBetween(
            $value
            , $this->range->getLowerBound()
            , $this->range->getUpperBound()
            , true
        );
    }

    private function cache(IFeature $first, IFeature $second, float $similarity): void {
        // cache in regular order
        $f = $this->cache[$first->getId()] ?? null;
        if (null === $f) {
            $f = [];
        }
        $f[$second->getId()] = $similarity;
        $this->cache[$first->getId()] = $f;

        // cache in reverse order
        $s = $this->cache[$second->getId()] ?? null;
        if (null === $s) {
            $s = [];
        }
        $s[$first->getId()] = $similarity;
        $this->cache[$second->getId()] = $s;


    }


}
