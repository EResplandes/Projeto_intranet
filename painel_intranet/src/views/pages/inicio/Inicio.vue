<script>
import jsPDF from 'jspdf';
import html2canvas from 'html2canvas';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import FolhaService from '../../../service/FolhaService';

export default {
    name: 'DashboardColaborador',
    components: {
        DataTable,
        Column
    },
    data() {
        return {
            // Dados de Contratcheque
            contracheques: [],
            salarioBaseAtual: 0,
            salarioLiquidoAtual: 0,
            horasExtrasTotaisPonto: '00h 00m',
            pontoData: [],
            isLoadingPonto: true,
            expandedRows: [],
            expandedSection: null,
            isGeneratingPDF: false,
            folhaService: new FolhaService(),
            colaborador: {
                nome: localStorage.getItem('usuario'),
                cargo: localStorage.getItem('cargo'),
                cpf: localStorage.getItem('cpf')
            },
            // Mapeamento dos índices da matriz do JSON para nomes de campos
            fieldMap: {
                data: 0,
                entrada1: 1,
                saida1: 2,
                entrada2: 3,
                saida2: 4,
                entrada3: 5,
                saida3: 6,
                normais: 7,
                faltas: 8,
                extras: 9,
                saldoBanco: 25,
                totalBanco: 26,
                justificativa: 44
            }
        };
    },
    mounted() {
        this.fetchContracheques();
        this.fetchPontoData();
    },
    methods: {
        async fetchContracheques() {
            const usuario_id = localStorage.getItem('usuario_id');
            try {
                const data = await this.folhaService.painelColaborador(usuario_id);
                if (data.status) {
                    this.contracheques = data.folhas;
                    this.salarioBaseAtual = data.folhas[0]?.salario_base || 0;
                    this.salarioLiquidoAtual = data.folhas[0]?.total_liquido || 0;
                }
            } catch (error) {
                console.error('Erro ao buscar contracheques:', error);
            }
        },
        async fetchPontoData() {
            this.isLoadingPonto = true;
            try {
                const response = await this.folhaService.secullum(this.colaborador.cpf);
                if (response && response.ponto && response.ponto.Linhas) {
                    this.pontoData = this.mapPontoData(response.ponto.Linhas);
                    this.horasExtrasTotaisPonto = this.calculateTotalHorasExtras(this.pontoData);
                } else {
                    this.pontoData = [];
                    this.horasExtrasTotaisPonto = '00h 00m';
                }
            } catch (error) {
                console.error('Erro ao buscar dados de ponto:', error);
                this.pontoData = [];
                this.horasExtrasTotaisPonto = '00h 00m';
            } finally {
                this.isLoadingPonto = false;
            }
        },
        mapPontoData(linhas) {
            return linhas.map((item) => {
                const values = item.Value;
                const mappedItem = {};
                for (const key in this.fieldMap) {
                    if (Object.prototype.hasOwnProperty.call(this.fieldMap, key)) {
                        const index = this.fieldMap[key];
                        mappedItem[key] = values[index] || null;
                    }
                }
                // Adicionar o campo `extras` do total, que está em `Totais[9]`
                // e é diferente do `extras` individual diário em `Linhas[i].Value[9]`
                if (item.Key === 'Totais') {
                    mappedItem.extrasTotais = values[9] || '00:00';
                }
                return mappedItem;
            });
        },
        calculateTotalHorasExtras(linhas) {
            // Se houver um item 'Totais' no array mapeado, usa o valor diretamente
            const totais = linhas.find((item) => item.Key === 'Totais');
            if (totais && totais.extrasTotais) {
                const [hours, minutes] = totais.extrasTotais.split(':').map(Number);
                return `${String(hours).padStart(2, '0')}h ${String(minutes).padStart(2, '0')}m`;
            }
            // Se não, calcula manualmente (fallback)
            let totalMinutes = 0;
            linhas.forEach((linha) => {
                if (linha.extras) {
                    const [hours, minutes] = linha.extras.split(':').map(Number);
                    totalMinutes += hours * 60 + minutes;
                }
            });

            const totalHours = Math.floor(totalMinutes / 60);
            const remainingMinutes = totalMinutes % 60;

            return `${String(totalHours).padStart(2, '0')}h ${String(remainingMinutes).padStart(2, '0')}m`;
        },
        toggleSection(sectionId) {
            this.expandedSection = this.expandedSection === sectionId ? null : sectionId;
        },
        formatCurrency(value) {
            if (isNaN(value)) return 'R$ 0,00';
            return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
        },
        getMonthName(monthNumber) {
            const months = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
            return months[monthNumber - 1];
        },
        async generatePDF(contrachequeData) {
            if (this.isGeneratingPDF) return;
            this.isGeneratingPDF = true;
            try {
                const pdfTemplate = document.getElementById('pdf-template');
                pdfTemplate.style.display = 'block';
                document.getElementById('pdf-mes-ano').innerText = `${this.getMonthName(contrachequeData.folha.mes)}/${contrachequeData.folha.ano}`;

                const proventosList = document.getElementById('pdf-proventos');
                proventosList.innerHTML = '';
                contrachequeData.itens
                    .filter((i) => i.tipo === 'Provento')
                    .forEach((item) => {
                        const li = document.createElement('li');
                        li.className = 'flex justify-between items-center text-gray-700';
                        li.innerHTML = `<span>${item.descricao}</span><span class="font-semibold text-green-600">+ ${this.formatCurrency(item.valor)}</span>`;
                        proventosList.appendChild(li);
                    });

                const descontosList = document.getElementById('pdf-descontos');
                descontosList.innerHTML = '';
                contrachequeData.itens
                    .filter((i) => i.tipo === 'Desconto')
                    .forEach((item) => {
                        const li = document.createElement('li');
                        li.className = 'flex justify-between items-center text-gray-700';
                        li.innerHTML = `<span>${item.descricao}</span><span class="font-semibold text-red-600">- ${this.formatCurrency(item.valor)}</span>`;
                        descontosList.appendChild(li);
                    });

                document.getElementById('pdf-total-proventos').innerText = this.formatCurrency(contrachequeData.total_proventos);
                document.getElementById('pdf-total-descontos').innerText = this.formatCurrency(contrachequeData.total_descontos);
                document.getElementById('pdf-total-liquido').innerText = this.formatCurrency(contrachequeData.total_liquido);

                const canvas = await html2canvas(pdfTemplate, { scale: 2 });
                const imgData = canvas.toDataURL('image/png');
                const imgWidth = canvas.width;
                const imgHeight = canvas.height;
                const pdfWidth = imgWidth;
                const pdfHeight = imgHeight;

                const doc = new jsPDF({
                    orientation: pdfWidth > pdfHeight ? 'landscape' : 'portrait',
                    unit: 'px',
                    format: [pdfWidth, pdfHeight]
                });

                doc.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
                const filename = `Contracheque-${this.getMonthName(contrachequeData.folha.mes)}-${contrachequeData.folha.ano}.pdf`;
                doc.save(filename);
                pdfTemplate.style.display = 'none';
            } catch (error) {
                console.error('Erro ao gerar o PDF:', error);
                alert('Ocorreu um erro ao gerar o PDF. Por favor, tente novamente.');
            } finally {
                this.isGeneratingPDF = false;
            }
        }
    }
};
</script>

<template>
    <div class="min-h-screen bg-gray-100 p-6 sm:p-8 lg:p-12 font-sans text-gray-800">
        <header class="text-center mb-12 sm:mb-16 max-w-7xl mx-auto">
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold leading-tight">Painel do Colaborador</h1>
            <p class="text-lg sm:text-xl text-gray-500 mt-4">Uma visão completa e detalhada das suas informações profissionais.</p>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8 max-w-7xl mx-auto mb-10">
            <div class="bg-white p-6 rounded-2xl shadow-lg border-l-4 border-indigo-500">
                <div class="flex items-center mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-500 mr-2" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM12.75 6a.75.75 0 0 0-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 0 0 0-1.5h-3.75V6Z" />
                    </svg>
                    <span class="text-sm font-semibold">Horas Extras</span>
                </div>
                <p class="text-3xl sm:text-4xl font-extrabold">{{ horasExtrasTotaisPonto }}</p>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-lg border-l-4 border-yellow-500">
                <div class="flex items-center mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500 mr-2" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM8.25 10.5a.75.75 0 0 1 .75-.75h6a.75.75 0 0 1 0 1.5h-6a.75.75 0 0 1-.75-.75Zm-.75 3a.75.75 0 0 0 0 1.5h7.5a.75.75 0 0 0 0-1.5h-7.5Z"
                        />
                    </svg>
                    <span class="text-sm font-semibold text-gray-500">Salário Base Atual</span>
                </div>
                <p class="text-3xl sm:text-4xl font-extrabold text-gray-800">{{ formatCurrency(salarioBaseAtual) }}</p>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-lg border-l-4 border-green-500">
                <div class="flex items-center mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500 mr-2" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM8.25 10.5a.75.75 0 0 1 .75-.75h6a.75.75 0 0 1 0 1.5h-6a.75.75 0 0 1-.75-.75Zm-.75 3a.75.75 0 0 0 0 1.5h7.5a.75.75 0 0 0 0-1.5h-7.5Z"
                        />
                    </svg>
                    <span class="text-sm font-semibold text-gray-500">Último Salário Líquido</span>
                </div>
                <p class="text-3xl sm:text-4xl font-extrabold">{{ formatCurrency(salarioLiquidoAtual) }}</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto bg-white p-6 sm:p-8 rounded-3xl shadow-2xl mb-8">
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-500 mr-3" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM12.75 6a.75.75 0 0 0-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 0 0 0-1.5h-3.75V6Z" />
                    </svg>
                    <h2 class="text-2xl font-bold text-gray-700">Registro de Ponto</h2>
                </div>
                <button @click="toggleSection('ponto')" class="flex items-center px-4 py-2 bg-blue-500 text-white rounded-full hover:bg-blue-600 transition-colors duration-200 focus:outline-none">
                    <span class="mr-2">{{ expandedSection === 'ponto' ? 'Recolher' : 'Ver detalhes' }}</span>
                    <i :class="['pi', expandedSection === 'ponto' ? 'pi-chevron-up' : 'pi-chevron-down']"></i>
                </button>
            </div>

            <div v-if="isLoadingPonto" class="flex justify-center items-center py-8">
                <svg class="animate-spin -ml-1 mr-3 h-10 w-10 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-xl font-medium text-gray-600">Carregando dados...</span>
            </div>

            <transition name="fade">
                <div v-if="expandedSection === 'ponto' && !isLoadingPonto">
                    <DataTable :value="pontoData" dataKey="data" class="p-datatable-sm" tableStyle="min-width: 50rem">
                        <Column expander style="width: 3rem" />

                        <Column field="data" header="Data" />
                        <Column field="entrada1" header="Entrada 1" />
                        <Column field="saida1" header="Saída 1" />
                        <Column field="entrada2" header="Entrada 2" />
                        <Column field="saida2" header="Saída 2" />
                        <Column field="normais" header="Horas Normais" />
                        <Column field="saldoBanco" header="Saldo Banco" />

                        <template #expansion="slotProps">
                            <div class="p-4 bg-gray-50 rounded-lg shadow-inner grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                <div>
                                    <h3 class="font-semibold mb-2 text-gray-700">Detalhes Adicionais</h3>
                                    <ul class="space-y-1 text-sm">
                                        <li class="flex justify-between text-gray-600">
                                            <span>Horas Extras:</span>
                                            <span class="font-medium text-green-600">{{ slotProps.data.extras || '00:00' }}</span>
                                        </li>
                                        <li class="flex justify-between text-gray-600">
                                            <span>Horas de Falta:</span>
                                            <span class="font-medium text-red-600">{{ slotProps.data.faltas || '00:00' }}</span>
                                        </li>
                                        <li class="flex justify-between text-gray-600">
                                            <span>Saldo Total Banco:</span>
                                            <span class="font-medium">{{ slotProps.data.totalBanco || '00:00' }}</span>
                                        </li>
                                    </ul>
                                </div>
                                <div v-if="slotProps.data.justificativa">
                                    <h3 class="font-semibold mb-2 text-gray-700">Observações</h3>
                                    <p class="text-sm text-gray-600">{{ slotProps.data.justificativa }}</p>
                                </div>
                            </div>
                        </template>
                    </DataTable>
                </div>
            </transition>
        </div>

        <div class="max-w-7xl mx-auto bg-white p-6 sm:p-8 rounded-3xl shadow-2xl mb-8">
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-500 mr-3" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM8.25 10.5a.75.75 0 0 1 .75-.75h6a.75.75 0 0 1 0 1.5h-6a.75.75 0 0 1-.75-.75Zm-.75 3a.75.75 0 0 0 0 1.5h7.5a.75.75 0 0 0 0-1.5h-7.5Z"
                        />
                    </svg>
                    <h2 class="text-2xl font-bold text-gray-700">Contracheques Detalhados</h2>
                </div>
                <button @click="toggleSection('contracheques')" class="flex items-center px-4 py-2 bg-indigo-500 text-white rounded-full hover:bg-indigo-600 transition-colors duration-200 focus:outline-none">
                    <span class="mr-2">{{ expandedSection === 'contracheques' ? 'Recolher' : 'Ver detalhes' }}</span>
                    <i :class="['pi', expandedSection === 'contracheques' ? 'pi-chevron-up' : 'pi-chevron-down']"></i>
                </button>
            </div>

            <div v-if="isGeneratingPDF" class="flex justify-center items-center py-8">
                <svg class="animate-spin -ml-1 mr-3 h-10 w-10 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-xl font-medium text-gray-600">Gerando PDF...</span>
            </div>

            <transition name="fade">
                <div v-if="expandedSection === 'contracheques' && !isGeneratingPDF">
                    <DataTable :value="contracheques" v-model:expandedRows="expandedRows" dataKey="id" class="p-datatable-sm" tableStyle="min-width: 50rem">
                        <Column expander style="width: 3rem" />

                        <Column field="mes" header="Mês/Ano">
                            <template #body="slotProps"> {{ getMonthName(slotProps.data.folha.mes) }}/{{ slotProps.data.folha.ano }} </template>
                        </Column>
                        <Column field="total_proventos" header="Total Proventos">
                            <template #body="slotProps"> {{ formatCurrency(slotProps.data.total_proventos) }} </template>
                        </Column>
                        <Column field="total_descontos" header="Total Descontos">
                            <template #body="slotProps"> {{ formatCurrency(slotProps.data.total_descontos) }} </template>
                        </Column>
                        <Column field="total_liquido" header="Salário Líquido">
                            <template #body="slotProps"> {{ formatCurrency(slotProps.data.total_liquido) }} </template>
                        </Column>
                        <Column header="Ações" style="width: 6rem">
                            <template #body="slotProps">
                                <button
                                    @click="generatePDF(slotProps.data)"
                                    :class="['transition-colors duration-200 font-semibold', slotProps.data.liberado ? 'text-indigo-600 hover:text-indigo-800' : 'text-gray-400 cursor-not-allowed']"
                                    :disabled="!slotProps.data.liberado"
                                >
                                    Baixar
                                </button>
                            </template>
                        </Column>

                        <template #expansion="slotProps">
                            <div class="p-4 bg-gray-50 rounded-lg shadow-inner grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h3 class="font-semibold mb-2 text-gray-700">Proventos</h3>
                                    <ul class="space-y-1 text-sm">
                                        <li v-for="item in slotProps.data.itens.filter((i) => i.tipo === 'Provento')" :key="item.id" class="flex justify-between text-gray-600">
                                            <span>{{ item.descricao }}</span>
                                            <span class="font-medium text-green-600">+ {{ formatCurrency(item.valor) }}</span>
                                        </li>
                                    </ul>
                                </div>
                                <div>
                                    <h3 class="font-semibold mb-2 text-gray-700">Descontos</h3>
                                    <ul class="space-y-1 text-sm">
                                        <li v-for="item in slotProps.data.itens.filter((i) => i.tipo === 'Desconto')" :key="item.id" class="flex justify-between text-gray-600">
                                            <span>{{ item.descricao }}</span>
                                            <span class="font-medium text-red-600">- {{ formatCurrency(item.valor) }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </template>
                    </DataTable>
                </div>
            </transition>
        </div>

        <div id="pdf-template" class="p-10 bg-white shadow-xl font-sans text-gray-800" style="width: 841.89px; height: 595.28px; display: none">
            <div class="text-center mb-8">
                <img src="../../../../public/img/logo_sem_fundo.png" alt="Logo" class="w-32 mx-auto mb-2" />
                <h1 class="text-2xl font-bold text-gray-900">Demonstrativo de Pagamento</h1>
                <p class="text-md text-gray-600 mt-1" id="pdf-mes-ano">Mês/Ano</p>
            </div>

            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-3 text-gray-800">Dados do Colaborador</h2>
                <div class="grid grid-cols-2 gap-4 text-sm text-gray-700">
                    <div>
                        <strong>Nome:</strong> <span class="text-gray-900">{{ colaborador.nome }}</span>
                    </div>
                    <div><strong>Empresa:</strong> <span class="text-gray-900">Centrais Contruções Pesadas S.A.</span></div>
                    <div>
                        <strong>Cargo:</strong> <span class="text-gray-900">{{ colaborador.cargo }}</span>
                    </div>
                    <div>
                        <strong>Salário Base:</strong> <span class="text-gray-900">{{ formatCurrency(salarioBaseAtual) }}</span>
                    </div>
                </div>
            </div>

            <div class="border-t border-b border-gray-300 py-6 mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Proventos</h3>
                    <ul class="space-y-1 text-sm" id="pdf-proventos"></ul>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Descontos</h3>
                    <ul class="space-y-1 text-sm" id="pdf-descontos"></ul>
                </div>
                <div class="flex flex-col justify-end border-t border-gray-300 mt-4 pt-4">
                    <div class="flex justify-between items-center font-bold text-base text-gray-800">
                        <span>Total Proventos:</span>
                        <span id="pdf-total-proventos" class="text-green-600">R$ 0,00</span>
                    </div>
                    <div class="flex justify-between items-center font-bold text-base text-gray-800">
                        <span>Total Descontos:</span>
                        <span id="pdf-total-descontos" class="text-red-600">R$ 0,00</span>
                    </div>
                    <div class="flex justify-between items-center font-bold text-xl mt-4 text-gray-900">
                        <span>SALÁRIO LÍQUIDO:</span>
                        <span id="pdf-total-liquido" class="text-indigo-600">R$ 0,00</span>
                    </div>
                </div>
            </div>

            <footer class="text-center text-xs text-gray-500 mt-8 pt-4 border-t border-gray-200">
                <p>Documento gerado eletronicamente em {{ new Date().toLocaleDateString('pt-BR') }} às {{ new Date().toLocaleTimeString('pt-BR') }}.</p>
            </footer>
        </div>
    </div>
</template>

<style scoped>
/* Transição suave para expandir/recolher */
.fade-enter-active,
.fade-leave-active {
    transition: all 0.5s ease-in-out;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(-20px);
}
.fade-enter-to,
.fade-leave-from {
    opacity: 1;
    transform: translateY(0);
}

/* Estilos de tabela do PrimeVue */
.p-datatable .p-datatable-thead > tr > th {
    background-color: #f3f4f6;
    color: #4b5563;
    font-weight: bold;
}
</style>
