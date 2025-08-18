<script>
import { ref } from 'vue';

export default {
    setup() {
        const globalFilter = ref('');
        const colaboradores = ref([
            { nome: 'João Silva', cargo: 'Analista de TI', mes: 'Agosto/2025', status: 'Processado' },
            { nome: 'Maria Souza', cargo: 'Engenheira', mes: 'Agosto/2025', status: 'Processado' },
            { nome: 'Carlos Lima', cargo: 'Financeiro', mes: 'Agosto/2025', status: 'Pendente' },
            { nome: 'Ana Paula', cargo: 'RH', mes: 'Agosto/2025', status: 'Processado' },
            { nome: 'Pedro Santos', cargo: 'Logística', mes: 'Agosto/2025', status: 'Pendente' }
        ]);

        const verFolha = (colaborador) => {
            alert(`Abrindo folha de ${colaborador.nome} (${colaborador.mes})`);
        };

        return { colaboradores, globalFilter, verFolha };
    }
};
</script>

<template>
    <div class="p-6 space-y-6 bg-gray-50 min-h-screen">
        <!-- Título -->
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-700">Folhas de Pagamento</h2>
            <p class="text-sm text-gray-500">Visualize e acompanhe as folhas dos colaboradores</p>
        </div>

        <!-- Indicadores gerais -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <Card class="shadow-md rounded-2xl border-l-4 border-blue-500">
                <template #title>Colaboradores</template>
                <template #content>
                    <p class="text-3xl font-bold text-blue-600">42</p>
                    <p class="text-gray-500">Ativos este mês</p>
                </template>
            </Card>

            <Card class="shadow-md rounded-2xl border-l-4 border-green-500">
                <template #title>Folhas Processadas</template>
                <template #content>
                    <p class="text-3xl font-bold text-green-600">38</p>
                    <p class="text-gray-500">Último fechamento</p>
                </template>
            </Card>

            <Card class="shadow-md rounded-2xl border-l-4 border-yellow-500">
                <template #title>Pendentes</template>
                <template #content>
                    <p class="text-3xl font-bold text-yellow-600">4</p>
                    <p class="text-gray-500">Aguardando conferência</p>
                </template>
            </Card>
        </div>

        <!-- Tabela de colaboradores -->
        <Card class="shadow-lg rounded-2xl">
            <template #title>
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-semibold text-gray-700">Lista de Colaboradores</h3>
                    <InputText v-model="globalFilter" placeholder="🔍 Buscar..." class="p-inputtext-sm" />
                </div>
            </template>

            <template #content>
                <DataTable :value="colaboradores" paginator rows="8" :globalFilter="globalFilter" responsiveLayout="scroll" class="text-sm">
                    <Column field="nome" header="Colaborador" sortable>
                        <template #body="slotProps">
                            <div class="flex items-center gap-2">
                                <span class="font-medium">{{ slotProps.data.nome }}</span>
                            </div>
                        </template>
                    </Column>

                    <Column field="cargo" header="Cargo" sortable></Column>

                    <Column field="mes" header="Mês Ref.">
                        <template #body="slotProps">
                            <Tag :value="slotProps.data.mes" severity="info" />
                        </template>
                    </Column>

                    <Column field="status" header="Status">
                        <template #body="slotProps">
                            <Tag :value="slotProps.data.status" :severity="slotProps.data.status === 'Processado' ? 'success' : 'warning'" />
                        </template>
                    </Column>

                    <Column header="Ações">
                        <template #body="slotProps">
                            <Button icon="pi pi-eye" class="p-button-sm p-button-rounded p-button-outlined" label="Ver Folha" @click="verFolha(slotProps.data)" />
                        </template>
                    </Column>
                </DataTable>
            </template>
        </Card>
    </div>
</template>

<style scoped></style>
