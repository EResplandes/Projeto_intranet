<script>
import { ref } from 'vue';
import FileUpload from 'primevue/fileupload';
import Calendar from 'primevue/calendar';

export default {
    components: {
        FileUpload,
        Calendar
    },
    setup() {
        const respostas = ref([]);
        const mesAno = ref(null);

        const uploadFile = async (event) => {
            if (!mesAno.value) {
                alert('Por favor, selecione o mês e ano referente ao contracheque.');
                return;
            }

            const file = event.files[0];
            const formData = new FormData();
            formData.append('file', file);
            formData.append('mesAno', mesAno.value.toLocaleDateString('pt-BR', { month: '2-digit', year: 'numeric' }));

            // try {
            //     const { data } = await axios.post('http://localhost:8000/api/upload', formData, {
            //         headers: { 'Content-Type': 'multipart/form-data' }
            //     });
            //     respostas.value = data.respostas;
            // } catch (error) {
            //     console.error('Erro ao enviar arquivo:', error);
            // }
        };

        return { respostas, mesAno, uploadFile };
    }
};
</script>

<template>
    <div class="flex flex-col items-center justify-center min-h-screen p-4">
        <h2 class="mb-4">Envio de Arquivo TXT</h2>

        <!-- Campo para mês e ano -->
        <div class="mb-4 w-full max-w-sm">
            <label for="mesAno" class="block mb-1 font-medium">Mês/Ano Referente</label>
            <Calendar lang="pt-BR" id="mesAno" v-model="mesAno" view="month" dateFormat="mm/yy" showIcon class="w-full" />
        </div>

        <!-- Upload do arquivo -->
        <FileUpload name="file" accept=".txt" chooseLabel="Selecionar Arquivo" uploadLabel="Enviar" cancelLabel="Cancelar" @uploader="uploadFile" class="w-full max-w-sm" />
    </div>
</template>

<style>
.p-4 {
    max-width: 600px;
    margin: auto;
}

.centralizado {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 60vh;
}
</style>
