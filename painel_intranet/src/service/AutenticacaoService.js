const token = localStorage.getItem('token');

import API_URL from './Config.js';

export default class AutenticacaoService {
    async logar(data) {
        return await fetch(`${API_URL}/autenticacao/login`, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                Authorization: 'Bearer ' + token
            },
            body: JSON.stringify(data)
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
