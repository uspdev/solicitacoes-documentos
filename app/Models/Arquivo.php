<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Arquivo extends Model
{
    use HasFactory;

    /**
     * relacionamento com solicitação de documento
     */
    public function solicitacoesdocumentos()
    {
        return $this->belongsToMany('App\Models\SolicitacaoDocumento', 'arquivo_solicitacaodocumento', 'arquivo_id', 'solicitacaodocumento_id')->withTimestamps();
    }

    /**
     * Relacionamento: arquivo tem um tipo de arquivo
     */
    public function tipoarquivo()
    {
        return $this->belongsTo('App\Models\TipoArquivo', 'tipoarquivo_id');
    }
}
