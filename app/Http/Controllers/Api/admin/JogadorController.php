<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Action;
use App\Http\Controllers\Api\ResponseController;
use App\Http\Controllers\Api\Type;
use App\Http\Controllers\Controller;
use App\Models\Jogador;
use Throwable;

class JogadorController extends Controller
{
    public function all()
    {
        try {
            $data = Jogador::all();
            return  ResponseController::json(Type::success, Action::index, $data);
        } catch(Throwable $e) {
            return ResponseController::json(Type::error, Action::index);
        }
    }
}
