<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

enum Action
{
    case update;
    case index;
    case store;
    case show;
    case all;
    case destroy;

    public function responseSucessoMessage(): string
    {
        return match ($this) {
            Action::update  => 'Atualizado com sucesso',
            Action::index   => 'Sucesso ao listar registros',
            Action::store   => 'Criado com sucesso',
            Action::show    => 'Registro encontrado',
            Action::all     => 'Sucesso ao listar todos registros',
            Action::destroy => 'Deletado com sucesso',
        };
    }

    public function responseErrorMessage(): string
    {
        return match ($this) {
            Action::update => 'Ocorreu um erro ao atualizar o registro',
            Action::index  => 'Ocorreu um erro ao listar registros',
            Action::store  => 'Ocorreu um erro ao criar o registros',
            Action::show   => 'Ocorreu um erro ao encontar o registros',
            Action::all    => 'Ocorreu um erro ao listar todos registros',
            Action::all    => 'Ocorreu um erro ao deletar o registro',
        };
    }
}

enum Type
{
    case success;
    case error;

    public function boolValue(): bool
    {
        return match ($this) {
            Type::success => true,
            Type::error   => false,
        };
    }

    public function status(): int
    {
        return match ($this) {
            Type::success => 200,
            Type::error   => 500,
        };
    }
}

class ResponseController extends Controller
{
    public static function json(Type $type, Action $action, $data = null, $customMsg = null)
    {
        return response()->json(
            [
                'success' => $type->boolValue(),
                'msg'
                    => is_null($customMsg)
                    ? ($type::success == Type::success
                        ? $action->responseSucessoMessage()
                        : $action->responseErrorMessage())
                    : $customMsg,
                ...(is_null($data) ? [] : ['data' => $data ]),
            ],
            $type->status()
        );
    }
}
