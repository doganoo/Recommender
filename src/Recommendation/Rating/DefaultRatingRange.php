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

namespace doganoo\Recommender\Recommendation\Rating;

/**
 * Class DefaultRatingRange
 *
 * @package doganoo\Recommender\Recommendation\Rater
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class DefaultRatingRange implements IRatingRange {

    /**
     * Returns the Lower Bound of the rating, e.g. 0
     *
     * @return float
     */
    public function getLowerBound(): float {
        return (float) 0;
    }

    /**
     * Returns an absolute value that denotes the recommendation value
     *
     * @return float
     */
    public function getRecommendationThreshold(): float {
        return (float) $this->getUpperBound() * 0.2;
    }

    /**
     * Returns the Upper Bound of the rating, e.g. 5
     *
     * @return float
     */
    public function getUpperBound(): float {
        return (float) 5;
    }

}
