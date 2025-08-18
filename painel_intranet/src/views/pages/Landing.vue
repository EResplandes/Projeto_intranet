<script>
import { ref } from 'vue';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Button from 'primevue/button';
import Message from 'primevue/message';
import AutenticacaoService from '../../service/AutenticacaoService';

export default {
    name: 'LoginPage',
    components: {
        InputText,
        Password,
        Button,
        Message
    },
    data() {
        return {
            form: {
                email: '',
                senha: ''
            },
            erro: {
                email: false,
                senha: false,
                login: false,
                ativo: false
            },
            autenticacaoService: new AutenticacaoService()
        };
    },
    methods: {
        async logar() {
            if (this.form.email === '') {
                this.erro.email = true;
                return;
            }

            if (this.form.senha === '') {
                this.erro.senha = true;
                return;
            }

            try {
                await this.autenticacaoService.logar(this.form).then((data) => {
                    if (data.token) {
                        console.log(data);
                        localStorage.setItem('token', data.token);
                        localStorage.setItem('usuario', data.usuario[0].nome);
                        localStorage.setItem('usuario_id', data.usuario[0].id);
                        localStorage.setItem('cargo', data.usuario[0].cargo);
                        localStorage.setItem('imagem', data.usuario[0].imagem);
                        localStorage.setItem('permissoes', JSON.stringify(data.permissoes));
                        localStorage.setItem('cpf', data.usuario[0].cpf);
                        localStorage.setItem('admin', data.usuario[0].admin);

                        if (data.usuario[0].admin === 1 && data.status) {
                            this.$router.push({ name: 'colaboradores' });
                        } else if (data.usuario[0].admin === 0 && data.status) {
                            this.$router.push({ name: 'inicio' });
                        } else {
                            this.erro.ativo = true;
                        }
                    } else {
                        this.erro.login = true;
                    }
                });
            } catch (error) {
                this.erro.login = true;
                console.error(error);
            }
        }
    }
};
</script>

<template>
    <div class="flex items-center justify-center min-h-screen bg-gray-100">
        <!-- Card -->
        <div class="flex w-full max-w-3xl bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Coluna Esquerda - Logo -->
            <div class="hidden md:flex w-1/2 bg-[#004285] items-center justify-center p-8">
                <img src="https://link.gruporialma.com.br/assets/logo_sem_fundo-4776d6e6.png" alt="Logo" class="max-w-[200px] w-full" />
            </div>

            <!-- Coluna Direita - Formulário -->
            <div class="flex w-full md:w-1/2 items-center justify-center p-8">
                <div class="w-full max-w-sm">
                    <h2 class="text-2xl font-bold text-center mb-6">RH Connect | Grupo Rialma</h2>

                    <form @submit.prevent="logar">
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                            <InputText id="email" v-model="form.email" placeholder="email@gruporialma.com.br" class="w-full" />
                        </div>

                        <div v-if="erro.email" class="mb-4">
                            <Message severity="error">Insira um e-mail!</Message>
                        </div>

                        <div class="mb-4">
                            <label for="senha" class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
                            <Password :feedback="false" id="senha" v-model="form.senha" placeholder="******" toggleMask class="w-full" inputClass="w-full" />
                        </div>

                        <div v-if="erro.senha" class="mb-4">
                            <Message severity="error">Insira uma senha!</Message>
                        </div>

                        <div class="flex items-center justify-between mb-4 text-sm">
                            <div class="flex items-center">
                                <input id="remember" type="checkbox" class="mr-2" />
                                <label for="remember" class="text-gray-600">Lembrar-me</label>
                            </div>
                            <a href="#" class="text-blue-600 hover:underline">Esqueceu a senha?</a>
                        </div>

                        <Button style="background-color: #004285" label="Entrar" type="submit" class="w-full mb-4 btn btn-info" severity="info" />

                        <div v-if="erro.login" class="mb-4">
                            <Message severity="error">Login ou senha incorreto(s)!</Message>
                        </div>
                        <div v-if="erro.ativo" class="mb-4">
                            <Message severity="error">Usuário inativo!</Message>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
