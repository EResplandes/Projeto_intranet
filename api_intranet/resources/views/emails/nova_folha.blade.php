<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Novo Contracheque - RH Connect</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f5f6fa;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 2rem auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            padding: 2rem;
        }

        .header {
            background-color: #4e73df;
            color: #fff;
            padding: 1rem;
            border-radius: 8px 8px 0 0;
            text-align: center;
            font-size: 1.25rem;
            font-weight: bold;
        }

        .content {
            margin-top: 1rem;
            line-height: 1.6;
        }

        .footer {
            margin-top: 2rem;
            font-size: 0.85rem;
            color: #777;
            text-align: center;
        }

        .highlight {
            font-weight: bold;
            color: #4e73df;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            RH Connect
        </div>

        <div class="content">
            <p>Olá <span class="highlight">{{ $folha->nome }}</span>,</p>

            <p>Um novo contracheque referente a <span class="highlight">{{ $folha->folha->mes }}/{{ $folha->folha->ano }}</span> está disponível na plataforma.</p>

            <!-- <p>Salário Líquido: <span class="highlight">R$ ???</span></p> -->

            <p>Você pode acessar sua folha diretamente pelo <strong>RH Connect</strong> para mais detalhes.</p>

            <p>Atenciosamente,<br>
                Equipe RH Connect</p>
        </div>

        <div class="footer">
            Este é um e-mail automático. Por favor, não responda a esta mensagem.
        </div>
    </div>
</body>

</html>