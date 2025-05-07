$(document).ready(function() {

  $('[data-toggle="tooltip"]').tooltip();

  $('input[id="input_arquivo"]').change(function() {
    submete_form('arquivos', 'post');
  });
});

function toggle_modo_edicao(element, arquivo_id) {
  var li = $(element).closest('li');
  li.toggleClass('modo-edicao');
  li.toggleClass('modo-visualizacao');
  $('#nome_arquivo_' + arquivo_id).val($('#nome_arquivo_original_' + arquivo_id).val());
}

function excluir_arquivo(arquivo_id, arquivo_nome) {
  if (confirm('Tem certeza que deseja deletar o documento ' + arquivo_nome + '?'))
    submete_form('arquivos/' + arquivo_id, 'delete');
}

function alterar_arquivo(arquivo_id) {
  $('#nome_arquivo').val($('#nome_arquivo_' + arquivo_id).val());
  submete_form('arquivos/' + arquivo_id, 'patch');
}

function submete_form(acao, metodo) {
  $('#modal_processando').modal('show');
  $('#form_arquivos').attr({ action: acao });
  $('<input>').attr({ type: 'hidden', name: '_method', value: metodo }).appendTo('#form_arquivos');
  $('#form_arquivos').submit();
}
