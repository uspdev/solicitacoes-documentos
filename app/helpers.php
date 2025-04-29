<?php

use Carbon\Carbon;
use Illuminate\Support\Str;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Node\Block\IndentedCode;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;
use Spatie\CommonMarkHighlighter\FencedCodeRenderer;
use Spatie\CommonMarkHighlighter\IndentedCodeRenderer;

if (!function_exists('md2html')) {
    /**
     * Converte markdown para html (github flavored)
     *
     * @param String $markdown
     * @param String $style Estido do CSS (default=default.css)
     * @return String
     * @author Masakik, em 16/11/2022
     */
    function md2html($markdown, $style = 'default.css')
    {
        $environment = new Environment();
        $environment->addExtension(new GithubFlavoredMarkdownExtension());
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addRenderer(FencedCode::class, new FencedCodeRenderer());
        $environment->addRenderer(IndentedCode::class, new IndentedCodeRenderer());

        $markdownConverter = new MarkdownConverter($environment);

        $html = '<style>' . file_get_contents(base_path('vendor/scrivo/highlight.php/styles/' . $style)) . '</style>';
        $html .= $markdownConverter->convertToHtml($markdown);
        return $html;
    }
}

if (!function_exists('formatarDecimal')) {
    function formatarDecimal($decimal)
    {
        if (floor($decimal) == $decimal)
            return number_format($decimal, 0);
        else
            return str_replace('.', ',', number_format($decimal, 2));
    }
}

if (!function_exists('formatarData')) {
    function formatarData($data)
    {
        // parece haver um bug nesta versão do Carbon que, se a data for nula, ele formata uma data anterior que ele formatou ao invés de retornar null
        return ((is_null($data)) ? '' : Carbon::parse($data)->format('d/m/Y'));
    }
}

if (!function_exists('formatarHora')) {
    function formatarHora($hora)
    {
        // parece haver um bug nesta versão do Carbon que, se a hora for nula, ele formata uma hora anterior que ele formatou ao invés de retornar null
        return ((is_null($hora)) ? '' : Carbon::parse($hora)->format('H:i'));
    }
}

if (!function_exists('formatarDataHora')) {
    function formatarDataHora($data_hora)
    {
        // parece haver um bug nesta versão do Carbon que, se a data for nula, ele formata uma data anterior que ele formatou ao invés de retornar null
        return ((is_null($data_hora)) ? '' : Carbon::parse($data_hora)->format('d/m/Y H:i'));
    }
}
