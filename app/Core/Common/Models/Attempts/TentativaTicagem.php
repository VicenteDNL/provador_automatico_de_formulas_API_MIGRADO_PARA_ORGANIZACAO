<?php

namespace App\Core\Common\Models\Attempts;

use App\Core\Common\Models\Steps\PassoTicagem;
use App\Core\Common\Models\Tree\No;
use App\Core\Common\Serialization\Serializa;

/**
 * @template T
 */
class TentativaTicagem extends Serializa
{
    protected bool $sucesso;
    protected string $mensagem;
    protected ?No $arvore;

    /** @var ?PassoTicagem[] */
    protected array $passos;

    /**
     *@return bool
     */
    public function getSucesso(): bool
    {
        return $this->sucesso;
    }

    /**
     * @param  bool $sucesso
     * @return void
     */
    public function setSucesso(bool $sucesso): void
    {
        $this->sucesso = $sucesso;
    }

    /**
     *@return string
     */
    public function getMensagem(): string
    {
        return $this->mensagem;
    }

    /**
     * @param  string $mensagem
     * @return void
     */
    public function setMensagem(string $mensagem): void
    {
        $this->mensagem = $mensagem;
    }

    /**
     *@return No|null
     */
    public function getArvore(): ?No
    {
        return $this->arvore;
    }

    /**
     * @param  No   $arvore
     * @return void
     */
    public function setArvore(No $arvore): void
    {
        $this->arvore = $arvore;
    }

    /**
     *@return PassoTicagem[]|null
     */
    public function getPassos(): ?array
    {
        return $this->passos;
    }

    /**
     * @param  PassoTicagem[] $passos
     * @return void
     */
    public function setPassos(array $passos): void
    {
        $this->passos = $passos;
    }
}
