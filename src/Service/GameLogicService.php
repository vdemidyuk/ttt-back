<?php

namespace App\Service;

use App\Model\Game;

class GameLogicService
{
    const X = 'X';
    const O = 'O';
    const FRONT_PLAY_CARD = self::X;
    const BACK_PLAY_CARD = self::O;
    const FREE_SPACE = '-';

    const wins = [
        [0, 1, 2],
        [3, 4, 5],
        [6, 7, 8],
        [0, 3, 6],
        [1, 4, 7],
        [2, 5, 8],
        [0, 4, 8],
        [2, 4, 6]
    ];

    public function __construct()
    {

    }

    public function movesAvailable(string $board): bool
    {
        $boardState = str_split($board);
        foreach($boardState as $tile) {
            if($tile === static::FREE_SPACE) {
                return true;
            }
        }
        return false;
    }

    public function getGameStatus(string $board): string
    {
        $boardState = str_split($board);
        foreach(static::wins as $win) {
            $a = $boardState[$win[0]];
            $b = $boardState[$win[1]];
            $c = $boardState[$win[2]];

            if($a === $b && $b === $c && $c !== static::FREE_SPACE) {
                return $c === static::X ? Game::X_WON : Game::O_WON;
            }
        }

        if(!$this->movesAvailable($board)) {
            return Game::DRAW;
        }

        return Game::RUNNING;
    }

    public function measureWeight(array $boardState, array $winCombination, $againstCard = self::FRONT_PLAY_CARD): int
    {
        $weight = 0;
        
        $weight += $boardState[$winCombination[0]] === $againstCard ? 1 : 0;
        $weight += $boardState[$winCombination[1]] === $againstCard ? 1 : 0;
        $weight += $boardState[$winCombination[2]] === $againstCard ? 1 : 0;

        $weight += $boardState[$winCombination[0]] !== $againstCard 
            && $boardState[$winCombination[0]] !== static::FREE_SPACE ? -3 : 0;
        $weight += $boardState[$winCombination[1]] !== $againstCard 
            && $boardState[$winCombination[1]] !== static::FREE_SPACE ? -3 : 0;
        $weight += $boardState[$winCombination[2]] !== $againstCard 
            && $boardState[$winCombination[2]] !== static::FREE_SPACE ? -3 : 0;

        return $weight;
    }

    public function makeAIMove(string $board): string
    {
        $boardState = str_split($board);
        
        foreach(static::wins as $win) {

            //check if we can make final winning move
            $weight = $this->measureWeight($boardState, $win, static::BACK_PLAY_CARD);
            if($weight >= 2) {
                for($i = 0; $i <= 2; $i++) {
                    if($boardState[$win[$i]] === static::FREE_SPACE) {
                        $boardState[$win[$i]] = static::BACK_PLAY_CARD;
                        return implode($boardState);
                    }
                }    
            }

            // check if we need to block to prevent win
            $weight = $this->measureWeight($boardState, $win);
            if($weight >= 2) {
                for($i = 0; $i <= 2; $i++) {
                    if($boardState[$win[$i]] === static::FREE_SPACE) {
                        $boardState[$win[$i]] = static::BACK_PLAY_CARD;
                        return implode($boardState);
                    }
                }    
            }

        }

        // special cases
        if($boardState == str_split('--X------')) {
            $boardState[6] = static::BACK_PLAY_CARD;
            return implode($boardState);
        }
        if($boardState == str_split('------X--')) {
            $boardState[2] = static::BACK_PLAY_CARD;
            return implode($boardState);
        }

        // check if center is available
        if($boardState[4] === static::FREE_SPACE) {
            $boardState[4] = static::BACK_PLAY_CARD;
            return implode($boardState);
        }

        // play any
        foreach($boardState as $key => $tile) {
            if($tile === static::FREE_SPACE) {
                $boardState[$key] = static::BACK_PLAY_CARD;
                return implode($boardState);
            }
        }

        return implode($boardState);
    }

    /**
     * @param string $prevBoard
     * @param string $board
     * @param string $currentMove
     * @return bool
     * @throws \Exception
     */
    public function validateMove(string $prevBoard, string $board, $currentMove = self::FRONT_PLAY_CARD)
    {
        $prevBoardState = str_split($prevBoard);
        $boardState = str_split($board);

        if(count($prevBoardState) !== count($boardState) || count($boardState) !== 9) {
            throw new \Exception('Impossible, my friend!', 400);
        }

        $moveDetected = false;
        foreach($prevBoardState as $key => $tile) {
            if($moveDetected && $boardState[$key] !== $prevBoardState[$key]) {
                throw new \Exception('Impossible, my friend!', 400);
            }

            if(!$moveDetected && $prevBoardState[$key] !== $boardState[$key] && $boardState[$key] !== $currentMove) {
                throw new \Exception('Impossible, my friend!', 400);
            }

            if(!$moveDetected && $boardState[$key] !== $prevBoardState[$key] && $boardState[$key] === $currentMove) {
                $moveDetected = true;
            }
        }

        if(!$moveDetected) {
            throw new \Exception('Impossible, my friend!', 400);
        }

        return true;
    }

    public function validateFirstMove(string $board)
    {
        return $this->validateMove('---------', $board);
    }

}