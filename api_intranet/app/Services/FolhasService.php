<?php

namespace App\Services;

use App\Http\Resources\ColaboradorFolhasResource;
use App\Models\FolhasPagamento;
use App\Models\FuncionariosFolha;
use App\Models\ItensFolha;
use App\Models\User;
use Carbon\Carbon;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\Mail;
use App\Mail\NovaFolhaDisponivel;
use Illuminate\Support\Facades\Http;

class FolhasService
{
    public function importacaoFolha($request)
    {
        try {
            // 1º Passo -> Recuperar arquivo e salvar
            $folha = $request->file('folha');

            $diretorio = 'folhas';
            $nomeArquivo = 'folha_centrais.txt';

            // Salvar no storage/app/private/folhas/folha_centrais.txt
            $path = $folha->storeAs($diretorio, $nomeArquivo);
            $fullPath = storage_path('app/private/' . $path);

            // 2º Passo -> Executar Script em Python
            $pythonScript = base_path('app/PythonScripts/importador.py');
            $process = new Process(['py', $pythonScript, $fullPath, env('FOLHA_CENTRAIS_PATH')]);
            $process->run();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            // Cria registro da folha
            $folhaModel = FolhasPagamento::create([
                'mes' => $request->mes,
                'ano' => $request->ano,
                'arquivo_original' => env('FOLHA_CENTRAIS_PATH'),
                'dt_importacao' => Carbon::now()
            ]);

            // 3º Passo -> Processa JSON gerado pelo Python
            $resultado = $this->processaJsonFolha($folhaModel->id);

            return [
                'http_code' => $resultado['http_code'] ?? 500,
                'status' => $resultado['status'] ?? false,
                'erro' => $resultado['erro'] ?? null,
                'avisos' => $resultado['avisos'] ?? null
            ];
        } catch (\Throwable $th) {
            return [
                'http_code' => 500,
                'status' => false,
                'erro' => $th->getMessage()
            ];
        }
    }

    public function processaJsonFolha($idFolha)
    {
        $caminho = env('FOLHA_CENTRAIS_PATH');

        if (!file_exists($caminho)) {
            return [
                'http_code' => 404,
                'status' => false,
                'erro' => "Arquivo JSON não encontrado em {$caminho}"
            ];
        }

        $conteudo = file_get_contents($caminho);
        $dados = json_decode($conteudo, true);

        if (!$dados || !isset($dados['funcionarios'])) {
            return [
                'http_code' => 400,
                'status' => false,
                'erro' => "JSON inválido ou sem funcionários"
            ];
        }

        $dadosFuncionarios = $dados['funcionarios'];
        $avisos = [];

        foreach ($dadosFuncionarios as $dadosFuncionario) {
            $idUsuario = User::where('matricula', $dadosFuncionario['matricula'])->pluck('id')->first();

            if ($idUsuario != null) {
                $funcionarioIdFolha = FuncionariosFolha::create([
                    'funcionario_id' => $idUsuario,
                    'folha_id' => $idFolha,
                    'total_proventos' => $dadosFuncionario['total_proventos'],
                    'total_descontos' => $dadosFuncionario['total_descontos'],
                    'total_liquido' => $dadosFuncionario['salario_liquido'],
                    'salario_base' => $dadosFuncionario['salario_base'],
                    'nome' => $dadosFuncionario['nome']
                ]);

                $this->insereDadosFolha($funcionarioIdFolha->id, $dadosFuncionario);
            } else {
                $avisos[] = "Usuário não encontrado para matrícula {$dadosFuncionario['matricula']}";
            }
        }

        return [
            'http_code' => 200,
            'status' => true,
            'avisos' => $avisos
        ];
    }

    public function insereDadosFolha($funcionarioIdFolha, $dados)
    {
        $dadosProventos = $dados['proventos'] ?? [];
        $dadosDescontos = $dados['descontos'] ?? [];

        foreach ($dadosProventos as $provento) {
            ItensFolha::create([
                'funcionario_folha_id' => $funcionarioIdFolha,
                'descricao' => $provento['descricao'] ?? 'Erro',
                'tipo' => 'Provento',
                'valor' => $provento['valor'] ?? 0,
                'horas_qtd' => $provento['horas_ou_qtd'] ?? 0
            ]);
        }

        foreach ($dadosDescontos as $desconto) {
            ItensFolha::create([
                'funcionario_folha_id' => $funcionarioIdFolha,
                'descricao' => $desconto['descricao'] ?? 'Erro',
                'tipo' => 'Desconto',
                'valor' => $desconto['valor'] ?? 0,
                'horas_qtd' => $desconto['horas_ou_qtd'] ?? 0
            ]);
        }

        return [
            'proventos' => count($dadosProventos),
            'descontos' => count($dadosDescontos)
        ];
    }

    public function buscaFolhasColaborador($idColaborador)
    {
        try {
            $folhas = ColaboradorFolhasResource::collection(
                FuncionariosFolha::where('funcionario_id', $idColaborador)
                    ->get()
            );

            return [
                'http_code' => 200,
                'status' => true,
                'folhas' => $folhas,
            ];
        } catch (\Throwable $th) {
            return [
                'http_code' => 500,
                'status' => false,
                'erro' => $th->getMessage()
            ];
        }
    }

    public function validacaoFolha($idFolha)
    {
        try {
            $folha = FuncionariosFolha::find($idFolha);
            if (!$folha) {
                return [
                    'http_code' => 404,
                    'status' => false,
                    'erro' => 'Folha não encontrada'
                ];
            }

            $folha->liberado = true;
            $folha->save();

            $usuario = User::find($folha->funcionario_id);

            if ($usuario->name && $usuario->email) {
                Mail::to($usuario->email)
                    ->send(new NovaFolhaDisponivel($folha));
            }

            return [
                'http_code' => 200,
                'status' => true,
                'folha' => $folha,
            ];
        } catch (\Throwable $th) {
            return [
                'http_code' => 500,
                'status' => false,
                'erro' => $th->getMessage()
            ];
        }
    }

    public function painelColaborar($idColaborador)
    {
        try {
            $folhas = ColaboradorFolhasResource::collection(
                FuncionariosFolha::where('funcionario_id', $idColaborador)
                    ->where('liberado', 1)
                    ->get()
            );

            $historicoSalario = FuncionariosFolha::where('funcionario_id', $idColaborador)
                ->orderBy('created_at', 'desc')
                ->get();

            $salarioBase = FuncionariosFolha::where('funcionario_id', $idColaborador)
                ->orderBy('created_at', 'desc')
                ->first();

            return [
                'http_code' => 200,
                'status' => true,
                'folhas' => $folhas,
                'historicoSalario' => $historicoSalario,
                'salarioBase' => $salarioBase
            ];
        } catch (\Throwable $th) {
            return [
                'http_code' => 500,
                'status' => false,
                'erro' => $th->getMessage()
            ];
        }
    }


    public function secullum($cpf)
    {

        try {
            $url = 'https://autenticador.secullum.com.br/Token?grant_type=password&username=' . env('USUARIO_SECULLUM') . '&password=' . env('PASSWORD_SECULLUM') . '&client_id=3';

            $tokenSecullum = Http::post($url);

            $token = $tokenSecullum->json()['access_token'];

            $dataFinal = Carbon::now()->format('Y-m-d');
            $dataInicial = Carbon::now()->subMonth()->format('Y-m-d');
            $payload = [
                "FuncionarioCpf" => $cpf,
                "DataInicial" => $dataInicial,
                "DataFinal" => $dataFinal,
            ];


            $folhaPonto = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'secullumidbancoselecionado' => env('ID_BANCO_SECULLUM'),
            ])
                ->post("https://pontowebintegracaoexterna.secullum.com.br/IntegracaoExterna/Calcular", $payload);

            return [
                'erro' => null,
                'ponto' => $folhaPonto->json(),
                'http_code' => 200,
                'status' => true,
            ];
        } catch (\Throwable $th) {
            return [
                'http_code' => 500,
                'status' => false,
                'erro' => $th->getMessage()
            ];
        }
    }
}
