<?php

namespace App\Services;

use App\Models\FolhasPagamento;
use App\Models\FuncionariosFolha;
use App\Models\ItensFolha;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class FolhasService
{

    public function importacaoFolha($request)
    {

        try {
            // 1º Passo -> Recuperar arquivo e salvar
            $folha = $request->file('folha');

            // Caminho dentro do storage/app/private/folhas
            $diretorio = 'folhas';
            $nomeArquivo = 'folha_centrais.txt';

            // Salvar com nome fixo
            $path = $folha->storeAs($diretorio, $nomeArquivo);

            // Caminho absoluto do arquivo salvo
            $fullPath = storage_path('app/private/' . $path);

            // 2º Passo -> Executar Script em Python
            $pythonScript = base_path('app/PythonScripts/importador.py');
            $process = new Process(['py', $pythonScript, $fullPath, env('FOLHA_CENTRAIS_PATH')]);
            $process->run();

            $jsonOutput = $process->getOutput();

            // Caso dê erro
            if (!$process->isSuccessful()) {
                dd('teste');
                throw new ProcessFailedException($process);
            }

            $folha = FolhasPagamento::create([
                'mes' => $request->mes,
                'ano' => $request->ano,
                'arquivo_original' => env('FOLHA_CENTRAIS_PATH'),
                'dt_importacao' => Carbon::now()
            ]);

            // 3º Passo -> Fazer importação do JSON
            $processaFolha = $this->processaJsonFolha($folha->id);
        } catch (\Throwable $th) {
            return [
                'http_code' => 500,
                'status' => false,
                'erro' => $th->getMessage(),
            ];
        }
    }

    public function processaJsonFolha($idFolha)
    {
        $caminho = env('FOLHA_CENTRAIS_PATH');

        if (file_exists($caminho)) {
            $conteudo = file_get_contents($caminho);
            $dados = json_decode($conteudo, true);

            $dadosFuncionarios = $dados['funcionarios'];

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
                }
            }
        } else {
            return false;
        }

        return [
            'http_code' => 200,
            'status' => true
        ];
    }

    public function insereDadosFolha($funcionarioIdFolha, $dados)
    {
        $dadosProventos = $dados['proventos'] ?? [];
        $dadosDescontos = $dados['descontos'] ?? [];


        if (!empty($dadosProventos)) {
            foreach ($dadosProventos as $provento) {
                ItensFolha::create([
                    'funcionario_folha_id' => $funcionarioIdFolha,
                    'descricao' => $provento['descricao'] ?? 'Erro',
                    'tipo' => 'Provento',
                    'valor' => $provento['valor'] ?? 0,
                    'horas_qtd' => $provento['horas_ou_qtd'] ?? 0
                ]);
            }
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
}
