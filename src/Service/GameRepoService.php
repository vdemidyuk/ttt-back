<?php

namespace App\Service;

use App\Model\Game;

class GameRepoService
{

    /**
     * GameRepoService constructor.
     * @param GameStorageService $gameStorageService
     */
    public function __construct(GameStorageService $gameStorageService)
    {
        $this->gameStorageService = $gameStorageService;
    }

    public function deleteGameById(string $game_id)
    {
        $games = $this->gameStorageService->getGames();
        $key_id = -1;
        foreach($games as $key => $game) {
            if($game->getId() === $game_id) {
                $key_id = $key;
            }
        }

        if($key_id >= 0) {
            unset($games[$key_id]);
            $games = array_values($games);
            $this->gameStorageService->setGames($games);
        }
    }

    public function setGameById(string $game_id, Game $game)
    {
        $games = $this->gameStorageService->getGames();
        $key_id = -1;
        foreach($games as $key => $gameCheck) {
            if($gameCheck->getId() === $game_id) {
                $key_id = $key;
            }
        }

        if($key_id >= 0) {
            $games[$key_id] = $game;
            $this->gameStorageService->setGames($games);
        }
    }

    /**
     * @param string $game_id
     * @return mixed
     * @throws \Exception
     */
    public function getGameById(string $game_id)
    {
        $games = $this->gameStorageService->getGames();
        foreach($games as $game) {
            if($game->getId() === $game_id) {
                return $game;
            }
        }
        throw new \Exception('Not found', 404);
    }

}