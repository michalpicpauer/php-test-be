<?php

namespace App\DTO;

class MySqlWatchDTO
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $title;

    /**
     * @var int
     */
    public $price;

    /**
     * @var string
     */
    public $description;

    /**
     * MySqlWatchDTO constructor.
     * @param int    $id
     * @param string $title
     * @param int    $price
     * @param string $description
     */
    public function __construct(int $id, string $title, int $price, string $description)
    {
        $this->id = $id;
        $this->title = $title;
        $this->price = $price;
        $this->description = $description;
    }

    public function getArray()
    {
        return [
            'identification' => $this->id,
            'title'          => $this->title,
            'price'          => $this->price,
            'description'    => $this->description,
        ];
    }

}