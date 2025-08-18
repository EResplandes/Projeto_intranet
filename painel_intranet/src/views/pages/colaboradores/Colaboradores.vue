<script>
import AvisosService from '../../../service/AvisosService';
import ColaboradoresService from '../../../service/ColaboradoresService';
import FolhaService from '../../../service/FolhaService';

export default {
    data() {
        return {
            colaboradores: [],
            colaborador: {},
            aviso: {},
            indicadores: {},
            folhas: {},
            departamentos: [],
            expandedRows: [],
            colaboradorSelecionadoId: null,
            novoDepartamento: null,
            botaoEditar: false,
            modalDeletarColaborador: false,
            submitted: false,
            carregandoIndicadores: true,
            modalFolhasColaborador: false,
            carregandoColaboradores: true,
            modalCadastroColaborador: false,
            modalCadastroDepartamento: false,
            avisosService: new AvisosService(),
            colaboradoresService: new ColaboradoresService(),
            folhaService: new FolhaService()
        };
    },
    mounted() {
        const admin = localStorage.getItem('admin');
        if (admin === '0') {
            this.$router.push({ name: 'inicio' });
        }

        // Busca todos colaboradores
        this.colaboradoresService.buscaTodosColaboradores().then((data) => {
            if (data.status) {
                this.colaboradores = data.colaboradores;
                this.carregandoColaboradores = false;
            }
        });

        // Busca indicadores da página
        this.colaboradoresService.buscaIndicadores().then((data) => {
            if (data.status) {
                this.indicadores = data.indicadores;
                this.carregandoIndicadores = false;
            }
        });

        // Busca departamentos
        this.colaboradoresService.buscaDepartamentos().then((data) => {
            this.departamentos = data.departamentos;
        });
    },
    methods: {
        abreModalCadastroColaborador() {
            this.modalCadastroColaborador = true;
            this.botaoEditar = false;
        },
        buscadepartamentos() {
            this.avisosService.buscadepartamentos().then((data) => {
                this.departamentos = data.departamentos;
            });
        },
        editaColaborador(colaborador) {
            this.colaborador = { ...colaborador };
            this.modalCadastroColaborador = true;
            this.botaoEditar = true;
        },
        salvaColaborador() {
            this.colaboradoresService.editaColaborador(this.colaborador).then((data) => {
                if (data.status) {
                    this.mostraMensagemSucesso('Colaborador editado com sucesso!');
                    this.colaborador = {};
                    this.modalCadastroColaborador = false;
                    this.colaboradores = data.colaboradores;
                } else {
                    this.mostraMensagemErro('Erro ao editar colaborador!');
                }
                this.botaoEditar = false;
            });
        },
        cadastrarDepartamento() {
            this.colaboradoresService.cadastrarDepartamento(this.novoDepartamento).then((data) => {
                if (data.status) {
                    this.mostraMensagemSucesso('Departamento cadastrada com sucesso!');
                    this.modalCadastroDepartamento = false;
                    this.departamentos = data.departamentos;
                    this.novoDepartamento = '';
                } else {
                    this.mostraMensagemErro('Erro ao cadastrar departamento!');
                }
            });
        },
        cadastraColaborador() {
            this.colaboradoresService.cadastraColaborador(this.colaborador).then((data) => {
                if (data.status) {
                    this.mostraMensagemSucesso('Colaborador cadastrado com sucesso!');
                    this.colaborador = {};
                    this.colaboradores = data.colaboradores;
                    this.modalCadastroColaborador = false;
                } else {
                    this.mostraMensagemErro('Erro ao cadastrar colaborador!');
                }
            });
        },

        alterarStatus(colaborador) {
            this.colaboradoresService.alterarStatusColaborador(colaborador.id).then((data) => {
                this.mostraMensagemSucesso('Status alterado com sucesso!');
                this.colaboradores = data.colaboradores;
            });
        },
        confirmacaoExclusao(data) {
            this.colaboradorSelecionadoId = data.id;
            this.colaborador = data;
            this.modalDeletarColaborador = true;
        },
        deletarColaborador() {
            this.colaboradoresService.deletarColaborador(this.colaboradorSelecionadoId).then((data) => {
                if (data.status) {
                    this.mostraMensagemSucesso('Colaborador deletado com sucesso!');
                    this.colaboradores = data.colaboradores;
                } else {
                    this.mostraMensagemErro('Erro ao deletar colaborador!');
                }
                this.modalDeletarColaborador = false;
            });
        },
        formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('pt-BR');
        },

        modalFolhas(data) {
            this.colaborador = data;

            this.buscaFolhaColaborador(data.id);

            this.modalFolhasColaborador = true;
        },

        buscaFolhaColaborador(id) {
            this.folhaService.buscaFolhasColaborador(id).then((data) => {
                this.folhas = data.folhas;
            });
        },

        mostraMensagemSucesso(mensagem) {
            this.$toast.add({ severity: 'Mensagem do Sistema', summary: 'Info', detail: mensagem, life: 3000 });
        },

        mostraMensagemErro(mensagem) {
            this.$toast.add({ severity: 'error', summary: 'Error', detail: mensagem, life: 3000 });
        },

        carregarImagem() {
            this.colaborador.imagem = this.$refs.img.files[0];
        },

        fecharModalFolhas() {
            this.modalFolhasColaborador = false;
            this.colaborador = {};
            this.folhas = {};
        },

        validarFolha(data) {
            this.folhaService.validacaoFolha(data.id).then((data) => {
                if (data.status) {
                    this.mostraMensagemSucesso('Folha validada com sucesso!');
                    this.buscaFolhaColaborador(this.colaborador.id);
                } else {
                    this.mostraMensagemErro('Erro ao validar folha!');
                }
            });
        }
    }
};
</script>

<template>
    <div class="avisos-container">
        <Toast style="z-index: 99999" />

        <!-- Barra de Título e Botão -->
        <div class="flex justify-content-between align-items-center mb-3 justify-center">
            <h1>Gestão de Colaboradores</h1>
        </div>

        <Dialog @hide="fecharModalFolhas" v-model:visible="modalFolhasColaborador" maximizable modal header="📑 Folhas do Colaborador" :style="{ width: '75rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
            <div class="flex flex-col gap-4">
                <!-- Informações do colaborador -->
                <div class="p-4 bg-gray-50 rounded-lg shadow-sm">
                    <h3 class="text-xl font-semibold text-gray-800">{{ colaborador.nome }}</h3>
                    <p class="text-sm text-gray-500">Matrícula: {{ colaborador.matricula }} | Admissão: {{ colaborador.dt_admissao }}</p>
                </div>

                <DataTable v-model:expandedRows="expandedRows" :value="folhas" dataKey="id" class="p-datatable-striped p-datatable-sm">
                    <!-- Coluna expansível -->
                    <Column expander style="width: 3rem" />

                    <!-- Colunas principais -->
                    <Column header="Mês/Ano">
                        <template #body="slotProps"> {{ slotProps.data.folha.mes }}/{{ slotProps.data.folha.ano }} </template>
                    </Column>

                    <Column header="Salário Base">
                        <template #body="slotProps"> R$ {{ slotProps.data.salario_base.toLocaleString('pt-BR', { minimumFractionDigits: 2 }) }} </template>
                    </Column>

                    <Column header="Total Proventos">
                        <template #body="slotProps"> R$ {{ slotProps.data.total_proventos.toLocaleString('pt-BR', { minimumFractionDigits: 2 }) }} </template>
                    </Column>

                    <Column header="Total Descontos">
                        <template #body="slotProps"> R$ {{ slotProps.data.total_descontos.toLocaleString('pt-BR', { minimumFractionDigits: 2 }) }} </template>
                    </Column>

                    <Column header="Salário Líquido">
                        <template #body="slotProps"> R$ {{ slotProps.data.total_liquido.toLocaleString('pt-BR', { minimumFractionDigits: 2 }) }} </template>
                    </Column>

                    <Column header="Status">
                        <template #body="slotProps">
                            <Tag :value="slotProps.data.liberado === 1 ? 'Validado' : 'Pendente de Validação'" :severity="slotProps.data.liberado === 1 ? 'success' : 'warn'" />
                        </template>
                    </Column>

                    <Column header="Ações" style="width: 8rem">
                        <template #body="slotProps">
                            <Button v-if="slotProps.data.liberado === 0" icon="pi pi-check" class="p-button-success mr-2" tooltipOptions="{ position: 'right' }" @click="validarFolha(slotProps.data)" />
                        </template>
                    </Column>

                    <!-- Linha expansível -->
                    <template #expansion="slotProps">
                        <div class="p-4 bg-gray-50 rounded-lg shadow-sm grid grid-cols-2 gap-6">
                            <!-- Coluna de Proventos -->
                            <div>
                                <h3 class="font-semibold mb-2">Proventos</h3>
                                <ul class="space-y-1">
                                    <li v-for="item in slotProps.data.itens.filter((i) => i.tipo === 'Provento')" :key="item.id" class="flex justify-between">
                                        <span>{{ item.descricao }}</span>
                                        <span>R$ {{ item.valor.toFixed(2) }}</span>
                                    </li>
                                </ul>
                            </div>

                            <!-- Coluna de Descontos -->
                            <div>
                                <h3 class="font-semibold mb-2">Descontos</h3>
                                <ul class="space-y-1">
                                    <li v-for="item in slotProps.data.itens.filter((i) => i.tipo === 'Desconto')" :key="item.id" class="flex justify-between">
                                        <span>{{ item.descricao }}</span>
                                        <span>R$ {{ item.valor.toFixed(2) }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </template>
                </DataTable>
            </div>
        </Dialog>

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
                        <div class="text-sm text-gray-600">Total de Colaboradores</div>
                        <div class="text-2xl font-bold text-gray-900">{{ indicadores.totalColaboradores ?? 0 }}</div>
                    </div>
                    <Avatar icon="pi pi-exclamation-circle" class="bg-cyan-500 text-white" shape="circle" />
                </div>

                <div class="flex justify-between items-center w-[300px] bg-white shadow rounded-xl p-4">
                    <div>
                        <div class="text-sm text-gray-600">Colaboradores Ativos</div>
                        <div class="text-2xl font-bold text-gray-900">{{ indicadores.colaboradoresAtivos ?? 0 }}</div>
                    </div>
                    <Avatar icon="pi pi-user-plus" class="bg-orange-500 text-white" shape="circle" />
                </div>

                <div class="flex justify-between items-center w-[300px] bg-white shadow rounded-xl p-4">
                    <div>
                        <div class="text-sm text-gray-600">Colaboradores Desativados</div>
                        <div class="text-2xl font-bold text-gray-900">{{ indicadores.colaboradoresInativos ?? 0 }}</div>
                    </div>
                    <Avatar icon="pi pi-clipboard" class="bg-gray-600 text-white" shape="circle" />
                </div>
            </template>
        </div>

        <Divider />

        <!-- Tabela de Colaboradores -->
        <Card>
            <template #title>Lista de Colaboradores</template>
            <template #subtitle>Gerencie seus colaboradores</template>
            <template #content>
                <DataTable :value="colaboradores" :paginator="true" :rows="10" :globalFilterFields="['titulo', 'mensagem']" responsiveLayout="scroll">
                    <template #header>
                        <div class="flex items-center gap-2">
                            <Button label="Adicionar Colaborador" icon="pi pi-plus" class="p-button-success ml-auto" @click="abreModalCadastroColaborador()" />
                        </div>
                    </template>

                    <template v-if="carregandoColaboradores">
                        <Skeleton class="mb-1" width="100%" height="90px" v-for="i in 10" :key="i" />
                    </template>

                    <template>
                        <Column field="matricula" header="Matricula" :sortable="true"></Column>
                        <Column field="nome" header="Nome Completo" :sortable="true"></Column>
                        <Column field="cargo" header="Cargo" :sortable="true"></Column>
                        <Column field="email" header="Email" :sortable="true"></Column>
                        <!-- <Column field="cpf" header="CPF" :sortable="true"></Column> -->
                        <Column header="Departamento" :sortable="true">
                            <template #body="{ data }">
                                <div class="truncate-text">
                                    {{ data.departamento?.departamento }}
                                </div>
                            </template>
                        </Column>
                        <Column field="dt_nascimento" header="Dt. Nascimento" :sortable="true">
                            <template #body="{ data }">
                                {{ data.dt_nascimento }}
                            </template>
                        </Column>
                        <Column header="Status">
                            <template #body="{ data }">
                                <Tag :value="data.ativo == 1 ? 'Ativo' : 'Desativado'" :severity="data.ativo == 1 ? 'success' : 'danger'" />
                            </template>
                        </Column>
                        <Column header="Ações" :style="{ minWidth: '240px' }">
                            <template #body="{ data }">
                                <div class="flex flex-wrap gap-2 justify-start">
                                    <Button icon="pi pi-pencil" class="p-button-success" @click="editaColaborador(data)" v-tooltip.top="'Editar'" />
                                    <Button :icon="data.ativo ? 'pi pi-eye-slash' : 'pi pi-eye'" class="p-button-info" @click="alterarStatus(data)" v-tooltip.top="data.ativo ? 'Desativar' : 'Ativar'" />
                                    <Button icon="pi pi-trash" class="p-button-danger" @click="confirmacaoExclusao(data)" v-tooltip.top="'Excluir'" />
                                    <Button icon="pi pi-wallet" class="p-button-warning" @click="modalFolhas(data)" v-tooltip.top="'Visualizar Folhas'" />
                                </div>
                            </template>
                        </Column>
                    </template>
                </DataTable>
            </template>
        </Card>

        <Dialog
            v-model:visible="modalCadastroColaborador"
            :header="colaborador.id ? 'Editar Colaborador' : 'Novo Colaborador'"
            :modal="true"
            :style="{ width: '800px', maxWidth: '90vw' }"
            :breakpoints="{ '960px': '75vw', '640px': '90vw' }"
            class="rounded-xl"
        >
            <form @submit.prevent="salvarColaborador" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div v-if="botaoEditar" class="flex items-center justify-center">
                    <div class="w-28 h-28 rounded-full border-4 border-blue-500 shadow-lg overflow-hidden">
                        <img :src="'http://localhost:8000/storage/' + colaborador.imagem" alt="Imagem" class="w-full h-full object-cover" />
                    </div>
                </div>

                <!-- Nome completo -->
                <div class="flex flex-col">
                    <label class="text-sm text-gray-600 mb-1">Nome completo</label>
                    <InputText v-model="colaborador.nome" placeholder="Digite o nome" class="w-full" />
                </div>

                <!-- Departamento -->
                <div class="flex flex-col">
                    <label class="text-sm text-gray-600 mb-1">Departamento</label>
                    <Dropdown v-model="colaborador.departamento" :options="departamentos" optionLabel="departamento" placeholder="Selecione" class="w-full" />
                </div>

                <!-- MATRICULA -->
                <div class="flex flex-col">
                    <label class="text-sm text-gray-600 mb-1">Matrícula</label>
                    <InputText v-model="colaborador.matricula" class="w-full" />
                </div>

                <!-- CPF -->
                <div class="flex flex-col">
                    <label class="text-sm text-gray-600 mb-1">CPF</label>
                    <InputMask mask="999.999.999-99" v-model="colaborador.cpf" class="w-full" />
                </div>

                <!-- E-mail -->
                <div class="flex flex-col">
                    <label class="text-sm text-gray-600 mb-1">E-mail</label>
                    <InputText v-model="colaborador.email" type="email" placeholder="email@gruporialma.com.br" class="w-full" />
                </div>

                <!-- Data de Admissão -->
                <div class="flex flex-col">
                    <label class="text-sm text-gray-600 mb-1">Data de Admissão</label>
                    <Calendar v-model="colaborador.dt_admissao" dateFormat="dd/mm/yy" showIcon class="w-full" />
                </div>

                <!-- Data de Nascimento -->
                <div class="flex flex-col">
                    <label class="text-sm text-gray-600 mb-1">Data de Nascimento</label>
                    <Calendar v-model="colaborador.dt_nascimento" dateFormat="dd/mm/yy" showIcon class="w-full" />
                </div>

                <!-- Cargo -->
                <div class="flex flex-col md:col-span-1">
                    <label class="text-sm text-gray-600 mb-1">Cargo</label>
                    <InputText v-model="colaborador.cargo" placeholder="Ex: Desenvolvedor Backend" class="w-full" />
                </div>

                <!-- Imagem -->
                <div class="md:col-span-2 flex flex-col gap-2 mt-4">
                    <FileUpload
                        class="w-full"
                        style="background-color: #004285"
                        :key="fileKey"
                        chooseLabel="Escolher Imagem"
                        @change="carregarImagem"
                        mode="basic"
                        type="file"
                        ref="img"
                        name="demo[]"
                        accept="image/*"
                        :maxFileSize="999999999"
                    ></FileUpload>
                </div>

                <!-- Botões -->
                <div class="col-span-1 md:col-span-2 flex justify-end gap-2 mt-4">
                    <Button label="Cancelar" severity="secondary" @click="modalCadastroColaborador = false" />
                    <Button label="Cadastrar Departamento" icon="pi pi-plus" class="p-button-info" @click="this.modalCadastroDepartamento = true" />
                    <Button v-if="this.botaoEditar" label="Editar" icon="pi pi-check" class="p-button-success" type="submit" @click="salvaColaborador()" />
                    <Button v-else label="Salvar" icon="pi pi-check" class="p-button-success" type="submit" @click="cadastraColaborador()" />
                </div>
            </form>
        </Dialog>

        <!-- Dialog de Cadastro de Departamentos -->
        <Dialog v-model:visible="modalCadastroDepartamento" header="Cadastro de Departameto" :modal="true" :style="{ width: '300px' }">
            <div class="p-fluid space-y-4">
                <!-- Título -->
                <div>
                    <label for="titulo" class="font-medium text-sm mb-1 block mb-4">Departamento <span class="text-red-500">*</span></label>
                    <InputText id="titulo" v-model.trim="novoDepartamento" autofocus :class="{ 'p-invalid': submitted && !aviso.titulo }" class="w-full" placeholder="Ex:  Departamento Financeiro" />
                    <small class="p-error" v-if="submitted && !aviso.titulo">Título é obrigatório.</small>
                </div>
            </div>

            <!-- Rodapé -->
            <template #footer>
                <div class="flex justify-end gap-2">
                    <Button label="Cancelar" icon="pi pi-times" class="p-button-text p-button-danger" @click="this.modalCadastroDepartamento = false" />
                    <Button label="Salvar" icon="pi pi-check" class="p-button-success" @click="cadastrarDepartamento()" />
                </div>
            </template>
        </Dialog>

        <!-- Confirmação de Exclusão -->
        <Dialog v-model:visible="modalDeletarColaborador" header="Confirmar" :modal="true" :style="{ width: '300px' }">
            <div class="confirmation-content">
                <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
                <span v-if="aviso">
                    Tem certeza que deseja excluir o colaborador? "<span class="font-medium ml-2">{{ colaborador.nome }}"</span>
                </span>
            </div>
            <template #footer>
                <Button label="Não" icon="pi pi-times" class="p-button-text p-button-danger" @click="this.modalDeletarColaborador = false" />
                <Button label="Sim" icon="pi pi-check" class="p-button-success" @click="deletarColaborador(colaborador.id)" />
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
