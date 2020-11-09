<?php

namespace App\Service;

use App\Model\Game;

class GameService
{
    /**
     * GameService constructor.
     * @param GameStorageService $gameStorageService
     * @param GameLogicService $gameLogicService
     * @param GameRepoService $gameRepoService
     */
    public function __construct(GameStorageService $gameStorageService,
                                GameLogicService $gameLogicService,
                                GameRepoService $gameRepoService)
    {
        $this->gameStorageService = $gameStorageService;
        $this->gameLogicService = $gameLogicService;
        $this->gameRepoService = $gameRepoService;
    }

    public function deleteGameById(string $game_id)
    {
        $this->gameRepoService->deleteGameById($game_id);
    }

    public function setGameById(string $game_id, Game $game)
    {
        $this->gameRepoService->setGameById($game_id, $game);
    }

    public function getGameById(string $game_id)
    {
        return $this->gameRepoService->getGameById($game_id);
    }

    public function newGame(string $board)
    {
        $this->gameLogicService->validateFirstMove($board);
        $board = $this->gameLogicService->makeAIMove($board);

        $game = new Game();
        $game->setBoard($board);

        $games = $this->gameStorageService->getGames();
        // to the top for faster search
        array_unshift($games, $game);
        $this->gameStorageService->setGames($games);

        return $game;
    }

    public function placeMove(Game $game, string $board): Game
    {
        if($this->checkGameStatus($game->getBoard()) !== Game::RUNNING) {
            throw new \Exception('Game already finished');
        }
        if($this->gameLogicService->validateMove($game->getBoard(), $board)) {
            // let's see, if last move changed status
            $status = $this->checkGameStatus($board);
            if($status === Game::RUNNING) {
                $board = $this->gameLogicService->makeAIMove($board);
                $status = $this->checkGameStatus($board);
            }
            $game->setBoard($board);
            $game->setStatus($status);
            $this->setGameById($game->getId(), $game);
        }
        return $game;
    }

    protected function checkGameStatus(string $board): string
    {
        return $this->gameLogicService->getGameStatus($board);
    }

}