<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSolicitacoesDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solicitacoesdocumentos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('estado', 90);
            $table->foreignId('setor_id')->constrained('setores');
            $table->foreignId('tipoarquivo_id')->constrained('tiposarquivo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('solicitacoesdocumentos');
    }
}
