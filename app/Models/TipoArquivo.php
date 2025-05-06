<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TipoArquivo extends Model
{
    use HasFactory;

    # tiposarquivo não segue convenção do laravel para nomes de tabela
    protected $table = 'tiposarquivo';

    protected $fillable = [
        'setor_id',
        'nome',
        'obrigatorio',
        'minimo',
        'aluno_especial',
    ];

    // uso no crud generico
    protected const fields = [
        [
            'name' => 'setor_id',
            'label' => 'Para',
            'type' => 'select',
            'model' => 'Setor',
            'data' => [],
        ],
        [
            'name' => 'nome',
            'label' => 'Nome',
        ],
    ];

    // uso no crud generico
    public static function getFields()
    {
        $fields = self::fields;
        foreach ($fields as &$field) {
            if (substr($field['name'], -3) == '_id') {
                $class = '\\App\\Models\\' . $field['model'];
                $field['data'] = $class::allToSelect();
            }
        }
        return $fields;
    }

    /**
     * retorna todos os tipos de arquivo
     * utilizado nas views common, para o select
     */
    public static function allToSelect()
    {
        $tiposarquivo = self::get();
        $ret = [];
        foreach ($tiposarquivo as $tipoarquivo)
            if (Gate::allows('tiposarquivo.view', $linhapesquisa))
                $ret[$tipoarquivo->id] = $tipoarquivo->nome;
        return $ret;
    }

    public static function obterTiposArquivoPossiveis(string $setor_nome)
    {
        // todos os tipos de arquivo possíveis para o dado setor
        return self::where('setor_id', $setor_nome)->get();
    }

    /**
     * Lista os tipos de arquivo autorizados para o usuário
     */
    public static function listarTiposArquivo()
    {
        return self::with('setor')->get()->sortBy('setor.sigla');
    }

    /**
     * Relacionamento: tipo de documento pertence a setor
     */
    public function setor()
    {
        return $this->belongsTo('App\Models\Setor');
    }
}
