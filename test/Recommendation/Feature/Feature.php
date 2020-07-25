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

namespace doganoo\Recommender\Test\Recommendation\Feature;

use doganoo\PHPAlgorithms\Datastructure\Graph\Tree\BinarySearchTree;
use doganoo\PHPAlgorithms\Datastructure\Table\HashTable;
use doganoo\Recommender\Recommendation\Feature\IFeature;
use doganoo\Recommender\Recommendation\Rater\IRater;

class Feature implements IFeature {

    /** @var int */
    private $id = 0;
    /** @var string */
    private $name = "";
    /** @var HashTable */
    private $raters;
    /** @var BinarySearchTree */
    private $ratersAsTree;

    public function __construct(int $id, string $name) {
        $this->id           = $id;
        $this->name         = $name;
        $this->raters       = new HashTable();
        $this->ratersAsTree = new BinarySearchTree();
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
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void {
        $this->name = $name;
    }

    /**
     * @return HashTable
     */
    public function getRaters(): HashTable {
        return $this->raters;
    }

    public function addRater(IRater $rater): void {
        $this->raters->put($rater->getId(), $rater);
        $this->ratersAsTree->insertValue($rater);
    }

    /**
     * @return BinarySearchTree
     */
    public function getRatersAsTree(): BinarySearchTree {
        return $this->ratersAsTree;
    }

    /**
     * @param BinarySearchTree $ratersAsTree
     */
    public function setRatersAsTree(BinarySearchTree $ratersAsTree): void {
        $this->ratersAsTree = $ratersAsTree;
    }

}
