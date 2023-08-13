<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\Conclusao;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\Formula;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\Predicado;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\PredicadoTipoEnum;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\Premissa;
use SimpleXMLElement;

class GeradorFormula
{
    /**
     * Função recebe o elemento XML e retorna uma Formula
     * @param SimpleXMLElement $xml
     */
    public function criarFormula(SimpleXMLElement $xml): Formula
    {
        $premissas = [];
        $conclusao = null;

        foreach ($xml as $filhos) {
            if ($filhos->getName() == 'PREMISSA') {
                array_push($premissas, $this->premissa($filhos));
            }

            if ($filhos->getName() == 'CONCLUSAO') {
                $conclusao = $this->conclusao($filhos);
            }
        }

        return new Formula(['premissas' => $premissas, 'conclusao' => $conclusao]);
    }

    /**
     * Função recebe um predicado e retorna a string de seu valor
     * @param Predicado $argumento
     * @param Predicado $arg
     */
    public function stringArg(Predicado $arg): string
    {
        $negacao = '';

        if ($arg->getNegadoPredicado() > 0) {
            for ($i = 0 ; $i < $arg->getNegadoPredicado(); ++$i) {
                $negacao = '~ ' . $negacao;
            }
        }

        if (in_array($arg->getTipoPredicado(), [PredicadoTipoEnum::CONJUNCAO, PredicadoTipoEnum::BICONDICIONAL, PredicadoTipoEnum::CONDICIONAL, PredicadoTipoEnum::DISJUNCAO])) {
            return $negacao . ' (' . $this->stringArg($arg->getEsquerdaPredicado()) . $arg->getTipoPredicado()->simbolo() . $this->stringArg($arg->getDireitaPredicado()) . ')';
        } else {
            return $negacao . ' ' . $arg->getValorPredicado();
        }
    }

    /**
     * Resolve o xml e retorna uma string da formula
     * @param  SimpleXMLElement $xml
     * @return string
     */
    public function stringFormula(SimpleXMLElement $xml): string
    {
        $str = '';
        $formula = $this->criarFormula($xml);

        foreach ($formula->getPremissas() as $premissa) {
            $str = $str . ' ' . $this->stringArg($premissa->getValorObjPremissa()) . ', ';
        }
        $str = $str . ' |- ' . $this->stringArg($formula->getConclusao()->getValorObjConclusao());
        return $str;
    }

    /**
     * Função recebe uma strig que representa a negação e retorna a quantidade de negações
     * @param string $atributo
     */
    private function qntdNegacao(string $atributo): int
    {
        return strlen($atributo);
    }

    /**
     * Função que verifica se o filho do elemento XML é igual à  Lpred
     * @param  SimpleXMLElement $pai
     * @return bool
     */
    private function childrenIsLpred(SimpleXMLElement $pai): bool
    {
        return $pai->children()->getName() == 'LPRED' ? true : false;
    }

    /**
     * Função que encontra o tipo do filho elemento XML,e o passa para as função de construção
     * do OBJECT corespondente ao tipo do filho (CONDICIONAL,BICONDICIONAL,DISJUNCAO,CONJUNCAO)
     * @param  SimpleXMLElement $pai
     * @return Predicado|null
     */
    private function encontraFilho(SimpleXMLElement $pai): ?Predicado
    {
        $nome = $pai->children()->getName();

        switch($nome) {
            case 'CONDICIONAL':
                return $this->condicional($pai->children());
                break;
            case 'BICONDICIONAL':
                return $this->bicondicional($pai->children());
                break;
            case 'DISJUNCAO':
                return $this->disjuncao($pai->children());
                break;
            case 'CONJUNCAO':
                return $this->conjuncao($pai->children());
                break;
            default:
                return null;
        }
    }

    /**
     * Função que extrai o valor do elemento Lpred do XML e o atributo de negação
     * @param  SimpleXMLElement    $lpred
     * @return array<string,mixed>
     */
    private function lpred(SimpleXMLElement $lpred): array
    {
        $negacao = $lpred->attributes()['NEG'] ?? '';
        return ['NEG' => $this->qntdNegacao($negacao), 'PREDICATIVO' => $lpred->children()->__toString() ?? ''];
    }

    /**
     * Função recebe o elemento CONDICIONAL XML e retorna o Objeto Predicado
     * @param  SimpleXMLElement $condicional
     * @return Predicado
     */
    private function condicional(SimpleXMLElement $condicional): Predicado
    {
        $antecendente_xml = $condicional->children()[0];
        $consequente_xml = $condicional->children()[1];

        if ($this->childrenIsLpred($antecendente_xml)) {
            $antecendente_array = $this->lpred($antecendente_xml->children());
            $antecendente_no = new Predicado($antecendente_array['PREDICATIVO'], $antecendente_array['NEG'], PredicadoTipoEnum::PREDICATIVO, null, null);
        } else {
            $antecendente_no = $this->encontraFilho($antecendente_xml);
        }

        if ($this->childrenIsLpred($consequente_xml)) {
            $consequente_array = $this->lpred($consequente_xml->children());
            $consequente_no = new Predicado($consequente_array['PREDICATIVO'], $consequente_array['NEG'], PredicadoTipoEnum::PREDICATIVO, null, null);
        } else {
            $consequente_no = $this->encontraFilho($consequente_xml);
        }
        $valor = $antecendente_no->getValorPredicado() . PredicadoTipoEnum::CONDICIONAL->simbolo() . $consequente_no->getValorPredicado();
        return new Predicado($valor, $this->qntdNegacao($condicional->attributes()['NEG'] ?? ''), PredicadoTipoEnum::CONDICIONAL, $antecendente_no, $consequente_no);
    }

    /**
     * Função recebe o elemento BICONDICIONAL XML e retorna o Objeto Predicado
     * @param  SimpleXMLElement $bicondicional
     * @return Predicado
     */
    private function bicondicional(SimpleXMLElement $bicondicional): Predicado
    {
        $primario_xml = $bicondicional->children()[0];
        $secundario_xml = $bicondicional->children()[1];

        if ($this->childrenIsLpred($primario_xml)) {
            $primario_array = $this->lpred($primario_xml->children());
            $primario_no = new Predicado($primario_array['PREDICATIVO'], $primario_array['NEG'], PredicadoTipoEnum::PREDICATIVO, null, null);
        } else {
            $primario_no = $this->encontraFilho($primario_xml);
        }

        if ($this->childrenIsLpred($secundario_xml)) {
            $secundario_array = $this->lpred($secundario_xml->children());
            $secundario_no = new Predicado($secundario_array['PREDICATIVO'], $secundario_array['NEG'], PredicadoTipoEnum::PREDICATIVO, null, null);
        } else {
            $secundario_no = $this->encontraFilho($secundario_xml);
        }
        $valor = $primario_no->getValorPredicado() . PredicadoTipoEnum::BICONDICIONAL->simbolo() . $secundario_no->getValorPredicado();
        return new Predicado($valor, $this->qntdNegacao($bicondicional->attributes()['NEG'] ?? ''), PredicadoTipoEnum::BICONDICIONAL, $primario_no, $secundario_no);
    }

    /**
     * Função recebe o elemento DISJUNÇÃO XML e retorna o Objeto Predicado
     * @param  SimpleXMLElement $disjuncao
     * @return Predicado
     */
    private function disjuncao(SimpleXMLElement $disjuncao): Predicado
    {
        $primario_xml = $disjuncao->children()[0];
        $secundario_xml = $disjuncao->children()[1];

        if ($this->childrenIsLpred($primario_xml)) {
            $primario_array = $this->lpred($primario_xml->children());
            $primario_no = new Predicado($primario_array['PREDICATIVO'], $primario_array['NEG'], PredicadoTipoEnum::PREDICATIVO, null, null);
        } else {
            $primario_no = $this->encontraFilho($primario_xml);
        }

        if ($this->childrenIsLpred($secundario_xml)) {
            $secundario_array = $this->lpred($secundario_xml->children());
            $secundario_no = new Predicado($secundario_array['PREDICATIVO'], $secundario_array['NEG'], PredicadoTipoEnum::PREDICATIVO, null, null);
        } else {
            $secundario_no = $this->encontraFilho($secundario_xml);
        }
        $valor = $primario_no->getValorPredicado() . PredicadoTipoEnum::DISJUNCAO->simbolo() . $secundario_no->getValorPredicado();
        return new Predicado($valor, $this->qntdNegacao($disjuncao->attributes()['NEG'] ?? ''), PredicadoTipoEnum::DISJUNCAO, $primario_no, $secundario_no);
    }

    /**
     * Função recebe o elemento CONJUNÇÃO XML e retorna o Objeto Predicado
     * @param  SimpleXMLElement $conjuncao
     * @return Predicado
     */
    private function conjuncao($conjuncao): Predicado
    {
        $primario_xml = $conjuncao->children()[0];
        $secundario_xml = $conjuncao->children()[1];

        if ($this->childrenIsLpred($primario_xml)) {
            $primario_array = $this->lpred($primario_xml->children());
            $primario_no = new Predicado($primario_array['PREDICATIVO'], $primario_array['NEG'], PredicadoTipoEnum::PREDICATIVO, null, null);
        } else {
            $primario_no = $this->encontraFilho($primario_xml);
        }

        if ($this->childrenIsLpred($secundario_xml)) {
            $secundario_array = $this->lpred($secundario_xml->children());
            $secundario_no = new Predicado($secundario_array['PREDICATIVO'], $secundario_array['NEG'], PredicadoTipoEnum::PREDICATIVO, null, null);
        } else {
            $secundario_no = $this->encontraFilho($secundario_xml);
        }
        $valor = $primario_no->getValorPredicado() . PredicadoTipoEnum::CONJUNCAO->simbolo() . $secundario_no->getValorPredicado();
        return new Predicado($valor, $this->qntdNegacao($conjuncao->attributes()['NEG'] ?? ''), PredicadoTipoEnum::CONJUNCAO, $primario_no, $secundario_no);
    }

    /**
     * Função recebe o elemento PREMISSA XML e retorna o Objeto Premissa
     * @param  SimpleXMLElement $premissa
     * @return Premissa|null
     */
    private function premissa(SimpleXMLElement $premissa): ?Premissa
    {
        if ($premissa->getName() == 'PREMISSA') {
            if ($this->childrenIsLpred($premissa)) {
                $premissa_array = $this->lpred($premissa->children());
                $predicado = new Predicado($premissa_array['PREDICATIVO'], $premissa_array['NEG'], PredicadoTipoEnum::PREDICATIVO, null, null);
                return new Premissa($predicado->getValorPredicado(), $predicado);
            } else {
                $nome = $premissa->children()->getName();

                if ($nome == 'CONDICIONAL') {
                    $valor = $this->condicional($premissa->children());
                } elseif ($nome == 'BICONDICIONAL') {
                    $valor = $this->bicondicional($premissa->children());
                } elseif ($nome == 'DISJUNCAO') {
                    $valor = $this->disjuncao($premissa->children());
                } elseif ($nome == 'CONJUNCAO') {
                    $valor = $this->conjuncao($premissa->children());
                }
            }
            return new Premissa($valor->getValorPredicado(), $valor);
        }
        return null;
    }

    /**
     * Função recebe o elemento CONCLUSÃO XML e retorna o Objeto Conclusao
     * @param  SimpleXMLElement $conclusao
     * @return Conclusao|null
     */
    private function conclusao(SimpleXMLElement $conclusao): Conclusao
    {
        if ($conclusao->getName() == 'CONCLUSAO') {
            if ($this->childrenIsLpred($conclusao)) {
                $conclusao_array = $this->lpred($conclusao->children());
                $valor_no = new Predicado($conclusao_array['PREDICATIVO'], $conclusao_array['NEG'], PredicadoTipoEnum::PREDICATIVO, null, null);
                return new Conclusao($valor_no->getValorPredicado(), $valor_no);
            } else {
                $nome = $conclusao->children()->getName();

                if ($nome == 'CONDICIONAL') {
                    $valor = $this->condicional($conclusao->children());
                } elseif ($nome == 'BICONDICIONAL') {
                    $valor = $this->bicondicional($conclusao->children());
                } elseif ($nome == 'DISJUNCAO') {
                    $valor = $this->disjuncao($conclusao->children());
                } elseif ($nome == 'CONJUNCAO') {
                    $valor = $this->conjuncao($conclusao->children());
                }
            }
            return new Conclusao($valor->getValorPredicado(), $valor);
        }
        return null;
    }
}
