<script>
import { ref } from 'vue';
import FileUpload from 'primevue/fileupload';
import Calendar from 'primevue/calendar';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';
import FolhaService from '../../../service/FolhaService';

export default {
    components: { FileUpload, Calendar, Toast },
    setup() {
        const respostas = ref([]);
        const mesAno = ref(null);
        const folhaService = new FolhaService();
        const toast = useToast();

        const uploadFile = async (event) => {
            if (!mesAno.value) {
                toast.add({
                    severity: 'warn',
                    summary: 'Atenção',
                    detail: 'Por favor, selecione o mês e ano referente ao contracheque.',
                    life: 3000
                });
                return;
            }

            const file = event.files[0];
            if (!file) {
                toast.add({
                    severity: 'warn',
                    summary: 'Atenção',
                    detail: 'Selecione um arquivo .txt',
                    life: 3000
                });
                return;
            }

            const mes = mesAno.value.getMonth() + 1;
            const ano = mesAno.value.getFullYear();

            try {
                const data = await folhaService.importaFolha(mes, ano, file);
                respostas.value = data.status === true ? 'Folha importada com sucesso.' : 'Erro ao importar folha.';

                toast.add({
                    severity: data.status ? 'success' : 'error',
                    summary: 'Importação',
                    detail: respostas.value,
                    life: 3000
                });
            } catch (error) {
                toast.add({
                    severity: 'error',
                    summary: 'Erro',
                    detail: 'Erro ao importar folha.',
                    life: 3000
                });
            }
        };

        return { respostas, mesAno, uploadFile };
    }
};
</script>

<template>
    <!-- Container do Toast precisa estar na árvore -->
    <Toast />

    <div class="flex flex-col items-center justify-center min-h-screen p-4">
        <h2 class="mb-4">Envio de Arquivo TXT</h2>

        <!-- Campo para mês e ano -->
        <div class="mb-4 w-full max-w-sm">
            <label for="mesAno" class="block mb-1 font-medium">Mês/Ano Referente</label>
            <Calendar lang="pt-BR" id="mesAno" v-model="mesAno" view="month" dateFormat="mm/yy" showIcon class="w-full" />
        </div>

        <!-- Upload do arquivo -->
        <FileUpload name="file" accept=".txt" :customUpload="true" @uploader="uploadFile" chooseLabel="Selecionar Arquivo" uploadLabel="Enviar" cancelLabel="Cancelar" class="w-full max-w-sm" />
    </div>
</template>
