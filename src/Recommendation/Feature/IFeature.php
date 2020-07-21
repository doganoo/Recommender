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

namespace doganoo\Recommender\Recommendation\Feature;

use doganoo\PHPAlgorithms\Datastructure\Table\HashTable;

/**
 * Interface IFeature
 *
 * @package doganoo\Recommender\Recommendation\Feature
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
interface IFeature {

    /**
     * Returns the ID that identifies the feature uniquely
     *
     * @return int
     */
    public function getId(): int;

    /**
     * Returns the name of the feature
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Returns all raters of the feature
     *
     * @return HashTable
     */
    public function getRaters(): HashTable;

}
