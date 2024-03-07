<?php

use MapasCulturais\i;

/**
 * @var MapasCulturais\App $app
 * @var MapasCulturais\Themes\BaseV2\Theme $this
 */

$this->import('
    entity-field
    mc-confirm-button
    mc-icon
');
?>

<div class="affirmative-policies--quota-configuration">
    <div v-if="!isActive" class="affirmative-policies--quota-configuration__activate">
        <button @click="addConfig();" class="button button--primary button--icon">
            <mc-icon name="add"></mc-icon>
            <?php i::_e("Configurar cotas por categoria") ?>
        </button>
    </div>

    <div v-if="isActive" v-for="(quota, index) in phase.quotaConfiguration.rules" :key="index" class="affirmative-policies--quota-configuration__card">
        <div class="affirmative-policies--quota-configuration__card-header">

            <div>
                <h4 class="bold"><?= i::__('Configuração das cotas por categoria') ?></h4>
    
                <div class="fields">
                    <label v-if="totalVacancies > 0" class="field__title">
                        <?= i::__('Percentual total das Cotas:') ?>
                        {{totalPercentage}} %
                    </label>
    
                    <label class="field__title">
                        <?= i::__('Número total das Cotas:') ?>
                        {{totalQuota}}
                    </label>
                </div>
            </div>

            <mc-confirm-button @confirm="removeConfig(index)">
                <template #button="{open}">
                    <button class="button button--sm button--text-danger button--icon affirmative-policies--quota-configuration__quota-delete" @click="open()">
                        <mc-icon class="danger__color" name="trash"></mc-icon>
                        <?= i::__('Excluir') ?>
                    </button>
                </template>
                <template #message="message">
                    <?= i::__('Deseja deletar a cota?') ?>
                </template>
            </mc-confirm-button>

        </div>

        <div class="affirmative-policies--quota-configuration__card-content">
            <div class="affirmative-policies--quota-configuration__quota-header">
                <div class="field">
                    <label><?= i::__('Descrição:') ?></label>
                    <input type="text" v-model="quota.title" @blur="autoSave()">
                </div>

                <div v-if="totalVacancies > 0" class="field">
                    <label><?= i::__('Porcentagem') ?></label>
                    <span class="affirmative-policies--quota-configuration__quota-field-sufix">
                        <input type="number" v-model="quota.percentage" @change="updateRuleQuotas(quota)" min="0" max="100"> %
                    </span>
                </div>

                <div class="field">
                    <label><?= i::__('Número de Vagas') ?></label>
                    <input type="number" v-model="quota.vacancies" @change="updateRuleQuotaPercentage(quota)" min="0">
                </div>
            </div>

            <div v-for="(field, indexF) in quota.fields" :key="indexF" class="affirmative-policies--quota-configuration__quota-field">
                <div class="field">
                    <mc-select placeholder="Selecione uma cota" :default-value="quota.fieldName" @change-option="setFieldName($event, field)">
                        <option v-for="(item, index) in phase.opportunity.affirmativePoliciesEligibleFields" :value="item.fieldName">{{ '#' + item.id + ' ' + item.title }}</option>
                    </mc-select>
                </div>

                <mc-confirm-button @confirm="removeField(index, indexF)">
                    <template #button="{open}">
                        <button class="button button--sm button--text-danger button--icon" @click="open()">
                            <mc-icon class="danger__color" name="trash"></mc-icon>
                        </button>
                    </template>

                    <template #message="message">
                        <?= i::__('Deseja deletar o campo?') ?>
                    </template>
                </mc-confirm-button>

                <div v-if="getFieldType(field) === 'select' || getFieldType(field) === 'multiselect' || getFieldType(field) === 'checkboxes'" class="field">
                    <label><?= i::__('Dar preferência a') ?>:</label>

                    <div class="field field--horizontal">
                        <label v-for="option in getFieldOptions(field)">
                            <input type="checkbox" :value="option" :true-value="[]" v-model="field.eligibleValues" @change="autoSave()" />
                            <span>{{option}}</span>
                        </label>
                    </div>
                </div>

                <div v-if="getFieldType(field) === 'checkbox' || getFieldType(field) === 'boolean'" class="field">
                    <label><?= i::__('Dar preferência a') ?>:</label>

                    <div class="field field--horizontal">
                        <label>
                            <input type="radio" :name="quota.fieldName + ':' + index" :value="true" v-model="field.eligibleValues" @change="autoSave()">
                            <span><?= i::__('Sim / Marcado') ?></span>
                        </label>

                        <label>
                            <input type="radio" :name="quota.fieldName + ':' + index" :value="false" v-model="field.eligibleValues" @change="autoSave()">
                            <span><?= i::__('Não / Desmarcado') ?></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="affirmative-policies--quota-configuration__card-footer">
            <button class="button button--primary button--icon" @click="addField(index)">
                <mc-icon name="add"></mc-icon>
                <?php i::_e("Adicionar campo") ?>
            </button>
        </div>
    </div>

    <div v-if="isActive" class="affirmative-policies--quota-configuration__add-category">
        <button @click="addConfig();" class="button button--primary button--icon">
            <mc-icon name="add"></mc-icon>
            <?php i::_e("Adicionar nova categoria") ?>
        </button>
    </div>

    <!-- <div v-if="isActive" class="affirmative-policies--quota-configuration__card">
        <div class="affirmative-policies--quota-configuration__header">
            <h4 class="bold"><?= i::__('Configuração das cotas por categoria') ?></h4>

            <div class="fields">
                <label class="field__title"> <!--  v-if="totalVacancies > 0" ->
                <?= i::__('Percentual total das Cotas:') ?>
                    {{totalPercentage}} %
                </label>

                <label class="field__title">
                    <?= i::__('Número total das Cotas:') ?>
                    {{totalQuota}}
                </label>
            </div>

            <entity-field :entity="firstPhase" prop="enableQuotasQuestion"></entity-field>
        </div>

        <div class="affirmative-policies--quota-configuration__content" v-if="entity.quotaConfiguration && entity?.quotaConfiguration?.rules.length > 0">
            <div class="affirmative-policies--quota-configuration__quota" v-for="(quota, index) in entity.quotaConfiguration.rules" :key="index">
                <div class="affirmative-policies--quota-configuration__column">
                    <div>
                        <div class="field col-2">
                            <label><?= i::__('Descrição:') ?></label>

                            <input type="text" v-model="quota.title" @blur="autoSave()">
                        </div>

                        <div class="field"> <!-- v-if="totalVacancies > 0" ->
                            <label><?= i::__('Porcentagem') ?></label>

                            <span class="affirmative-policies--quota-configuration__field-sufix">
                                <input type="number" v-model="quota.percentage" @change="updateRuleQuotas(quota)" min="0" max="100"> %
                            </span>
                        </div>

                        <div class="field">
                            <label><?= i::__('Número de Vagas') ?></label>

                            <input type="number" v-model="quota.vacancies" @change="updateRuleQuotaPercentage(quota)" min="0">
                        </div>
                    </div>

                    <div class="affirmative-policies--quota-configuration__quota-trash">
                        <mc-confirm-button @confirm="removeConfig(index)">
                            <template #button="{open}">
                                <button class="field__trash button button--md button--text-danger button-icon" @click="open()">
                                    <mc-icon class="danger__color" name="trash"></mc-icon>
                                    <?= i::__('Excluir') ?>
                                </button>
                            </template>
                            <template #message="message">
                                <?= i::__('Deseja deletar a cota?') ?>
                            </template>
                        </mc-confirm-button>
                    </div>

                    <div v-for="(field, indexF) in quota.fields" :key="indexF" class="affirmative-policies--quota-configuration__quota-fields">
                        <div class="field">
                            <label></label>
                            <mc-select placeholder="Selecione uma cota" :default-value="quota.fieldName" @change-option="setFieldName($event, field)">
                                <option v-for="(item, index) in entity.opportunity.affirmativePoliciesEligibleFields" :value="item.fieldName">{{ '#' + item.id + ' ' + item.title }}</option>
                            </mc-select>
                        </div>


                        <div class="affirmative-policies--quota-configuration__field-trash">
                            <mc-confirm-button @confirm="removeField(index, indexF)">
                                <template #button="{open}">
                                    <button class="button button--md button--text-danger button-icon" @click="open()">
                                        <mc-icon class="danger__color" name="trash"></mc-icon>
                                    </button>
                                </template>

                                <template #message="message">
                                    <?= i::__('Deseja deletar o campo?') ?>
                                </template>
                            </mc-confirm-button>
                        </div>

                        <div v-if="getFieldType(field) === 'select' || getFieldType(field) === 'multiselect' || getFieldType(field) === 'checkboxes'" class="field col-4">
                            <label><?= i::__('Dar preferência a') ?>:</label>

                            <div class="field field--horizontal">
                                <label v-for="option in getFieldOptions(field)">
                                    <input type="checkbox" :value="option" :true-value="[]" v-model="quota.eligibleValues" @change="autoSave()" />
                                    <span>{{option}}</span>
                                </label>
                            </div>
                        </div>

                        <div v-if="getFieldType(field) === 'checkbox' || getFieldType(field) === 'boolean'" class="field col-4">
                            <label><?= i::__('Dar preferência a') ?>:</label>

                            <div class="field field--horizontal">
                                <label>
                                    <input type="radio" :name="quota.fieldName + ':' + index" :value="true" v-model="quota.eligibleValues" @change="autoSave()">
                                    <span><?= i::__('Sim / Marcado') ?></span>
                                </label>

                                <label>
                                    <input type="radio" :name="quota.fieldName + ':' + index" :value="false" v-model="quota.eligibleValues" @change="autoSave()">
                                    <span><?= i::__('Não / Desmarcado') ?></span>
                                </label>
                            </div>
                        </div>
                    </div>                    
                </div>

                <button class="button button--primary button--icon" @click="addField(index)"><?php i::_e("Adicionar campo") ?></button>
            </div>
        </div>
        
        <div class="affirmative-policies--quota-configuration__footer">
            <button @click="addConfig();" class="button button--primary button--icon">
                <mc-icon name="add"></mc-icon>
                <?php i::_e("Adicionar categoria") ?>
            </button>
        </div>
    </div> -->
</div>