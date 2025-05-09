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
    public static function getFields(?int $setor_id = null)
    {
        $fields = self::fields;
        foreach ($fields as &$field) {
            if (substr($field['name'], -3) == '_id') {
                $class = '\\App\\Models\\' . $field['model'];
                $field['data'] = $class::allToSelect($setor_id);
            }
        }
        return $fields;
    }

    /**
     * retorna todos os tipos de arquivo
     * utilizado nas views common, para o select
     */
    public static function allToSelect(?int $setor_id = null)
    {
        $tiposarquivo = $setor_id ? self::where('setor_id', $setor_id)->get() : self::get();
        $ret = [];
        foreach ($tiposarquivo as $tipoarquivo)
            $ret[$tipoarquivo->id] = $tipoarquivo->nome;
        return $ret;
    }

    /**
     * Lista os tipos de arquivo autorizados para o usuário
     */
    public static function listarTiposArquivo()
    {
        switch (session('perfil')) {
            case 'admin':
                return self::with('setor')->get()->sortBy('setor.sigla');

            case 'gerente':
                return self::with('setor')->where('setor_id', \Auth::user()->obterSetorMaisRecente()->id)->get()->sortBy('setor.sigla');
        }
    }

    /**
     * Relacionamento: tipo de arquivo pertence a setor
     */
    public function setor()
    {
        return $this->belongsTo('App\Models\Setor');
    }

    /**
     * relacionamento com arquivos
     */
    public function arquivos()
    {
        return $this->hasMany('App\Models\Arquivo', 'tipoarquivo_id');
    }
}
