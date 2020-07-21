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

    public function __construct(
        IFeatureRepository $featureRepository
        , CosineComputer $cosineComputer
    ) {
        $this->featureRepository = $featureRepository;
        $this->cosineComputer    = $cosineComputer;
        $this->reset();
    }

    public function reset(): void {
        $this->resultTable = new HashTable();
    }

    public function getWeight(): float {
        return (float) 1;
    }

    public function calculate(IFeature $baseFeature): void {

        foreach ($this->featureRepository->getFeatures() as $feature) {

            $this->resultTable->put(
                $feature
                , $this->cosineComputer->compute($baseFeature, $feature)
            );

        }
    }

    public function getResult(): HashTable {
        return $this->resultTable;
    }

    public function getName(): string {
        return CollaborativeFiltering::class;
    }

}
