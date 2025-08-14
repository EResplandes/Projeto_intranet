<template>
    <div class="min-h-screen bg-gray-100 p-6 sm:p-8 lg:p-12 font-sans text-gray-800">
        <header class="text-center mb-12 sm:mb-16 max-w-7xl mx-auto">
            <p class="text-lg sm:text-xl text-gray-500 mt-4">Seu portal completo de informações e serviços.</p>
        </header>

        <!-- Seção principal do dashboard com grid responsivo -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 sm:gap-10 max-w-7xl mx-auto mb-10">
            <!-- Card de Resumo de Horas (em destaque) -->
            <div class="lg:col-span-1 bg-white p-6 sm:p-8 rounded-3xl shadow-2xl transition-all duration-300 hover:scale-[1.02] hover:shadow-3xl transform border-t-4 border-indigo-500">
                <div class="flex items-center mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-500 mr-3" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            fill-rule="evenodd"
                            d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM12.75 6a.75.75 0 0 0-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 0 0 0-1.5h-3.75V6Z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <h2 class="text-2xl font-bold text-gray-700">Resumo de Horas</h2>
                </div>
                <div class="flex justify-between items-center w-full mb-8">
                    <div class="text-center flex-1">
                        <p class="text-gray-500 text-sm mb-1">Saldo</p>
                        <p class="text-4xl font-black text-green-600">{{ saldoHoras }}h</p>
                    </div>
                    <div class="text-center flex-1">
                        <p class="text-gray-500 text-sm mb-1">Devendo</p>
                        <p class="text-4xl font-black text-red-600">{{ horasDevendo }}h</p>
                    </div>
                    <div class="text-center flex-1">
                        <p class="text-gray-500 text-sm mb-1">Extras</p>
                        <p class="text-4xl font-black text-blue-600">{{ horasExtras }}h</p>
                    </div>
                </div>
                <div class="w-full h-64">
                    <Chart type="doughnut" :data="chartData" :options="chartOptions" />
                </div>
            </div>

            <!-- Card de Últimos Contracheques -->
            <div class="lg:col-span-1 bg-white p-6 sm:p-8 rounded-3xl shadow-2xl transition-all duration-300 hover:scale-[1.02] hover:shadow-3xl transform">
                <div class="flex items-center mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-500 mr-3" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            fill-rule="evenodd"
                            d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM8.25 10.5a.75.75 0 0 1 .75-.75h6a.75.75 0 0 1 0 1.5h-6a.75.75 0 0 1-.75-.75Zm-.75 3a.75.75 0 0 0 0 1.5h7.5a.75.75 0 0 0 0-1.5h-7.5Z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <h2 class="text-2xl font-bold text-gray-700">Últimos Contracheques</h2>
                </div>
                <ul class="divide-y divide-gray-200 flex-grow overflow-auto max-h-72">
                    <li v-for="(contracheque, index) in contracheques" :key="index" class="py-4 px-2 flex justify-between items-center hover:bg-gray-50 cursor-pointer rounded-xl transition-colors duration-200">
                        <span class="text-gray-700 font-medium">{{ contracheque.mes }} - {{ contracheque.ano }}</span>
                        <a :href="contracheque.link" target="_blank" class="bg-indigo-600 text-white px-4 py-2 rounded-full font-semibold text-sm hover:bg-indigo-700 transition-colors duration-200 shadow-md"> Ver PDF </a>
                    </li>
                </ul>
            </div>

            <!-- Novo Card de Histórico Salarial com gráfico de linha -->
            <div class="lg:col-span-1 bg-white p-6 sm:p-8 rounded-3xl shadow-2xl transition-all duration-300 hover:scale-[1.02] hover:shadow-3xl transform">
                <div class="flex items-center mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-500 mr-3" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            fill-rule="evenodd"
                            d="M2.25 13.5a.75.75 0 0 1 .75-.75H18a.75.75 0 0 1 0 1.5H3a.75.75 0 0 1-.75-.75ZM2.25 18a.75.75 0 0 1 .75-.75H18a.75.75 0 0 1 0 1.5H3a.75.75 0 0 1-.75-.75ZM3 6a.75.75 0 0 0 0 1.5h15a.75.75 0 0 0 0-1.5H3Z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <h2 class="text-2xl font-bold text-gray-700">Histórico Salarial Anual</h2>
                </div>
                <div class="w-full h-64">
                    <Chart type="line" :data="salarioChartData" :options="salarioChartOptions" />
                </div>
            </div>
        </div>

        <!-- Cards informativos (novas funcionalidades) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 sm:gap-10 max-w-7xl mx-auto mb-10">
            <!-- Card sobre Horas Extras e Banco de Horas -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-2xl transition-all duration-300 hover:scale-[1.02] hover:shadow-3xl transform border-l-4 border-teal-500">
                <div class="flex items-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-teal-500 mr-3" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM12.75 9a.75.75 0 0 0-1.5 0v2.25H9a.75.75 0 0 0 0 1.5h2.25V15a.75.75 0 0 0 1.5 0v-2.25H15a.75.75 0 0 0 0-1.5h-2.25V9Z"
                        />
                    </svg>
                    <h3 class="text-xl font-bold text-gray-700">Visualizar e Gerenciar Horas Extras</h3>
                </div>
                <p class="text-gray-600 mt-2">
                    Acompanhe o seu saldo de banco de horas e o registro de horas extras de forma transparente. Você pode usar o chat para tirar dúvidas sobre como compensar essas horas ou solicitar informações detalhadas sobre a política da empresa.
                </p>
            </div>
            <!-- Card sobre Atualização Cadastral -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-2xl transition-all duration-300 hover:scale-[1.02] hover:shadow-3xl transform border-l-4 border-orange-500">
                <div class="flex items-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-orange-500 mr-3" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M7.5 7.5a.75.75 0 0 0-1.5 0v.75H4.5A2.25 2.25 0 0 0 2.25 10.5v1.5a.75.75 0 0 0 1.5 0v-1.5a.75.75 0 0 1 .75-.75h1.5v.75a.75.75 0 0 0 1.5 0v-.75H12a.75.75 0 0 0 0 1.5H7.5a.75.75 0 0 0 0 1.5H12a.75.75 0 0 0 0 1.5H7.5a.75.75 0 0 0 0 1.5h4.5a.75.75 0 0 0 0-1.5H9a.75.75 0 0 1-.75-.75V15a2.25 2.25 0 0 0-2.25-2.25H4.5a.75.75 0 0 1 0-1.5h.75a.75.75 0 0 0 0-1.5H4.5a.75.75 0 0 0 0 1.5H5.25a.75.75 0 0 0 0 1.5H4.5v.75a.75.75 0 0 0 1.5 0v-.75h1.5a.75.75 0 0 0 0-1.5H6.75v-.75a.75.75 0 0 0-1.5 0v.75H4.5a.75.75 0 0 0-1.5 0v1.5c0 .776.666 1.405 1.5 1.474V16.5a.75.75 0 0 0 1.5 0v-1.5H12a.75.75 0 0 0 0-1.5H7.5a.75.75 0 0 0 0-1.5H12a.75.75 0 0 0 0-1.5H7.5a.75.75 0 0 0 0-1.5H12V7.5H4.5a.75.75 0 0 0-1.5 0v.75c-.828 0-1.5.672-1.5 1.5v1.5a.75.75 0 0 0 1.5 0v-1.5Z"
                        />
                        <path
                            d="M12 1.5a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-1.5 0V2.25A.75.75 0 0 1 12 1.5ZM12 4.5a.75.75 0 0 1 .75.75V6a.75.75 0 0 1-1.5 0V5.25A.75.75 0 0 1 12 4.5ZM12 7.5a.75.75 0 0 1 .75.75V9a.75.75 0 0 1-1.5 0V8.25A.75.75 0 0 1 12 7.5ZM12 10.5a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-1.5 0v-1.5a.75.75 0 0 1 .75-.75Z"
                        />
                        <path fill-rule="evenodd" d="M16.5 7.5a.75.75 0 0 1 0 1.5h-1.5a.75.75 0 0 1 0-1.5h1.5Z" clip-rule="evenodd" />
                    </svg>
                    <h3 class="text-xl font-bold text-gray-700">Atualização de Dados Cadastrais</h3>
                </div>
                <p class="text-gray-600 mt-2">
                    Mantenha suas informações pessoais e de contato sempre atualizadas. Se precisar alterar seu endereço, telefone ou e-mail, fale com a AI para obter o formulário correto ou ser direcionado ao setor responsável.
                </p>
            </div>
            <!-- Card sobre Férias e Benefícios -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-2xl transition-all duration-300 hover:scale-[1.02] hover:shadow-3xl transform border-l-4 border-sky-500">
                <div class="flex items-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-sky-500 mr-3" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12.516 6.32a.75.75 0 0 0-1.032 0l-5.75 6.75A.75.75 0 0 0 6.25 14h11.5a.75.75 0 0 0 .516-1.079l-5.75-6.75Z" />
                        <path
                            fill-rule="evenodd"
                            d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM14.165 6.945l-5.75 6.75a.75.75 0 0 1-1.032 0L5.25 11.47a.75.75 0 0 1 1.03-1.085l2.457 2.336 4.72-5.536a.75.75 0 0 1 1.03-.004Z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <h3 class="text-xl font-bold text-gray-700">Férias e Benefícios</h3>
                </div>
                <p class="text-gray-600 mt-2">
                    Precisa tirar dúvidas sobre seus benefícios, como plano de saúde e vale-refeição, ou saber como solicitar suas férias? O chat está disponível para ajudar a esclarecer qualquer questão relacionada a esses temas.
                </p>
            </div>
            <!-- Card sobre Avaliação de Desempenho -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-2xl transition-all duration-300 hover:scale-[1.02] hover:shadow-3xl transform border-l-4 border-violet-500">
                <div class="flex items-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-violet-500 mr-3" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M12 2.25a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-1.5 0V3a.75.75 0 0 1 .75-.75ZM7.5 5.25A.75.75 0 0 1 8.25 6v1.5a.75.75 0 0 1-1.5 0V6a.75.75 0 0 1 .75-.75ZM4.5 10.5a.75.75 0 0 1 .75-.75h1.5a.75.75 0 0 1 0 1.5H5.25a.75.75 0 0 1-.75-.75ZM4.5 15a.75.75 0 0 1 .75-.75h1.5a.75.75 0 0 1 0 1.5H5.25a.75.75 0 0 1-.75-.75ZM7.5 18a.75.75 0 0 1 .75-.75h1.5a.75.75 0 0 1 0 1.5H8.25a.75.75 0 0 1-.75-.75ZM12 18a.75.75 0 0 1 .75-.75h1.5a.75.75 0 0 1 0 1.5H12.75a.75.75 0 0 1-.75-.75Z"
                        />
                        <path
                            fill-rule="evenodd"
                            d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM6.75 12a.75.75 0 0 1 .75-.75h1.5a.75.75 0 0 1 0 1.5H7.5a.75.75 0 0 1-.75-.75ZM15 12a.75.75 0 0 1 .75-.75h1.5a.75.75 0 0 1 0 1.5H15.75a.75.75 0 0 1-.75-.75ZM12 15a.75.75 0 0 1 .75-.75h1.5a.75.75 0 0 1 0 1.5H12.75a.75.75 0 0 1-.75-.75Z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <h3 class="text-xl font-bold text-gray-700">Avaliação de Desempenho</h3>
                </div>
                <p class="text-gray-600 mt-2">Acompanhe seu progresso e metas. Use o chat para obter orientações sobre como se preparar para sua próxima avaliação de desempenho ou para entender melhor os critérios utilizados.</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, nextTick, onMounted, computed } from 'vue';
import Chart from 'primevue/chart';

// Variáveis de estado
const saldoHoras = ref(12.5);
const horasDevendo = ref(3);
const horasExtras = ref(5);
const isLoading = ref(false);

const contracheques = ref([
    { mes: 'Julho', ano: 2025, link: 'https://exemplo.com/contracheque-julho.pdf' },
    { mes: 'Junho', ano: 2025, link: 'https://exemplo.com/contracheque-junho.pdf' },
    { mes: 'Maio', ano: 2025, link: 'https://exemplo.com/contracheque-maio.pdf' }
]);

// Dados de exemplo para o histórico salarial
const salarioHistorico = ref([
    { mes: 'Jan', valor: 3500 },
    { mes: 'Fev', valor: 3500 },
    { mes: 'Mar', valor: 3600 },
    { mes: 'Abr', valor: 3650 },
    { mes: 'Mai', valor: 3650 },
    { mes: 'Jun', valor: 3700 },
    { mes: 'Jul', valor: 3700 },
    { mes: 'Ago', valor: 3750 },
    { mes: 'Set', valor: 3800 },
    { mes: 'Out', valor: 3800 },
    { mes: 'Nov', valor: 3850 },
    { mes: 'Dez', valor: 3900 }
]);

const mensagens = ref([{ usuario: 'AI', texto: 'Olá! Bem-vindo ao chat de Dúvidas Salariais. Posso ajudar com questões sobre pagamentos, benefícios ou contracheques?' }]);

const novaMensagem = ref('');
const chatBox = ref(null);

// Função para rolar o chat para o fim
const scrollToBottom = () => {
    nextTick(() => {
        if (chatBox.value) {
            chatBox.value.scrollTop = chatBox.value.scrollHeight;
        }
    });
};

// Integração com o Gemini API para simular uma resposta de AI
async function getGeminiResponse(prompt) {
    let chatHistory = [];
    chatHistory.push({ role: 'user', parts: [{ text: prompt }] });
    const payload = { contents: chatHistory };
    const apiKey = '';
    const apiUrl = `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-preview-05-20:generateContent?key=${apiKey}`;

    try {
        let response = await fetch(apiUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        // Implementação de exponential backoff para tratamento de erros
        let retries = 0;
        const maxRetries = 5;
        const initialDelay = 1000; // 1 second

        while (!response.ok && retries < maxRetries) {
            const delay = initialDelay * Math.pow(2, retries);
            await new Promise((res) => setTimeout(res, delay));
            response = await fetch(apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            retries++;
        }

        if (!response.ok) {
            throw new Error(`API call failed with status: ${response.status}`);
        }

        const result = await response.json();
        if (result.candidates && result.candidates.length > 0 && result.candidates[0].content && result.candidates[0].content.parts && result.candidates[0].content.parts.length > 0) {
            return result.candidates[0].content.parts[0].text;
        } else {
            return 'Não consegui entender a sua pergunta. Poderia reformular?';
        }
    } catch (error) {
        console.error('Erro na chamada da API:', error);
        return 'Ocorreu um erro ao processar a sua solicitação. Por favor, tente novamente mais tarde.';
    }
}

// Lógica para enviar a mensagem
async function enviarMensagem() {
    if (novaMensagem.value.trim() === '') return;

    // Adiciona a mensagem do usuário
    const userMessage = novaMensagem.value.trim();
    mensagens.value.push({ usuario: 'Você', texto: userMessage });
    novaMensagem.value = '';
    scrollToBottom();

    isLoading.value = true;
    scrollToBottom();

    // Obtém a resposta da AI e adiciona ao chat
    const aiResponse = await getGeminiResponse(`Responda de forma concisa e amigável como um assistente de RH a seguinte pergunta: "${userMessage}"`);

    isLoading.value = false;
    mensagens.value.push({ usuario: 'AI', texto: aiResponse });
    scrollToBottom();
}

// Dados do gráfico de Rosca (computed para reatividade)
const chartData = computed(() => {
    return {
        labels: ['Saldo de Horas', 'Horas Devendo', 'Horas Extras'],
        datasets: [
            {
                data: [saldoHoras.value, horasDevendo.value, horasExtras.value],
                backgroundColor: ['#22c55e', '#ef4444', '#3b82f6'],
                hoverBackgroundColor: ['#16a34a', '#dc2626', '#2563eb']
            }
        ]
    };
});

// Opções do gráfico de Rosca
const chartOptions = ref({
    cutout: '70%',
    plugins: {
        legend: {
            display: false // Esconde a legenda para um visual mais limpo
        },
        tooltip: {
            enabled: true
        }
    },
    maintainAspectRatio: false
});

// Dados do novo gráfico de linha para salários
const salarioChartData = computed(() => {
    const documentStyle = getComputedStyle(document.documentElement);

    return {
        labels: salarioHistorico.value.map((s) => s.mes),
        datasets: [
            {
                label: 'Salário Bruto',
                data: salarioHistorico.value.map((s) => s.valor),
                fill: false,
                borderColor: documentStyle.getPropertyValue('--indigo-500') || '#6366f1',
                tension: 0.4
            }
        ]
    };
});

// Opções do gráfico de linha
const salarioChartOptions = ref({
    maintainAspectRatio: false,
    aspectRatio: 0.8,
    plugins: {
        legend: {
            position: 'top',
            labels: {
                color: '#4b5563',
                font: { weight: 'bold' }
            }
        },
        tooltip: {
            callbacks: {
                label: function (context) {
                    let label = context.dataset.label || '';
                    if (label) {
                        label += ': ';
                    }
                    if (context.parsed.y !== null) {
                        label += new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(context.parsed.y);
                    }
                    return label;
                }
            }
        }
    },
    scales: {
        x: {
            ticks: {
                color: '#4b5563',
                font: { weight: 'bold' }
            },
            grid: {
                color: '#e5e7eb'
            }
        },
        y: {
            ticks: {
                color: '#4b5563',
                font: { weight: 'bold' },
                callback: function (value) {
                    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
                }
            },
            grid: {
                color: '#e5e7eb'
            }
        }
    }
});

onMounted(() => {
    scrollToBottom();
});
</script>

<style scoped>
/* Animação de digitação da IA */
.dot-flashing {
    position: relative;
    width: 10px;
    height: 10px;
    border-radius: 5px;
    background-color: #94a3b8;
    color: #94a3b8;
    animation: dotFlashing 1s infinite linear alternate;
    animation-delay: 0.5s;
}

.dot-flashing::before,
.dot-flashing::after {
    content: '';
    display: inline-block;
    position: absolute;
    top: 0;
}

.dot-flashing::before {
    left: -15px;
    width: 10px;
    height: 10px;
    border-radius: 5px;
    background-color: #94a3b8;
    color: #94a3b8;
    animation: dotFlashing 1s infinite alternate;
    animation-delay: 0s;
}

.dot-flashing::after {
    left: 15px;
    width: 10px;
    height: 10px;
    border-radius: 5px;
    background-color: #94a3b8;
    color: #94a3b8;
    animation: dotFlashing 1s infinite alternate;
    animation-delay: 1s;
}

@keyframes dotFlashing {
    0% {
        background-color: #94a3b8;
    }
    50%,
    100% {
        background-color: #e2e8f0;
    }
}
</style>
