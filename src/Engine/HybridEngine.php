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

namespace doganoo\Recommender\Engine;

use doganoo\DI\Object\Float\IFloatService;
use doganoo\PHPAlgorithms\Common\Exception\InvalidKeyTypeException;
use doganoo\PHPAlgorithms\Common\Exception\UnsupportedKeyTypeException;
use doganoo\PHPAlgorithms\Datastructure\Table\HashTable;
use doganoo\Recommender\Exception\InvalidRatingException;
use doganoo\Recommender\Exception\WeightTooLessException;
use doganoo\Recommender\Exception\WeightTooMuchException;
use doganoo\Recommender\Recommendation\Feature\IFeature;
use doganoo\Recommender\Service\IRecommendationService;

/**
 * Class HybridEngine
 *
 * The main entry point for recommendations.
 *
 * All recommendation services are registered at the engine.
 * The engine runs over all services and features to make recommendations.
 *
 * @package doganoo\Recommender\Engine
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class HybridEngine {

    /** @var IRecommendationService[] */
    private $services = [];

    /** @var IFloatService */
    private $floatService;

    /** @var float */
    private $recommendationThreshold;

    /**
     * HybridEngine constructor.
     *
     * @param IFloatService $floatService
     */
    public function __construct(IFloatService $floatService) {
        $this->floatService = $floatService;
    }

    /**
     * Registers a recommendation service
     *
     * @param IRecommendationService $recommendationService
     */
    public function register(IRecommendationService $recommendationService) {
        $this->services[] = $recommendationService;
    }

    /**
     * Returns a table of recommendations for the given feature.
     *
     * The recommendations are calculated by the given services. Each service
     * must provide a weight between 0 and 1 which is to multiply the resulting
     * similarity with the weight.
     *
     * All weights have to sum up to 1, otherwise this method will throw an exception
     *
     * @param IFeature $feature The feature to whom the recommendations are seeked
     *
     * @return HashTable A Table of similar features
     * @throws InvalidRatingException thrown when a rating is out of bounds
     * @throws WeightTooLessException thrown when the total sum of weights is less than 1
     * @throws WeightTooMuchException thrown when the total sum of weights is greater than 1
     */
    public function getRecommendations(IFeature $feature): HashTable {
        $totalWeight = 0;
        $allResults  = new HashTable();

        foreach ($this->services as $service) {
            $weight      = $service->getWeight();
            $totalWeight = $totalWeight + $weight;

            if ($this->floatService->greaterThan($totalWeight, 1, false)) {
                throw new WeightTooMuchException();
            }

            $results    = $this->getRecommendationsForService($service, $feature);
            $allResults = $this->mergeRecommendations($allResults, $results, $service->getWeight());
        }

        if ($this->floatService->lessThan($totalWeight, 1, false)) {
            throw new WeightTooLessException();
        }

        return $allResults;
    }

    /**
     * Makes recommendations using a given service.
     *
     * The recommendations for a feature are measured by a given service.
     *
     * @param IRecommendationService $service The service that measures
     * @param IFeature               $feature The feature to that recommendations are seeked
     *
     * @return HashTable The table of recommendations
     * @throws InvalidKeyTypeException thrown when a key is not found in a HashTable (should not happen)
     * @throws UnsupportedKeyTypeException thrown when a key is not found in a HashTable (should not happen)
     */
    private function getRecommendationsForService(IRecommendationService $service, IFeature $feature): HashTable {
        $results = new HashTable();

        $service->reset();
        $service->calculate($feature);
        $candidateFeatures = $service->getResult();

        /** @var IFeature $candidateFeature */
        foreach ($candidateFeatures->keySet() as $candidateFeature) {
            $similarity = $candidateFeatures->get($candidateFeature);

            if ($feature->getId() === $candidateFeature->getId()) continue;

            if (true === $this->floatService->greaterThan(
                    $similarity
                    , $this->getRecommendationThreshold()
                    , true
                )
            ) {
                $results->put($candidateFeature, $similarity);
            }
        }
        return $results;
    }

    /**
     * @return float
     */
    public function getRecommendationThreshold(): float {
        return $this->recommendationThreshold;
    }

    /**
     * @param float $recommendationThreshold
     */
    public function setRecommendationThreshold(float $recommendationThreshold): void {
        $this->recommendationThreshold = $recommendationThreshold;
    }

    /**
     * Merges all recommendations to a final table
     *
     * The recommendations are merged into a final HashTable using the weighted average method
     *
     * @param HashTable $allResults    The resulting HashTable with recommendations
     * @param HashTable $serviceResult The HashTable with recommendations to merge into allResults
     * @param float     $weight        The weight
     *
     * @return HashTable merged results
     * @throws InvalidKeyTypeException thrown when a key is not found in a HashTable (should not happen)
     * @throws UnsupportedKeyTypeException thrown when a key is not found in a HashTable (should not happen)
     */
    private function mergeRecommendations(HashTable $allResults, HashTable $serviceResult, float $weight): HashTable {

        /** @var IFeature $feature */
        foreach ($serviceResult->keySet() as $feature) {

            $similarity = 0;

            if (true === $allResults->containsKey($feature)) {
                $similarity = $allResults->get($feature);
            }

            $allResults->put($feature
                , $similarity + ($weight * $serviceResult->get($feature)
                )
            );

        }

        return $allResults;
    }

}
