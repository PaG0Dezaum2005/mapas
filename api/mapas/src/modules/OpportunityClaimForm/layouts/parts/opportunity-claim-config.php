<opportunity-enable-claim v-if="phase.__objectType == 'evaluationmethodconfiguration'" :entity="phase.opportunity"></opportunity-enable-claim>
<opportunity-enable-claim v-else-if="!phase.evaluationMethodConfiguration" :entity="phase"></opportunity-enable-claim>