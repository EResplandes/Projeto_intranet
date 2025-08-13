import re
import json

def to_float(txt):
    """
    Converte uma string no formato monetário brasileiro (ex: 1.234,56) para float.
    Retorna None se a entrada for None.
    """
    if txt is None:
        return None
    return float(txt.replace(".", "").replace(",", ".").strip())

def extrair_blocos_linha(linha):
    """
    Captura blocos de informações em uma linha, seguindo o padrão
    <codigo> <descricao> <numero> [<valor>]
    
    A função retorna uma lista de dicionários, onde cada dicionário
    representa um bloco encontrado, com sua posição inicial na linha,
    código, descrição, horas/quantidade e valor.
    """
    # Expressão regular para capturar os blocos de informações.
    # Esta regex procura por um código (1 a 4 dígitos), uma descrição e um valor monetário,
    # com uma quantidade/horas opcional.
    pad = re.compile(
        r"(\d{1,4})\s+([A-Z0-9 .ÁÉÍÓÚÂÊÔÃÕÇ/%()§+-º\d]+?)\s+(?:(\d{1,3}(?:,\d{2})?)\s+)?(\d{1,3}(?:\.\d{3})*,\d{2})"
    )
    itens = []
    for m in pad.finditer(linha):
        codigo = m.group(1).strip()
        descricao = m.group(2).strip()
        
        # Ignora itens cuja descrição contenha um dois-pontos,
        # pois isso indica uma linha de resumo, não um item de folha.
        if ":" in descricao:
            continue
            
        horas_ou_qtd = to_float(m.group(3))
        valor = to_float(m.group(4))

        itens.append({
            "start": m.start(),
            "codigo": codigo,
            "descricao": descricao,
            "horas_ou_qtd": horas_ou_qtd,
            "valor": valor
        })
    return itens

# Padrões para identificar linhas de totalização ou rodapé que devem ser ignoradas.
STOP_PATTERNS = re.compile(
    r"^\s*(TOTALIZAÇÃO|Totalização|GPS -|IDENTIFICADOR|RIALMA|Folha de Pagamento|COMPETÊNCIA|_{3,}|SALÁRIO LÍQUIDO|BASE DO|TOTAL DE)",
    re.I
)

def parse_folha_de_pagamento_por_funcionario(conteudo_txt):
    """
    Função principal que analisa o conteúdo completo do arquivo de texto
    e extrai os dados de cada funcionário, incluindo proventos e descontos.
    """
    # Corta o conteúdo na palavra "Totalização" para evitar processar o rodapé.
    if "Totalização" in conteudo_txt:
        conteudo_txt = conteudo_txt.split("Totalização")[0]

    funcionarios = []

    # Encontra o início de cada bloco de funcionário.
    inicios = [m.start() for m in re.finditer(r"(?m)^\s*\d{6}[A-ZÁÉÍÓÚÂÊÔÃÕÇ]", conteudo_txt)]
    inicios.append(len(conteudo_txt))  # Adiciona um marcador para o final do último bloco.

    # O ponto de corte entre a coluna de proventos e a de descontos é fixo, em torno do caractere 90.
    # Isso é mais robusto do que tentar encontrar o cabeçalho "DESCONTOS", que pode variar.
    split_point = 90
            
    # Define descontos conhecidos por nome e código para uma categorização mais precisa.
    # Adicionamos palavras-chave e códigos mais específicos para evitar falsos positivos.
    KNOWN_DISCOUNTS = {"INSS - MENSAL", "IRRF - MENSAL", "PLANO DE SAÚDE", "EMPR CONSIGNADO", "VALE TRANSPORTE", "PENSÃO AL MENSAL"}
    KNOWN_DISCOUNT_CODES = {"1074", "1082", "603", "604", "605", "329", "271", "364"}

    for i in range(len(inicios) - 1):
        bloco = conteudo_txt[inicios[i]:inicios[i+1]].strip()
        linhas = [l.rstrip() for l in bloco.splitlines() if l.strip()]
        if not linhas:
            continue

        # Extrai matrícula e nome do funcionário da primeira linha do bloco.
        primeira = linhas[0]
        m_mat = re.match(r"^\s*(\d{6})", primeira)
        if not m_mat:
            continue
        matricula = m_mat.group(1)

        m_nome = re.match(r"^\s*\d{6}([A-ZÁÉÍÓÚÂÊÔÃÕÇ\s]+?)(?=\s+\d{1,4}\s|$)", primeira)
        if m_nome:
            nome = m_nome.group(1).strip()
        else:
            nome = primeira[6:].strip().split("  ")[0].strip()

        # Procura a função do funcionário nas próximas linhas.
        funcao = ""
        for l in linhas[1:6]:
            m_fun = re.search(r"^\s*([A-Z0-9 .ÁÉÍÓÚÂÊÔÃÕÇ/-]+?)\s*/\d{3,5}\b", l)
            if m_fun:
                funcao = m_fun.group(1).strip()
                break

        admissao = None
        salario_base = 0.0
        proventos = []
        descontos = []
        
        # Itera sobre todas as linhas do bloco para extrair dados.
        for l in linhas:
            # Pula linhas de totalização ou de rodapé.
            if STOP_PATTERNS.search(l):
                continue

            # Extrai data de admissão e salário base, se disponíveis.
            m_adm = re.search(r"ADMISS[ÃA]O EM\s+(\d{2}/\d{2}/\d{4})", l)
            if m_adm:
                admissao = m_adm.group(1)

            m_sb = re.search(r"SAL[ÁA]RIO BASE\s*:\s*([\d.,]+)", l)
            if m_sb:
                salario_base = to_float(m_sb.group(1))

            # Extrai todos os blocos de itens da linha e os separa em proventos e descontos.
            itens = extrair_blocos_linha(l)
            for item in itens:
                # Prioriza a categorização de descontos conhecidos por nome ou código exato.
                is_known_discount = any(
                    known_disc in item['descricao'].upper() for known_disc in KNOWN_DISCOUNTS
                ) or item['codigo'] in KNOWN_DISCOUNT_CODES
                
                if is_known_discount:
                    descontos.append({
                        "codigo": item["codigo"],
                        "descricao": item["descricao"],
                        "horas_ou_qtd": item["horas_ou_qtd"],
                        "valor": item["valor"]
                    })
                elif item["start"] < split_point:
                    proventos.append({
                        "codigo": item["codigo"],
                        "descricao": item["descricao"],
                        "horas_ou_qtd": item["horas_ou_qtd"],
                        "valor": item["valor"]
                    })
                else:
                    descontos.append({
                        "codigo": item["codigo"],
                        "descricao": item["descricao"],
                        "horas_ou_qtd": item["horas_ou_qtd"],
                        "valor": item["valor"]
                    })
        
        # Calcula os totais e o salário líquido
        total_proventos = sum(p['valor'] for p in proventos if p['valor'] is not None)
        total_descontos = sum(d['valor'] for d in descontos if d['valor'] is not None)
        salario_liquido = total_proventos - total_descontos

        # Adiciona os dados do funcionário à lista principal.
        funcionarios.append({
            "matricula": matricula,
            "nome": nome,
            "funcao": funcao,
            "admissao": admissao,
            "salario_base": salario_base,
            "proventos": proventos,
            "descontos": descontos,
            "total_proventos": total_proventos,
            "total_descontos": total_descontos,
            "salario_liquido": salario_liquido
        })

    return {"funcionarios": funcionarios}


if __name__ == "__main__":
    # Substitua "folha.txt" pelo caminho do seu arquivo de folha de pagamento.
    arquivo_txt = "folha_centrais.TXT"
    
    try:
        with open(arquivo_txt, "r", encoding="latin-1") as f:
            conteudo = f.read()
    except FileNotFoundError:
        print(f"Erro: O arquivo '{arquivo_txt}' não foi encontrado.")
        exit()
    
    # Chama a função principal com o conteúdo do arquivo
    resultado = parse_folha_de_pagamento_por_funcionario(conteudo)

    with open("folha_completa.json", "w", encoding="utf-8") as f:
        json.dump(resultado, f, ensure_ascii=False, indent=4)

    print("OK: folha_completa.json gerado com proventos, descontos e totais")
