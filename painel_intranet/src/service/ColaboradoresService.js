const token = localStorage.getItem('token');

import API_URL from './Config.js';

export default class ColaboradoresService {
    async buscaTodosColaboradores() {
        return await fetch(`${API_URL}/colaboradores/`, {
            method: 'GET',
            headers: {
                Accept: 'application/json',
                Authorization: 'Bearer ' + token
            }
        })
            .then((res) => res.json())
            .then((d) => {
                return d;
            })
            .catch((error) => {
                console.error('Error:', error);
                throw error;
            });
    }

    async buscaDepartamentos() {
        return await fetch(`${API_URL}/colaboradores/departamentos`, {
            method: 'GET',
            headers: {
                Accept: 'application/json',
                Authorization: 'Bearer ' + token
            }
        })
            .then((res) => res.json())
            .then((d) => {
                return d;
            })
            .catch((error) => {
                console.error('Error:', error);
                throw error;
            });
    }

    async buscaIndicadores() {
        return await fetch(`${API_URL}/colaboradores/indicadores`, {
            method: 'GET',
            headers: {
                Accept: 'application/json',
                Authorization: 'Bearer ' + token
            }
        })
            .then((res) => res.json())
            .then((d) => {
                return d;
            })
            .catch((error) => {
                console.error('Error:', error);
                throw error;
            });
    }

    async cadastrarDepartamento(departamento) {
        return await fetch(`${API_URL}/colaboradores/cadastrar-departamento`, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                Authorization: 'Bearer ' + token
            },
            body: JSON.stringify({
                departamento: departamento
            })
        })
            .then((res) => res.json())
            .then((d) => {
                return d;
            })
            .catch((error) => {
                console.error('Error:', error);
                throw error;
            });
    }

    async deletarColaborador(id) {
        return await fetch(`${API_URL}/colaboradores/${id}`, {
            method: 'DELETE',
            headers: {
                Accept: 'application/json',
                Authorization: 'Bearer ' + token
            }
        })
            .then((res) => res.json())
            .then((d) => {
                return d;
            })
            .catch((error) => {
                console.error('Error:', error);
                throw error;
            });
    }

    async alterarStatusColaborador(id) {
        return await fetch(`${API_URL}/colaboradores/alterar-status/${id}`, {
            method: 'GET',
            headers: {
                Accept: 'application/json',
                Authorization: 'Bearer ' + token
            }
        })
            .then((res) => res.json())
            .then((d) => {
                return d;
            })
            .catch((error) => {
                console.error('Error:', error);
                throw error;
            });
    }
    async cadastraColaborador(colaborador) {
        const formData = new FormData();
        formData.append('nome', colaborador?.nome ?? null);
        formData.append('cpf', colaborador?.cpf ?? null);
        formData.append('email', colaborador?.email ?? null);
        formData.append('departamento_id', colaborador?.departamento.id ?? null);
        formData.append('dt_admissao', this.formatarData(colaborador?.dt_admissao) ?? null);
        formData.append('dt_nascimento', this.formatarData(colaborador?.dt_nascimento) ?? null);
        formData.append('cargo', colaborador?.cargo ?? null);
        formData.append('imagem', colaborador?.imagem ?? null);
        formData.append('matricula', colaborador?.matricula ?? null);

        return fetch(`${API_URL}/colaboradores/cadastrar`, {
            method: 'POST',
            body: formData
        })
            .then((res) => res.json())
            .then((d) => {
                return d;
            })
            .catch((error) => {
                console.error('Error:', error);
                throw error;
            });
    }

    formatarData(data) {
        if (!(data instanceof Date)) return '';

        const ano = data.getFullYear();
        const mes = String(data.getMonth() + 1).padStart(2, '0');
        const dia = String(data.getDate()).padStart(2, '0');

        return `${ano}-${mes}-${dia}`;
    }

    async editaColaborador(colaborador) {
        return fetch(`${API_URL}/colaboradores/editar/${colaborador?.id}`, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                name: colaborador?.nome ?? null,
                cpf: colaborador?.cpf ?? null,
                email: colaborador?.email ?? null,
                departamento_id: colaborador?.departamento.id ?? null,
                dt_admissao: colaborador?.dt_admissao ?? null,
                dt_nascimento: colaborador?.dt_nascimento ?? null,
                cargo: colaborador?.cargo ?? null
            })
        })
            .then((res) => res.json())
            .then((d) => {
                return d;
            })
            .catch((error) => {
                console.error('Error:', error);
                throw error;
            });
    }
}
