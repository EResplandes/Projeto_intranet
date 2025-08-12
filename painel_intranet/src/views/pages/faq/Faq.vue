<script>
import FaqService from '../../../service/FaqService';

export default {
    data() {
        return {
            faqs: [],
            faq: {},
            indicadores: {},
            categorias: [],
            faqSelecionadoId: null,
            novaCategoria: null,
            botaoEditar: false,
            filters: {
                global: null
            },
            modalDeletarFaq: false,
            submitted: false,
            carregandoIndicadores: true,
            carregandoFaqs: true,
            modalCadastroFaq: false,
            modalCadastroCategoria: false,
            faqService: new FaqService()
        };
    },
    mounted() {
        // Metodo para carregar as perguntas
        this.faqService.buscaFaqs().then((data) => {
            if (data.status) this.carregandoFaqs = false;
            this.faqs = data.faqs;
        });

        this.buscaIndicadores();
    },
    methods: {
        buscaFaqs() {
            this.faqService.buscaFaqs().then((data) => {
                if (data.status) {
                    this.faqs = data.faqs;
                    this.indicadores = data.indicadores;
                    this.carregandoFaqs = false;
                    this.carregandoIndicadores = false;
                }
            });
        },
        buscaIndicadores() {
            this.faqService.buscaIndicadores().then((data) => {
                if (data.status) {
                    this.indicadores = data.indicadores;
                    this.carregandoIndicadores = false;
                }
            });
        },
        abreModalCadastroFaq() {
            this.modalCadastroFaq = true;
            this.botaoEditar = false;
            this.faq = {};
        },
        editaFaq(faq) {
            this.faq = { ...faq };
            this.modalCadastroFaq = true;
            this.botaoEditar = true;
        },
        salvarEdicaoFaq() {
            this.faqService.editaFaq(this.faq, this.faq.id).then((data) => {
                if (data.status) {
                    this.mostraMensagemSucesso('Pergunta editada com sucesso!');
                    this.modalCadastroFaq = false;
                    this.faqs = data.faqs;
                } else {
                    this.mostraMensagemErro('Erro ao editar pergunta!');
                }
                this.botaoEditar = false;
            });
        },
        cadastrarFaq() {
            this.faqService.cadastrarFaq(this.faq).then((data) => {
                if (data.status) {
                    this.mostraMensagemSucesso('Pergunta cadastrada com sucesso!');
                    this.faq = {};
                    this.faqs = data.faqs;
                    this.modalCadastroFaq = false;
                    this.buscaIndicadores();
                } else {
                    this.mostraMensagemErro('Erro ao cadastrar pergunta!');
                }
            });
        },
        alterarStatus(faq) {
            this.faqService.alteraStatusFaq(faq.id).then((data) => {
                if (data.status) {
                    this.mostraMensagemSucesso('Status alterado com sucesso!');
                    this.faqs = data.faqs;
                    this.buscaIndicadores();
                } else {
                    this.mostraMensagemErro('Erro ao alterar status!');
                }
            });
        },
        confirmacaoExclusao(data) {
            this.faqSelecionadoId = data.id;
            this.faq = data;
            this.modalDeletarFaq = true;
        },
        deletarFaq(id) {
            this.faqService.deletaFaq(id).then((data) => {
                if (data.status) {
                    this.faqs = data.faqs;
                    this.buscaIndicadores();
                    this.mostraMensagemSucesso('Pergunta excluída com sucesso!');
                } else {
                    this.mostraMensagemErro('Erro ao excluir pergunta!');
                }
                this.buscaFaqs();
                this.modalDeletarFaq = false;
            });
        },
        mostraMensagemSucesso(mensagem) {
            this.$toast.add({ severity: 'success', summary: 'Sucesso', detail: mensagem, life: 3000 });
        },
        mostraMensagemErro(mensagem) {
            this.$toast.add({ severity: 'error', summary: 'Erro', detail: mensagem, life: 3000 });
        }
    }
};
</script>

<template>
    <Toast style="z-index: 99999" />
    <div class="faq-container">
        <div class="flex justify-content-between align-items-center mb-3 justify-center">
            <h1>Gestão de FAQ</h1>
        </div>

        <Divider />

        <!-- Indicadores -->
        <div class="flex flex-wrap gap-6 mb-5 justify-center w-full">
            <template v-if="carregandoIndicadores">
                <Skeleton width="300px" height="100px" borderRadius="12px" v-for="i in 3" :key="i" />
            </template>
            <template v-else>
                <div class="metric-card w-[300px]">
                    <div class="text-sm text-gray-600">Total de Perguntas</div>
                    <div class="text-2xl font-bold">{{ indicadores.total ?? 0 }}</div>
                </div>
                <div class="metric-card w-[300px]">
                    <div class="text-sm text-gray-600">Ativas</div>
                    <div class="text-2xl font-bold">{{ indicadores.ativas ?? 0 }}</div>
                </div>
                <div class="metric-card w-[300px]">
                    <div class="text-sm text-gray-600">Desativadas</div>
                    <div class="text-2xl font-bold">{{ indicadores.inativas ?? 0 }}</div>
                </div>
            </template>
        </div>

        <Divider />

        <!-- Tabela -->
        <Card>
            <template #title>Lista de Perguntas</template>
            <template #subtitle>Gerencie as perguntas e respostas</template>
            <template #content>
                <DataTable :value="faqs" :paginator="true" :rows="10" :globalFilterFields="['pergunta', 'resposta']" responsiveLayout="scroll">
                    <template v-if="carregandoColaboradores">
                        <Skeleton class="mb-1" width="100%" height="90px" v-for="i in 10" :key="i" />
                    </template>

                    <template #header>
                        <div class="flex items-center gap-2">
                            <Button label="Adicionar Pergunta" icon="pi pi-plus" class="p-button-success ml-auto" @click="abreModalCadastroFaq()" />
                        </div>
                    </template>

                    <Column field="dt_criacao" header="Dt. Criação" :sortable="true"></Column>

                    <Column field="categoria" header="Categoria">
                        <template #body="{ data }">
                            <div class="truncate-text" style="max-width: 300px">
                                {{ data.categoria }}
                            </div>
                        </template>
                    </Column>
                    <Column field="pergunta" header="Pergunta" :sortable="true"></Column>
                    <Column field="resposta" header="Resposta">
                        <template #body="{ data }">
                            <div class="truncate-text" style="max-width: 300px">
                                {{ data.resposta }}
                            </div>
                        </template>
                    </Column>
                    <Column field="status" header="Status">
                        <template #body="{ data }">
                            <div class="truncate-text" style="max-width: 300px">
                                {{ data.ativo ? 'Ativo' : 'Inativo' }}
                            </div>
                        </template>
                    </Column>
                    <Column field="acoes" header="Ações">
                        <template #body="{ data }">
                            <div class="flex gap-2">
                                <Button icon="pi pi-pencil" class="p-button-success" @click="editaFaq(data)" />
                                <Button :tooltip="data.ativo ? 'Desativar' : 'Ativar'" :icon="data.ativo ? 'pi pi-eye-slash' : 'pi pi-eye'" class="p-button-info" @click="alterarStatus(data)" />
                                <Button icon="pi pi-trash" class="p-button-danger" @click="confirmacaoExclusao(data)" />
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </template>
        </Card>

        <!-- Modal Cadastro -->
        <Dialog v-model:visible="modalCadastroFaq" :header="faq.id ? 'Editar Pergunta' : 'Nova Pergunta'" :modal="true" :style="{ width: '700px' }">
            <div class="p-fluid space-y-4">
                <div>
                    <label>Pergunta <span class="text-red-500">*</span></label>
                    <InputText v-model.trim="faq.pergunta" class="w-full" />
                </div>
                <div>
                    <label>Categoria <span class="text-red-500">*</span></label>
                    <InputText v-model.trim="faq.categoria" class="w-full" />
                </div>
                <div>
                    <label>Resposta <span class="text-red-500">*</span></label>
                    <Textarea v-model="faq.resposta" rows="5" class="w-full" />
                </div>
            </div>
            <template #footer>
                <Button label="Cancelar" class="p-button-text p-button-danger" @click="modalCadastroFaq = false" />
                <Button v-if="botaoEditar" label="Editar" class="p-button-success" @click="salvarEdicaoFaq()" />
                <Button v-else label="Salvar" class="p-button-success" @click="cadastrarFaq()" />
            </template>
        </Dialog>

        <!-- Modal Categoria -->
        <Dialog v-model:visible="modalCadastroCategoria" header="Cadastro de Categoria" :modal="true" :style="{ width: '300px' }">
            <div class="p-fluid">
                <label>Categoria</label>
                <InputText v-model.trim="novaCategoria" class="w-full" />
            </div>
            <template #footer>
                <Button label="Cancelar" class="p-button-text p-button-danger" @click="modalCadastroCategoria = false" />
                <Button label="Salvar" class="p-button-success" @click="cadastrarCategoria()" />
            </template>
        </Dialog>

        <!-- Confirmação Exclusão -->
        <Dialog v-model:visible="modalDeletarFaq" header="Confirmar" :modal="true" :style="{ width: '300px' }">
            <div class="confirmation-content">
                Tem certeza que deseja excluir "<b>{{ faq.pergunta }}</b
                >"?
            </div>
            <template #footer>
                <Button label="Não" class="p-button-text p-button-danger" @click="modalDeletarFaq = false" />
                <Button label="Sim" class="p-button-success" @click="deletarFaq(faq.id)" />
            </template>
        </Dialog>
    </div>
</template>

<style scoped>
.truncate-text {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.metric-card {
    background: white;
    padding: 1rem;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
</style>
