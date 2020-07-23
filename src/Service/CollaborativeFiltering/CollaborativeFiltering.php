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
use doganoo\PHPAlgorithms\Datastructure\Table\HashTable;
use doganoo\Recommender\Recommendation\Feature\IFeature;
use doganoo\Recommender\Repository\Feature\IFeatureRepository;
use doganoo\Recommender\Service\IRecommendationService;

class CollaborativeFiltering implements IRecommendationService {

    /** @var IFeatureRepository */
    private $featureRepository;
    /** @var CosineComputer */
    private $cosineComputer;
    /** @var HashTable */
    private $resultTable;
    /** @var float */
    private $thresholdPercent;
    /** @var IFloatService */
    private $floatService;
    /** @var array */
    private $cache = [];

    public function __construct(
        IFeatureRepository $featureRepository
        , CosineComputer $cosineComputer
        , IFloatService $floatService
    ) {
        $this->featureRepository = $featureRepository;
        $this->cosineComputer    = $cosineComputer;
        $this->floatService      = $floatService;
        $this->reset();
    }

    public function reset(): void {
        $this->resultTable = new HashTable();
        $this->setThreshold(0.0);
        $this->cache = [];
    }

    /**
     * @param float $threshold
     */
    public function setThreshold(float $threshold): void {
        $this->thresholdPercent = $threshold;
    }

    public function getWeight(): float {
        return (float) 1;
    }

    public function calculate(IFeature $baseFeature): void {

        foreach ($this->featureRepository->getFeatures() as $feature) {

            if ($baseFeature->getId() === $feature->getId()) continue;

            $similarity = $this->getCachedValue($baseFeature, $feature);

            if (null !== $similarity && is_float($similarity)) {
                $this->put($feature, $similarity);
                continue;
            }

            $similarity = $this->cosineComputer->compute($baseFeature, $feature);
            $this->put($feature, $similarity);
        }
    }

    public function getCachedValue(IFeature $first, IFeature $second): ?float {
        $value1 = $this->getCached($first, $second);
        if (null !== $value1) return $value1;
        return $this->getCached($second, $first);
    }

    public function getCached(IFeature $first, IFeature $second): ?float {
        return ($this->cache[$first->getId()] ?? null) [$second->getId()] ?? null;
    }

    private function put(IFeature $feature, float $similarity) {

        if (true === $this->floatService->greaterThan(
                $similarity
                , $this->getThreshold()
                , true
            )
        ) {
            $this->resultTable->put(
                $feature
                , $similarity
            );

        }

    }

    /**
     * @return float
     */
    public function getThreshold(): float {
        return $this->thresholdPercent;
    }

    public function getResult(): HashTable {
        return $this->resultTable;
    }

    public function getName(): string {
        return CollaborativeFiltering::class;
    }

}
