<?php

namespace App\Service;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class GameStorageService
{

    const KEY = 'game.games';

    /**
     * @var FilesystemAdapter
     */
    private $client;

    /**
     * @var mixed
     */
    private $item;

    /**
     * GameStorageService constructor.
     */
    public function __construct()
    {
        $this->client = new FilesystemAdapter();
        $this->item = $this->client->getItem(static::KEY);
    }

    /**
     * @param array $games
     */
    public function setGames(array $games)
    {
        $this->item->set($games);
        $this->client->save($this->item);
    }

    /**
     * @return array
     */
    public function getGames(): array
    {
        return $this->item->get() ?? [];
    }

}