<?php 

namespace App\Model;

use Symfony\Component\Uid\Uuid;

class Game implements \JsonSerializable {

    const RUNNING = 'RUNNING';
    const X_WON = 'X_WON';
    const O_WON = 'O_WON';
    const DRAW = 'DRAW';

    private $id;
    private $board;
    private $status;

    public function __construct()
    {
        // dear lord of randomness...
        $this->setId(Uuid::v4()->toRfc4122());
        // amen
        $this->setStatus(static::RUNNING);
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getBoard()
    {
        return $this->board;
    }

    public function setBoard($board)
    {
        $this->board = $board;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'board' => $this->getBoard(),
            'status' => $this->getStatus(),
        ];
    }

}