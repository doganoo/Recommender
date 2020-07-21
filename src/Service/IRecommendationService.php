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

namespace doganoo\Recommender\Service;

use doganoo\PHPAlgorithms\Datastructure\Table\HashTable;
use doganoo\Recommender\Recommendation\Feature\IFeature;

/**
 * Interface IRecommendationService
 *
 * @package doganoo\Recommender\Service
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
interface IRecommendationService {

    /**
     * Returns the weight of the recommendation result
     * within the total recommendation services
     *
     * The weight should be coordinated with other
     * recommendation services as they have to result
     * in 1 in total
     *
     * @return float
     */
    public function getWeight(): float;

    /**
     * Calculates all recommendations for a given feature
     *
     * @param IFeature $baseFeature
     */
    public function calculate(IFeature $baseFeature): void;

    /**
     * Returns the results as a HashTable with the format:
     *
     *      IFeature -> similarity
     *
     * @return HashTable
     */
    public function getResult(): HashTable;

    /**
     * Resets the recommendation service for a fresh calculation
     */
    public function reset(): void;

    /**
     * Returns the name of the service
     *
     * @return string
     */
    public function getName(): string;

}
