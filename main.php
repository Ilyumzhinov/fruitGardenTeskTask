<?php
// MARK: - Types
class Fruit
{
    public function __construct(
        public float $weight
    ) {
    }
}

/** Genertic Tree interface.
 * Contains fruits attribute. Initialises unique tree ID. */
abstract class TreeType
{
    public string $tree_id;
    // array<Fruit>
    public array $fruits;

    function __construct()
    {
        $this->tree_id = "tree_" . uniqid();
    }

    /** Counts the total fruits weight */
    function sum_weight(): float
    {
        return array_reduce($this->fruits, fn ($accum, $fruit) => $accum + $fruit->weight, 0);
    }
}

/** Represents an Apple tree */
class AppleTree extends TreeType
{
    function __construct(int $n_apples_min = 40, int $n_apples_max = 50)
    {
        parent::__construct();

        // Create apples within specified range
        $this->fruits = array_map(
            fn () => new Fruit(floatval(rand(150, 180))),
            range(1, rand($n_apples_min, $n_apples_max))
        );
    }
}

/** Represents a Pear tree */
class PearTree extends TreeType
{
    function __construct(int $n_pears_min = 0, int $n_pears_max = 20)
    {
        parent::__construct();

        $n_pears = rand($n_pears_min, $n_pears_max);

        // Create apples within specified range
        // If number of pears == 0, do not create Fruits
        $this->fruits = ($n_pears != 0) ?
            array_map(
                fn () => new Fruit(floatval(rand(130, 170))),
                range(1, $n_pears)
            ) : [];
    }
}

/** Managers Apple and Pear trees */
class Garden
{
    private array $trees;

    function __construct(int $n_apple = 10, int $n_pears = 15)
    {
        $apples = array_map(fn () => new AppleTree(), range(1, $n_apple));
        $pears = array_map(fn () => new PearTree(), range(1, $n_pears));
        $this->trees = array_merge($apples, $pears);
    }

    /** Allows adding additional trees to the garden */
    function add_tree(TreeType $tree)
    {
        array_push($this->trees, $tree);
    }

    /** Counts fruits and weight for trees of certain type
     * @param Type $type Meta type of tree. E.g. AppleTree or PearTree.
     * @return array ["count" => int, "weight" => float]
     */
    function count_trees($type): array
    {
        $apples = array_filter($this->trees, fn ($tree) => $tree instanceof $type);

        $count_apple = fn ($accum, $tree) => [
            "count" => $accum["count"] + count($tree->fruits),
            "weight" => $accum["weight"] + $tree->sum_weight()
        ];

        return array_reduce($apples, $count_apple, ["count" => 0, "weight" => 0]);
    }

    /** Counts fruits and weight for Apple trees */
    function count_apples(): array
    {
        return $this->count_trees("AppleTree");
    }

    /** Counts fruits and weight for Pear trees */
    function count_pears(): array
    {
        return $this->count_trees("PearTree");
    }
}


// MARK: - Main
$garden = new Garden();
$apples = $garden->count_apples();
$pears = $garden->count_pears();

echo "Собрано " . $apples["count"] . " яблок весом " . $apples["weight"] . " гр." . "\n";
echo "Собрано " . $pears["count"] . " груш весом " . $pears["weight"] . " гр." . "\n";