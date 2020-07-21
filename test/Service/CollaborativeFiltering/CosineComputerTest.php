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

namespace doganoo\Recommender\Test\Service\CollaborativeFiltering;

use doganoo\DI\Object\Float\IFloatService;
use doganoo\DIP\Object\Float\FloatService;
use doganoo\PHPAlgorithms\Common\Exception\InvalidKeyTypeException;
use doganoo\PHPAlgorithms\Common\Exception\UnsupportedKeyTypeException;
use doganoo\Recommender\Recommendation\Feature\IFeature;
use doganoo\Recommender\Service\CollaborativeFiltering\CosineComputer;
use doganoo\Recommender\Service\CollaborativeFiltering\Rating\Range\BinaryRange;
use doganoo\Recommender\Service\HashTableService;
use doganoo\Recommender\Test\Recommendation\Feature\Feature;
use doganoo\Recommender\Test\Recommendation\Rater\Rater;
use doganoo\Recommender\Test\Suite\TestCase;

class CosineComputerTest extends TestCase {

    /** @var CosineComputer */
    private $cosineComputer;

    /** @var IFloatService */
    private $floatService;

    /**
     * @param IFeature $first
     * @param IFeature $second
     * @param float    $similarity
     *
     * @throws InvalidKeyTypeException
     * @throws UnsupportedKeyTypeException
     * @dataProvider getFeatures
     */
    public function testCosineComputer(IFeature $first, IFeature $second, float $similarity) {
        $resultSimilarity = $this->cosineComputer->compute($first, $second);
        $this->assertTrue(true === $this->floatService->equals($resultSimilarity, $similarity));
    }

    public function getFeatures() {

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

        return [
            [$d1, $d2, 0.7071067811]
            , [$d1, $d3, 0.40824829046386]
            , [$d1, $d4, 0]
            , [$d1, $d5, 0]

            , [$d2, $d3, 0.57735026918963]
            , [$d2, $d4, 0]
            , [$d2, $d5, 0.40824829046386]

            , [$d3, $d4, 0]
            , [$d3, $d5, 0.5]

            , [$d4, $d5, 0]
        ];
    }

    protected function setUp() {
        parent::setUp();
        $this->floatService   = new FloatService();
        $this->cosineComputer = new CosineComputer(
            $this->floatService
            , new BinaryRange()
        );
    }

}
