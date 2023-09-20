<?php

namespace App\LogicLive\Common\Models;

use App\LogicLive\Common\Serialization\Serializa;

class RespostaModel extends Serializa
{
    protected string $exe_hash;
    protected bool $usx_completado;
    protected string $uer_log;
    protected ?int $tempo_exercicio;

    /**
     * @return string
     */
    public function getExeHash(): string
    {
        return $this->exe_hash;
    }

    /**
     * @param  string $exe_hash
     * @return void
     */
    public function setExeHash(string $exe_hash): void
    {
        $this->exe_hash = $exe_hash;
    }

    /**
     * @return bool
     */
    public function getUsxCompletado(): bool
    {
        return $this->usx_completado;
    }

    /**
     * @param  bool $usx_completado
     * @return void
     */
    public function setUsxCompletado(bool $usx_completado): void
    {
        $this->usx_completado = $usx_completado;
    }

    /**
     * @return string
     */
    public function getUerLog(): string
    {
        return $this->uer_log;
    }

    /**
     * @param  string $uer_log
     * @return void
     */
    public function setUerLog(string $uer_log): void
    {
        $this->uer_log = $uer_log;
    }

    /**
     * @return ?int
     */
    public function getTempoExercicio(): ?int
    {
        return $this->tempo_exercicio;
    }

    /**
     * @param  int  $tempo_exercicio
     * @return void
     */
    public function setTempoExercicio(?int $tempo_exercicio): void
    {
        $this->tempo_exercicio = $tempo_exercicio;
    }
}
