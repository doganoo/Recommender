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

namespace doganoo\Recommender\Test\Repository\Feature;

use doganoo\Recommender\Repository\Feature\IFeatureRepository;
use doganoo\Recommender\Test\Recommendation\Feature\Feature;
use doganoo\Recommender\Test\Recommendation\Rater\Rater;

class FeatureRepository implements IFeatureRepository {

    public function getFeatures(): array {
        $features = [];

        $d1 = new Feature(1, "D1");
        $d1->addRater(new Rater(1, 0));
        $d1->addRater(new Rater(2, 1));
        $d1->addRater(new Rater(3, 0));
        $d1->addRater(new Rater(4, 0));
        $d1->addRater(new Rater(5, 1));

        $d2 = new Feature(2, "D2");
        $d2->addRater(new Rater(1, 0));
        $d2->addRater(new Rater(2, 1));
        $d2->addRater(new Rater(3, 1));
        $d2->addRater(new Rater(4, 1));
        $d2->addRater(new Rater(5, 1));

        $d3 = new Feature(3, "D3");
        $d3->addRater(new Rater(1, 1));
        $d3->addRater(new Rater(2, 0));
        $d3->addRater(new Rater(3, 0));
        $d3->addRater(new Rater(4, 1));
        $d3->addRater(new Rater(5, 1));

        $d4 = new Feature(4, "D4");
        $d4->addRater(new Rater(1, 0));
        $d4->addRater(new Rater(2, 0));
        $d4->addRater(new Rater(5, 0));

        $d5 = new Feature(5, "D5");
        $d5->addRater(new Rater(1, 1));
        $d5->addRater(new Rater(2, 0));
        $d5->addRater(new Rater(3, 1));
        $d5->addRater(new Rater(4, 0));

        $features[] = $d1;
        $features[] = $d2;
        $features[] = $d3;
        $features[] = $d4;
        $features[] = $d5;

        return $features;
    }


}
