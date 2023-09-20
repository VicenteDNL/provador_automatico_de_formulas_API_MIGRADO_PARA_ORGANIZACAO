<?php

namespace App\Http\Controllers\Api\Aluno;

use App\Http\Controllers\Controller;
use App\Models\Exercicio;
use App\Models\Jogador;
use App\Models\Resposta;
use DateTime;

class RespostaController extends Controller
{
    public function saudeResposta(Jogador $jogador, Exercicio $exercicio, bool $isCriacao = false)
    {
        $resposta = Resposta::where(['jogador_id' => $jogador->id, 'exercicio_id' => $exercicio->id])->firstOrFail();
        $recompensa = $exercicio->recompensa()->firstOrFail();
        return
        [
            'tempo'      => $exercicio->tempo == null
                ? null
                : [
                    'atual'   => $isCriacao ? $exercicio->tempo : $this->tempoRestante($resposta, $exercicio),
                    'inicial' => $exercicio->tempo,
                ],
            'tentativas' => $exercicio->qndt_erros == null
                ? null
                : [
                    'atual'   => $exercicio->qndt_erros - $resposta->tentativas_invalidas,
                    'inicial' => $exercicio->qndt_erros,
                ],
            'pontuacao'  => [
                'atual'   => $resposta->pontuacao,
                'inicial' => $recompensa->pontuacao,
            ],
        ];
    }

    public function isGameOver(Jogador $jogador, Exercicio $exercicio)
    {
        $saude = $this->saudeResposta($jogador, $exercicio);

        if (!is_null($saude['tempo']) && $saude['tempo']['atual'] == 0) {
            return true;
        }

        if (!is_null($saude['tentativas']) && $saude['tentativas']['atual'] == 0) {
            return true;
        }

        return false;
    }

    public function aplicarPenalidade(Jogador $jogador, Exercicio $exercicio): Resposta
    {
        $resposta = Resposta::where(['jogador_id' => $jogador->id, 'exercicio_id' => $exercicio->id])->firstOrFail();

        if (is_null($exercicio->qndt_erros)) {
            return $resposta;
        }

        if ($resposta->tentativas_invalidas >= $exercicio->qndt_erros) {
            return $resposta;
        }

        $resposta->tentativas_invalidas += 1;
        $resposta->save();

        return $resposta;
    }

    public function aplicarNovaTentativa(Jogador $jogador, Exercicio $exercicio)
    {
        $resposta = Resposta::where(['jogador_id' => $jogador->id, 'exercicio_id' => $exercicio->id])->firstOrFail();
        $resposta->repeticao += 1;
        $resposta->ultima_interacao = date('Y-m-d H:i:s');
        $resposta->tentativas_invalidas = 0;
        $resposta->pontuacao = $this->recalcularPontuacao($resposta, $exercicio);
        $resposta->save();
    }

    public function finalizarExercicio(DateTime $dataHoraFinalizacao, Jogador $jogador, Exercicio $exercicio)
    {
        $resposta = Resposta::where(['jogador_id' => $jogador->id, 'exercicio_id' => $exercicio->id])->firstOrFail();
        $resposta->concluida = true;
        $resposta->tempo = $this->calcularTempoResposta($dataHoraFinalizacao, $resposta, $exercicio);
        $resposta->save();
    }

    private function tempoRestante(Resposta $resposta, Exercicio $exercicio)
    {
        if ($exercicio->tempo == null) {
            return  null;
        }

        $inicio = strtotime($resposta->ultima_interacao);
        $atual = strtotime('now');

        $tempo = $atual - $inicio ;

        if ($tempo <= 0) {
            return 0;
        } else {
            $tempoRestante = $exercicio->tempo - $tempo;
            return  $tempoRestante < 0 ? 0 : $tempoRestante ;
        }
    }

    private function calcularTempoResposta(DateTime $dataHoraFinalizacao, Resposta $resposta, Exercicio $exercicio)
    {
        $inicio = strtotime($resposta->ultima_interacao);
        $finalizacao = strtotime($dataHoraFinalizacao->format('Y-m-d H:i:s'));
        return $finalizacao - $inicio;
    }

    private function recalcularPontuacao(Resposta $resposta, Exercicio $exercicio)
    {
        $recompensa = $exercicio->recompensa()->firstOrFail();

        $pontuacaoInicial = $recompensa->pontuacao;

        $porcentagem = ($resposta->repeticao / 5);

        $porcentagem = $porcentagem > 0.8 ? 0.8 : $porcentagem;

        return round($pontuacaoInicial - ($pontuacaoInicial * $porcentagem));
    }
}
