<script>
import AvisosService from '../../../service/AvisosService';

export default {
    data() {
        return {
            avisos: [],
            aviso: {},
            indicadores: {},
            categorias: [],
            avisoSelecionadoId: null,
            novaCategoria: null,
            botaoEditar: false,
            filters: {
                global: null
            },
            modalDeletarAviso: false,
            submitted: false,
            carregandoIndicadores: true,
            carregandoAvisos: true,
            modalCadastroAviso: false,
            modalCadastroCategoria: false,
            avisosService: new AvisosService()
        };
    },
    mounted() {
        // Buscando todos os avisos cadastrados no sistema
        this.avisosService.buscaAvisos().then((data) => {
            this.avisos = data.avisos;
            this.carregandoAvisos = false;
        });

        // Busca todos indicadores da página
        this.avisosService.buscaIndicadores().then((data) => {
            this.indicadores = data.indicadores;
            this.carregandoIndicadores = false;
        });

        // Busca todas categorais
        this.avisosService.buscaCategorias().then((data) => {
            this.categorias = data.categorias;
        });
    },
    methods: {
        abreModalCadastroAviso() {
            this.modalCadastroAviso = true;
            this.botaoEditar = false;
        },
        abrirModalCadastarAviso() {
            this.modalA;
        },
        buscaCategorias() {
            this.avisosService.buscaCategorias().then((data) => {
                this.categorias = data.categorias;
            });
        },
        buscaAvisos() {
            // Buscando todos os avisos cadastrados no sistema
            this.avisosService.buscaAvisos().then((data) => {
                this.avisos = data.avisos;
            });

            // Busca todos indicadores da página
            this.avisosService.buscaIndicadores().then((data) => {
                this.indicadores = data.indicadores;
            });
        },
        editaAviso(aviso) {
            this.aviso = { ...aviso };
            this.modalCadastroAviso = true;
            this.botaoEditar = true;
        },
        salvarEdicaoAviso() {
            this.avisosService.editaAviso(this.aviso, this.aviso.id).then((data) => {
                if (data.status == 'sucesso') {
                    this.mostraMensagemSucesso('Aviso editado com sucesso!');
                    this.aviso = {};
                    this.modalCadastroAviso = false;
                    this.avisos = data.avisos;
                } else {
                    this.mostraMensagemErro('Erro ao editar aviso!');
                }
                this.botaoEditar = false;
            });
        },
        cadastrarCategoria() {
            this.avisosService.cadastrarCategoria(this.novaCategoria).then((data) => {
                if (data.status == 'sucesso') {
                    this.mostraMensagemSucesso('Categoria cadastrada com sucesso!');
                    this.modalCadastroCategoria = false;
                    this.categorias = data.categorias;
                    this.novaCategoria = '';
                } else {
                    this.mostraMensagemErro('Erro ao cadastrar categoria!');
                }
            });
        },
        cadastrarAviso() {
            this.avisosService.cadastrarAviso(this.aviso).then((data) => {
                if (data.status == 'sucesso') {
                    this.mostraMensagemSucesso('Aviso cadastrado com sucesso!');
                    this.aviso = {};
                    this.avisos = data.avisos;
                    this.modalCadastroAviso = false;
                } else {
                    this.mostraMensagemErro('Erro ao cadastrar aviso!');
                }
            });
        },
        alterarStatus(aviso) {
            this.avisosService.alteraStatusAviso(aviso.id).then((data) => {
                this.mostraMensagemSucesso('Status alterado com sucesso!');
                this.buscaAvisos();
            });
        },
        confirmacaoExclusao(data) {
            this.avisoSelecionadoId = data.id;
            this.aviso = data;
            this.modalDeletarAviso = true;
        },
        deletarAviso(id) {
            this.avisosService.deletarAviso(id).then((data) => {
                if (data.status === 'sucesso') {
                    this.mostraMensagemSucesso('Aviso deletado com sucesso!');
                } else {
                    this.mostraMensagemErro('Erro ao deletar aviso!');
                }
                this.buscaAvisos();
                this.modalDeletarAviso = false;
            });
        },
        formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('pt-BR');
        },
        stripHtml(html) {
            if (!html) return '';
            return html.replace(/<[^>]*>/g, '');
        },
        mostraMensagemSucesso(mensagem) {
            this.$toast.add({ severity: 'Mensagem do Sistema', summary: 'Info', detail: mensagem, life: 3000 });
        },

        mostraMensagemErro(mensagem) {
            this.$toast.add({ severity: 'error', summary: 'Error', detail: mensagem, life: 3000 });
        }
    }
};
</script>

<template>
    <Toast style="z-index: 99999" />
    <div class="avisos-container">
        <!-- Barra de Título e Botão -->
        <div class="flex justify-content-between align-items-center mb-3 justify-center">
            <h1>Gestão de Avisos</h1>
        </div>

        <Divider />

        <div class="flex flex-wrap gap-6 mb-5 justify-center w-full">
            <template v-if="carregandoIndicadores">
                <Skeleton width="300px" height="100px" borderRadius="12px" />
                <Skeleton width="300px" height="100px" borderRadius="12px" />
                <Skeleton width="300px" height="100px" borderRadius="12px" />
            </template>
            <template v-else>
                <div class="flex justify-between items-center w-[300px] bg-white shadow rounded-xl p-4">
                    <div>
                        <div class="text-sm text-gray-600">Total de Avisos</div>
                        <div class="text-2xl font-bold text-gray-900">{{ indicadores.totalAvisos ?? 0 }}</div>
                    </div>
                    <Avatar icon="pi pi-exclamation-circle" class="bg-cyan-500 text-white" shape="circle" />
                </div>

                <div class="flex justify-between items-center w-[300px] bg-white shadow rounded-xl p-4">
                    <div>
                        <div class="text-sm text-gray-600">Avisos Ativos</div>
                        <div class="text-2xl font-bold text-gray-900">{{ indicadores.avisosAtivos ?? 0 }}</div>
                    </div>
                    <Avatar icon="pi pi-user-plus" class="bg-orange-500 text-white" shape="circle" />
                </div>

                <div class="flex justify-between items-center w-[300px] bg-white shadow rounded-xl p-4">
                    <div>
                        <div class="text-sm text-gray-600">Avisos Desativados</div>
                        <div class="text-2xl font-bold text-gray-900">{{ indicadores.avisosInativos ?? 0 }}</div>
                    </div>
                    <Avatar icon="pi pi-clipboard" class="bg-gray-600 text-white" shape="circle" />
                </div>
            </template>
        </div>

        <Divider />

        <!-- Tabela de Avisos -->
        <Card>
            <template #title>Lista de Avisos</template>
            <template #subtitle>Gerencie seus avisos publicados</template>
            <template #content>
                <DataTable :value="avisos" :paginator="true" :rows="10" :globalFilterFields="['titulo', 'mensagem']" responsiveLayout="scroll">
                    <template #header>
                        <div class="flex items-center gap-2">
                            <Button label="Adicionar Aviso" icon="pi pi-plus" class="p-button-success ml-auto" @click="abreModalCadastroAviso()" />
                        </div>
                    </template>

                    <template v-if="carregandoAvisos">
                        <Skeleton class="mb-1" width="100%" height="90px" v-for="i in 10" :key="i" />
                    </template>

                    <template>
                        <Column field="titulo" header="Título" :sortable="true"></Column>
                        <Column header="Texto" :sortable="true">
                            <template #body="{ data }">
                                <div class="truncate-text" style="max-width: 300px">
                                    {{ stripHtml(data.texto) }}
                                </div>
                            </template>
                        </Column>
                        <Column field="dataInicio" header="Dt. Criação" :sortable="true">
                            <template #body="{ data }">
                                {{ formatDate(data.dt_abertura) }}
                            </template>
                        </Column>
                        <Column header="Status">
                            <template #body="{ data }">
                                <Tag :value="data.ativo == 1 ? 'Ativo' : 'Desativado'" :severity="data.ativo == 1 ? 'success' : 'danger'" />
                            </template>
                        </Column>
                        <Column header="Ações" style="width: 150px">
                            <template #body="{ data }">
                                <Button icon="pi pi-pencil" class="p-button-rounded p-button-text p-button-success mr-2" @click="editaAviso(data)" v-tooltip.top="'Editar'" />
                                <Button :icon="data.ativo ? 'pi pi-eye-slash' : 'pi pi-eye'" class="p-button-rounded p-button-text p-button-warning mr-2" @click="alterarStatus(data)" v-tooltip.top="data.ativo ? 'Desativar' : 'Ativar'" />
                                <Button icon="pi pi-trash" class="p-button-rounded p-button-text p-button-danger" @click="confirmacaoExclusao(data)" v-tooltip.top="'Excluir'" />
                            </template>
                        </Column>
                    </template>
                </DataTable>
            </template>
        </Card>

        <!-- Dialog de Formulário -->
        <Dialog v-model:visible="modalCadastroAviso" :header="aviso.id ? 'Editar Aviso' : 'Novo Aviso'" :modal="true" :style="{ width: '700px', maxWidth: '90vw' }" :breakpoints="{ '960px': '75vw', '640px': '90vw' }" class="rounded-xl">
            <div class="p-fluid space-y-4">
                <!-- Título -->
                <div>
                    <label for="titulo" class="font-medium text-sm mb-1 block">Título <span class="text-red-500">*</span></label>
                    <InputText id="titulo" v-model.trim="aviso.titulo" autofocus :class="{ 'p-invalid': submitted && !aviso.titulo }" class="w-full" placeholder="Ex: Aviso de manutenção" />
                    <small class="p-error" v-if="submitted && !aviso.titulo">Título é obrigatório.</small>
                </div>

                <!-- Categoria -->
                <div>
                    <label for="titulo" class="font-medium text-sm mb-1 block">Categoria <span ckass="text-red-500">*</span></label>
                    <Dropdown v-model="aviso.categoria" :options="categorias" optionLabel="categoria" placeholder="Selecione uma categoria" class="w-full" />
                    <small class="p-error" v-if="submitted && !aviso.titulo">Título é obrigatório.</small>
                </div>

                <!-- Editor de Mensagem -->
                <div>
                    <label for="mensagem" class="font-medium text-sm mb-1 block">Texto <span class="text-red-500">*</span></label>
                    <Textarea class="w-full" v-model="aviso.texto" rows="5" cols="30" />
                    <small class="p-error" v-if="submitted && !aviso.texto">Mensagem é obrigatória.</small>
                </div>
            </div>

            <!-- Rodapé -->
            <template #footer>
                <div class="flex justify-end gap-2">
                    <Button label="Cancelar" icon="pi pi-times" class="p-button-text p-button-danger" @click="this.modalCadastroAviso = false" />
                    <Button label="Cadastrar Categoria" icon="pi pi-plus" class="p-button-info" @click="this.modalCadastroCategoria = true" />
                    <Button v-if="this.botaoEditar" label="Editar" icon="pi pi-check" class="p-button-success" @click="salvarEdicaoAviso()" />
                    <Button v-else label="Salvar" icon="pi pi-check" class="p-button-success" @click="cadastrarAviso()" />
                </div>
            </template>
        </Dialog>

        <!-- Dialog de Cadastro de Categorias -->
        <Dialog v-model:visible="modalCadastroCategoria" header="Cadastro de Categoria" :modal="true" :style="{ width: '300px' }">
            <div class="p-fluid space-y-4">
                <!-- Título -->
                <div>
                    <label for="titulo" class="font-medium text-sm mb-1 block mb-4">Categoria <span class="text-red-500">*</span></label>
                    <InputText id="titulo" v-model.trim="novaCategoria" autofocus :class="{ 'p-invalid': submitted && !aviso.titulo }" class="w-full" placeholder="Ex:  Aviso de manutenção" />
                    <small class="p-error" v-if="submitted && !aviso.titulo">Título é obrigatório.</small>
                </div>
            </div>

            <!-- Rodapé -->
            <template #footer>
                <div class="flex justify-end gap-2">
                    <Button label="Cancelar" icon="pi pi-times" class="p-button-text p-button-danger" @click="this.modalCadastroCategoria = false" />
                    <Button label="Salvar" icon="pi pi-check" class="p-button-success" @click="cadastrarCategoria()" />
                </div>
            </template>
        </Dialog>

        <!-- Confirmação de Exclusão -->
        <Dialog v-model:visible="modalDeletarAviso" header="Confirmar" :modal="true" :style="{ width: '300px' }">
            <div class="confirmation-content">
                <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
                <span v-if="aviso">
                    Tem certeza que deseja excluir o aviso? "<span class="font-medium ml-2">{{ aviso.titulo }}"</span>
                </span>
            </div>
            <template #footer>
                <Button label="Não" icon="pi pi-times" class="p-button-text p-button-danger" @click="this.modalDeletarAviso = false" />
                <Button label="Sim" icon="pi pi-check" class="p-button-success" @click="deletarAviso(aviso.id)" />
            </template>
        </Dialog>
    </div>
</template>

<style scoped>
.avisos-container {
    padding: 1rem;
}

.metric-card {
    height: 100%;
}

.truncate-text {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.confirmation-content {
    display: flex;
    align-items: center;
}

.metric-card {
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.metric-card:hover {
    transform: translateY(-2px);
    transition: all 0.3s ease;
}

.text-color-secondary {
    color: var(--text-color-secondary);
}
</style>
