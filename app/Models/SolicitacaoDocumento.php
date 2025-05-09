<?php

namespace App\Models;

use App\Observers\SolicitacaoDocumentoObserver;
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
        'tipoarquivo_id',
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
        [
            'name' => 'tipoarquivo_id',
            'label' => 'Tipo do Documento',
            'type' => 'select',
            'model' => 'TipoArquivo',
            'data' => [],
        ],
    ];

    // uso no crud generico
    public static function getFields(?int $setor_id = null)
    {
        $fields = self::fields;
        foreach ($fields as &$field)
            if (substr($field['name'], -3) == '_id') {
                $class = '\\App\\Models\\' . $field['model'];
                $field['data'] = $class::allToSelect($setor_id);
            }
        return $fields;
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        SolicitacaoDocumento::observe(SolicitacaoDocumentoObserver::class);
    }

    /**
     * lista de estados padrão
     */
    public static function estados()
    {
        return [
            'Pendente',    // decorre de ações do usuário
            'Atendida'     // decorre de ações do gerente
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
                return self::where('setor_id', \Auth::user()->obterSetorMaisRecente()->id)->get();

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
     * relacionamento com arquivos
     */
    public function arquivos()
    {
        return $this->belongsToMany('App\Models\Arquivo', 'arquivo_solicitacaodocumento', 'solicitacaodocumento_id', 'arquivo_id')->withPivot('tipo')->withTimestamps();
    }

    /**
     * relacionamento com users
     */
    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'user_solicitacaodocumento', 'solicitacaodocumento_id', 'user_id')->withTimestamps();
    }

    /**
     * relacionamento com setor
     */
    public function setor()
    {
        return $this->belongsTo(Setor::class);
    }

    /**
     * relacionamento com tipo de arquivo
     */
    public function tipoarquivo()
    {
        return $this->belongsTo(TipoArquivo::class);
    }
}
