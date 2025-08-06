<template>
    <div class="avisos-container">
        <Toast />

        <!-- Barra de Título e Botão -->
        <div class="flex justify-content-between align-items-center mb-3">
            <h1>Gestão de Avisos</h1>
        </div>

        <Divider />

        <div class="flex flex-wrap gap-6 mb-5 align-items-center justify-content-center w-full sm:w-[100%] lg:w-[100%] xl:w-[100%] 2xl:w-[100%] 3xl:w-[100%]">
            <!-- Emergency Alerts -->
            <div class="flex justify-between items-center w-full sm:w-[33%] bg-white shadow rounded-xl p-4">
                <div>
                    <div class="text-sm text-gray-600">Total de Avisos</div>
                    <div class="text-2xl font-bold text-gray-900">{{ this.indicadores.totalAvisos ?? 0 }}</div>
                </div>
                <Avatar icon="pi pi-exclamation-circle" class="bg-cyan-500 text-white" shape="circle" />
            </div>

            <!-- Patient Admissions -->
            <div class="flex justify-between items-center w-full sm:w-[30%] bg-white shadow rounded-xl p-4">
                <div>
                    <div class="text-sm text-gray-600">Avisos Ativos</div>
                    <div class="text-2xl font-bold text-gray-900">{{ this.indicadores.avisosAtivos ?? 0 }}</div>
                </div>
                <Avatar icon="pi pi-user-plus" class="bg-orange-500 text-white" shape="circle" />
            </div>

            <!-- Medical Records -->
            <div class="flex justify-between items-center w-full sm:w-[30%] bg-white shadow rounded-xl p-4">
                <div>
                    <div class="text-sm text-gray-600">Avisos Desativados</div>
                    <div class="text-2xl font-bold text-gray-900">{{ this.indicadores.avisosInativos ?? 0 }}</div>
                </div>
                <Avatar icon="pi pi-clipboard" class="bg-gray-600 text-white" shape="circle" />
            </div>
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
                            <span class="p-input-icon-left">
                                <InputText v-model="filters.global" placeholder="Buscar avisos..." />
                            </span>

                            <!-- Alinha à direita -->
                            <Button label="Adicionar Aviso" icon="pi pi-plus" class="p-button-success ml-auto" @click="this.modalCadastroAviso = true" />
                        </div>
                    </template>

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
                            <Button icon="pi pi-pencil" class="p-button-rounded p-button-text p-button-success mr-2" @click="editAviso(data)" v-tooltip.top="'Editar'" />
                            <Button :icon="data.ativo ? 'pi pi-eye-slash' : 'pi pi-eye'" class="p-button-rounded p-button-text p-button-warning mr-2" @click="alterarStatus(data)" v-tooltip.top="data.ativo ? 'Desativar' : 'Ativar'" />
                            <Button icon="pi pi-trash" class="p-button-rounded p-button-text p-button-danger" @click="confirmacaoExclusao(data)" v-tooltip.top="'Excluir'" />
                        </template>
                    </Column>
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

                <!-- Categorai -->
                <div>
                    <label for="titulo" class="font-medium text-sm mb-1 block">Categoria <span ckass="text-red-500">*</span></label>
                    <Dropdown v-model="aviso.categoria" :options="categorias" optionLabel="categoria" placeholder="Selecione uma categoria" class="w-full" />
                    <small class="p-error" v-if="submitted && !aviso.titulo">Título é obrigatório.</small>
                </div>

                <!-- Editor de Mensagem -->
                <div>
                    <label for="mensagem" class="font-medium text-sm mb-1 block">Mensagem <span class="text-red-500">*</span></label>
                    <Textarea class="w-full" v-model="value" rows="5" cols="30" />
                    <small class="p-error" v-if="submitted && !aviso.mensagem">Mensagem é obrigatória.</small>
                </div>

                <!-- Checkbox urgente -->
                <div class="flex items-center gap-2">
                    <Checkbox id="urgente" v-model="aviso.urgente" :binary="true" />
                    <label for="urgente" class="text-sm font-medium">Aviso Urgente (destaque especial)</label>
                </div>
            </div>

            <!-- Rodapé -->
            <template #footer>
                <div class="flex justify-end gap-2">
                    <Button label="Cancelar" icon="pi pi-times" class="p-button-text p-button-danger" @click="this.modalCadastroAviso = false" />
                    <Button label="Cadastrar Categoria" icon="pi pi-plus" class="p-button-info" @click="this.modalCadastroCategoria = true" />
                    <Button label="Salvar" icon="pi pi-check" class="p-button-success" @click="saveAviso" />
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
            selectedAviso: null,
            novaCategoria: null,
            filters: {
                global: null
            },
            avisoDialog: false,
            modalDeletarAviso: false,
            submitted: false,
            modalCadastroAviso: false,
            modalCadastroCategoria: false,
            avisosService: new AvisosService()
        };
    },
    mounted() {
        // Buscando todos os avisos cadastrados no sistema
        this.avisosService.buscaAvisos().then((data) => {
            this.avisos = data.avisos;
        });

        // Busca todos indicadores da página
        this.avisosService.buscaIndicadores().then((data) => {
            this.indicadores = data.indicadores;
        });

        this.avisosService.buscaCategorias().then((data) => {
            this.categorias = data.categorias;
        });
    },
    onMounted() {},
    methods: {
        openNew() {
            this.aviso = {
                titulo: '',
                mensagem: '',
                dataInicio: null,
                dataFim: null,
                ativo: true,
                urgente: false
            };
            this.submitted = false;
            this.avisoDialog = true;
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
        editAviso(aviso) {
            this.aviso = { ...aviso };
            this.avisoDialog = true;
        },
        hideDialog() {
            this.avisoDialog = false;
            this.submitted = false;
        },
        saveAviso() {
            this.submitted = true;

            if (!this.aviso.titulo || !this.aviso.mensagem || !this.aviso.dataInicio) {
                return;
            }

            if (this.aviso.id) {
                // Atualização
                const index = this.avisos.findIndex((a) => a.id === this.aviso.id);
                this.avisos[index] = { ...this.aviso };
                this.$toast.add({
                    severity: 'success',
                    summary: 'Sucesso',
                    detail: 'Aviso atualizado',
                    life: 3000
                });
            } else {
                // Criação
                const newId = Math.max(...this.avisos.map((a) => a.id), 0) + 1;
                this.avisos.push({
                    ...this.aviso,
                    id: newId,
                    clicks: 0,
                    ultimoClick: null
                });
                this.$toast.add({
                    severity: 'success',
                    summary: 'Sucesso',
                    detail: 'Aviso criado',
                    life: 3000
                });
            }

            this.avisoDialog = false;
        },
        cadastrarCategoria() {
            this.avisosService.cadastrarCategoria(this.novaCategoria).then((data) => {
                if (data.status === 'success') {
                    this.buscaCategorias();
                }
            });
        },
        alterarStatus(aviso) {
            this.avisosService.alteraStatusAviso(aviso.id).then((data) => {
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
                if (data.status === 'success') {
                    this.mostrarMensagemSucesso(data);
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
        formatDateTime(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleString('pt-BR');
        },
        stripHtml(html) {
            if (!html) return '';
            return html.replace(/<[^>]*>/g, '');
        },
        mostrarMensagemSucesso(mensagem) {
            this.$toast.add({
                severity: 'success',
                summary: 'Sucesso',
                detail: `${mensagem}`,
                life: 3000
            });
        }
    }
};
</script>

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
