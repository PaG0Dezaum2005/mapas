<?php
use MapasCulturais\i;
$this->layout = 'entity';

$this->import('
    entity-actions
    entity-header
    entity-links
    mapas-breadcrumb
    opportunity-basic-info
    opportunity-phases-config
    tabs
');

$this->breadcrumb = [
  ['label'=> i::__('Painel'), 'url' => $app->createUrl('panel', 'index')],
  ['label'=> i::__('Minhas oportunidades'), 'url' => $app->createUrl('panel', 'opportunity')],
  ['label'=> $entity->name, 'url' => $app->createUrl('opportunity', 'edit', [$entity->id])],
];
?>

<div class="main-app">
    <mapas-breadcrumb></mapas-breadcrumb>
    <entity-header :entity="entity" editable></entity-header>
    <tabs class="tabs">
        <tab label="<?= i::__('Informações') ?>" slug="info">
            <opportunity-basic-info :entity="entity"></opportunity-basic-info>
        </tab>
        <tab label="<?= i::__('Configuração de fases') ?>" slug="config">
            <opportunity-phases-config :entity="entity"></opportunity-phases-config>
        </tab>
        <tab label="<?= i::__('Inscrições e Resultados') ?>" slug="subs_result">
        </tab>
        <tab label="<?= i::__('Relatórios') ?>" slug="report">
        </tab>
    </tabs>
    <entity-actions :entity="entity" editable></entity-actions>
</div>