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
    case listbyId;
    case login;
    case logout;
    case validator;
    case find;

    public function responseSucessoMessage(): string
    {
        return match ($this) {
            Action::update       => 'Atualizado com sucesso',
            Action::index        => 'Sucesso ao listar registros',
            Action::store        => 'Criado com sucesso',
            Action::show         => 'Registro encontrado',
            Action::all          => 'Sucesso ao listar todos registros',
            Action::destroy      => 'Deletado com sucesso',
            Action::listbyId     => 'Sucesso ao listar registros',
            Action::login        => 'Sucesso',
            Action::logout       => 'Deslogado com sucesso',
            Action::validator    => 'Campos válidos',
            Action::find         => 'Recurso encontrado'
        };
    }

    public function responseErrorMessage(): string
    {
        return match ($this) {
            Action::update       => 'Ocorreu um erro ao atualizar o registro',
            Action::index        => 'Ocorreu um erro ao listar registros',
            Action::store        => 'Ocorreu um erro ao criar o registros',
            Action::show         => 'Ocorreu um erro ao encontar o registros',
            Action::all          => 'Ocorreu um erro ao listar todos registros',
            Action::destroy      => 'Ocorreu um erro ao deletar o registro',
            Action::listbyId     => 'Ocorreu um erro ao listar registros',
            Action::login        => 'Não autorizado',
            Action::logout       => 'Ocorreu um erro ao efetuar o deslog',
            Action::validator    => 'Os dados fornecidos são invalidos',
            Action::find         => 'Recurso não encontrado'
        };
    }
}

enum Type
{
    case success;
    case error;
    case exception;
    case notAuthentication;
    case validator;
    case notFound;
    case methodNotAllowed;

    public function boolValue(): bool
    {
        return match ($this) {
            Type::success           => true,
            Type::error             => false,
            Type::exception         => false,
            Type::notAuthentication => false,
            Type::validator         => false,
            Type::notFound          => false,
            Type::methodNotAllowed  => false,
        };
    }

    public function status(): int
    {
        return match ($this) {
            Type::success           => 200,
            Type::error             => 200,
            Type::exception         => 500,
            Type::notAuthentication => 401,
            Type::validator         => 422,
            Type::notFound          => 404,
            Type::methodNotAllowed  => 405,
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
                    ? ($type->boolValue()
                        ? $action->responseSucessoMessage()
                        : $action->responseErrorMessage())
                    : $customMsg,
                ...(is_null($data) ? [] : ['data' => $data ]),
            ],
            $type->status()
        );
    }
}
