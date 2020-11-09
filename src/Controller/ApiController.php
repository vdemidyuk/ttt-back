<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Service\GameStorageService;
use App\Service\GameService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ApiController extends AbstractController
{

    public function makeMove(Request $request, GameService $gameService): Response
    {
        try {
            $game = $gameService->getGameById($request->get('game_id'));

            $board = $request->get('board');

            $game = $gameService->placeMove($game, $board);

            return $this->json($game);
        } catch (\Exception $e) {
            return new Response($e->getMessage(), $e->getCode());
        }
    }

    public function newGame(Request $request, GameService $gameService): Response
    {
        try {

            $board = $request->get('board');

            $game = $gameService->newGame($board);

            $url = $this->generateUrl(
                'api_new_game',
                ['game_id' => $game->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $location = explode('?', $url)[0].'/'.$game->getId();

            return $this->json([
                'location' => $location,
            ]);

        } catch (\Exception $e) {
            return new Response($e->getMessage(), $e->getCode());
        }

    }

    public function getGame(Request $request, GameService $gameService): Response
    {
        try {
            $game = $gameService->getGameById($request->get('game_id'));
            return $this->json($game);
        } catch (\Exception $e) {
            return new Response($e->getMessage(), $e->getCode());
        }
    }

    public function deleteGame(Request $request, GameService $gameService): Response
    {
        try {
            $gameService->getGameById($request->get('game_id'));
            $gameService->deleteGameById($request->get('game_id'));
            return $this->json('');
        } catch (\Exception $e) {
            return new Response($e->getMessage(), $e->getCode());
        }
    }

    public function getGames(GameStorageService $gameStorageService): Response
    {
        return $this->json($gameStorageService->getGames());
    }

    public function flushGames(GameStorageService $gameStorageService): Response
    {
        $gameStorageService->setGames([]);
        return $this->json($gameStorageService->getGames());
    }

}
