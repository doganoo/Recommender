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

namespace doganoo\Recommender\Test\Recommendation\Rater;

use doganoo\PHPAlgorithms\Common\Util\Comparator;
use doganoo\Recommender\Recommendation\Rater\IRater;

class Rater implements IRater {

    /** @var int */
    private $id;
    /** @var float */
    private $rating;

    public function __construct(int $id, float $rating) {
        $this->setId($id);
        $this->setRating($rating);
    }

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void {
        $this->id = $id;
    }

    /**
     * @return float
     */
    public function getRating(): float {
        return $this->rating;
    }

    /**
     * @param float $rating
     */
    public function setRating(float $rating): void {
        $this->rating = $rating;
    }

    public function compareTo($object): int {
        if ($object instanceof IRater) {
            if (Comparator::equals($this->getId(), $object->getId())) return 0;
            if (Comparator::lessThan($this->getId(), $object->getId())) return -1;
            if (Comparator::greaterThan($this->getId(), $object->getId())) return 1;
        }
        return -1;
    }

}
