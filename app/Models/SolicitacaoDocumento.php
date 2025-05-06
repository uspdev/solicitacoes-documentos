<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class SolicitacaoDocumento extends Model
{
    use HasFactory;

    # solicitações de documentos não segue convenção do laravel para nomes de tabela
    protected $table = 'solicitacoesdocumentos';

    protected $fillable = [
        'setor_id',
    ];

    // uso no crud generico
    protected const fields = [
        [
            'name' => 'setor_id',
            'label' => 'Setor',
            'type' => 'hidden',
            'model' => 'Setor',
            'data' => [],
        ],
    ];

    // uso no crud generico
    public static function getFields()
    {
        $fields = self::fields;
        foreach ($fields as &$field)
            if (substr($field['name'], -3) == '_id') {
                $class = '\\App\\Models\\' . $field['model'];
                $field['data'] = $class::allToSelect();
            }
        return $fields;
    }

    /**
     * lista de estados padrão
     */
    public static function estados()
    {
        return [
            'Aguardando Envio', 'Isenção de Taxa Solicitada',                                          // decorrem de ações do candidato
            'Isenção de Taxa em Avaliação', 'Isenção de Taxa Aprovada', 'Isenção de Taxa Rejeitada'    // decorrem de ações do serviço de pós-graduação
        ];
    }

    /**
     * Valores possiveis para pivot do relacionamento com users
     */
    #
    public static function pessoaPapeis($formSelect = false)
    {
        if ($formSelect)
            return ['Autor' => 'Autor'];
        else
            return ['Autor'];
    }

    /**
     * Lista as solicitações de documentos autorizadas para o usuário
     *
     * Se perfiladmin mostra todas as solicitações de documentos
     * Se perfilusuario mostra as solicitações de documentos que ele está cadastrado como criador
     *
     * @return Collection
     */
    public static function listarSolicitacoesDocumentos()
    {
        switch (session('perfil')) {
            case 'admin':
                return self::all();

            case 'gerente':
                return ;

            default:
                return Auth::user()->solicitacoesdocumentos()->wherePivotIn('papel', ['Autor'])->get();
        }
    }

    public static function listarSolicitacoesDocumentosPorSetor(Setor $setor, int $ano)
    {
        return self::where('setor_id', $setor->id)->whereYear('created_at', $ano)->get();
    }

    /**
     * Mostra as pessoas que têm vínculo com a solicitação de documento
     *
     * Se informado $pivot, retorna somente o primeiro usuário, senão retorna a lista completa
     *
     * @param  $pivot Papel da pessoa na solicitação de documento (autor, null = todos)
     * @return App\Models\User|Collection
     */
    public function pessoas($pivot = null)
    {
        if ($pivot)
            return $this->users()->wherePivot('papel', $pivot)->first();
        else
            return $this->users()->withPivot('papel');
    }

    /**
     * relacionamento com users
     */
    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'user_solicitacaodocumento')->withTimestamps();
    }

    /**
     * relacionamento com setor
     */
    public function setor()
    {
        return $this->belongsTo(Setor::class);
    }
}
