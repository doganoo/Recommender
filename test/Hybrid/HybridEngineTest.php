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

namespace doganoo\Recommender\Test\Hybrid;

use doganoo\DIP\Object\Float\FloatService;
use doganoo\PHPAlgorithms\Common\Exception\InvalidKeyTypeException;
use doganoo\PHPAlgorithms\Common\Exception\UnsupportedKeyTypeException;
use doganoo\Recommender\Engine\HybridEngine;
use doganoo\Recommender\Exception\WeightTooLessException;
use doganoo\Recommender\Exception\WeightTooMuchException;
use doganoo\Recommender\Recommendation\Feature\IFeature;
use doganoo\Recommender\Service\CollaborativeFiltering\CollaborativeFiltering;
use doganoo\Recommender\Service\CollaborativeFiltering\CosineComputer;
use doganoo\Recommender\Service\CollaborativeFiltering\Rating\Range\BinaryRange;
use doganoo\Recommender\Test\Repository\Feature\FeatureRepository;
use doganoo\Recommender\Test\Suite\TestCase;
use Psr\Log\NullLogger;

class HybridEngineTest extends TestCase {

    /**
     * @throws InvalidKeyTypeException
     * @throws UnsupportedKeyTypeException
     * @throws WeightTooLessException
     * @throws WeightTooMuchException
     */
    public function testEngine() {
        $floatService = new FloatService();
        $engine       = new HybridEngine(
            $floatService
        );

        $featureRepository = new FeatureRepository();

        $engine->register(
            new CollaborativeFiltering(
                $featureRepository
                , new CosineComputer(
                    $floatService
                    , new BinaryRange()
                    , new NullLogger()
                )
                , $floatService
            ));


        $results = [
            "D1,D2,0.70710678118655"
            , "D1,D3,0.40824829046386"
            , "D2,D1,0.70710678118655"
            , "D2,D3,0.57735026918963"
            , "D2,D5,0.40824829046386"
            , "D3,D1,0.40824829046386"
            , "D3,D2,0.57735026918963"
            , "D3,D5,0.5"
            , "D5,D2,0.40824829046386"
            , "D5,D3,0.5"
        ];

        /** @var IFeature $feature */
        foreach ($featureRepository->getFeatures() as $feature) {
            $result = $engine->getRecommendations($feature);

            /** @var IFeature $f */
            foreach ($result->keySet() as $f) {
                $this->assertTrue(true === in_array("{$feature->getName()},{$f->getName()},{$result->get($f)}", $results));
            }
        }

    }

}
